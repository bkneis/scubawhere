<?php

namespace Scubawhere\Services;

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Repositories\AgentRepoInterface;
use Scubawhere\Repositories\BookingRepoInterface;
use Scubawhere\Exceptions\HTTPForbiddenException;
use Scubawhere\Repositories\PaymentRepoInterface;

class BookingStatusService 
{

	/**
	 * Repository to perform CRUD operations on Bookings
	 *
	 * @var \Scubawhere\Repositories\BookingRepo
	 */
	protected $booking_repo;

	/**
	 * Repository to perform CRUD operations on Payments
	 *
	 * @var \Scubawhere\Repositories\PaymentRepo
	 */
	protected $payment_repo;

	/**
	 * Repository to perform CRUD operations on Agents
	 *
	 * @var \Scubawhere\Repositories\AgentRepo
	 */
	protected $agent_repo;

	public function __construct(BookingRepoInterface $booking_repo,
								PaymentRepoInterface $payment_repo,
								AgentRepoInterface $agent_repo)
	{
		$this->booking_repo = $booking_repo;
		$this->payment_repo = $payment_repo;
		$this->agent_repo   = $agent_repo;
	}

	public function initialise($data)
	{
		if ($data['agent_id']) {
			$agent = $this->agent_repo->get($data['agent_id']);
            // If a valid agent_id is supplied, discard source
            $data['source'] = null;
		} else {
            $agent = null;
        }

        $data['price'] = 0;

        // Reserve booking for 15 min by default
        $data['reserved_until'] = Helper::localTime()->add(new \DateInterval('PT15M'))->format('Y-m-d H:i:s');
        $data['status'] = 'initialised';

		return array(
			'booking' => $this->booking_repo->create($data),
			'agent'   => $agent
		);
	}

	// @todo this should have more validation
	public function reserve($id, $reserved_until, $email)
	{
        if (is_null($reserved_until)) {
			throw new InvalidInputException(['Please specify the amount of hours to reserve the booking for.']);
        }

		$booking = $this->booking_repo->get($id, null);

        if (in_array($booking->status, array('confirmed', 'on hold', 'cancelled'))) {
			throw new HTTPForbiddenException(['The booking cannot be reserved, as it is ' . $booking->status . '.']);
        }

        $data = ['reserved_until' => abs($reserved_until)];

        $local_now = Helper::localTime();
        $data['reserved_until'] = $local_now->add(new \DateInterval('PT'.$data['reserved_until'].'H'))->format('Y-m-d H:i:s');

        $data['status'] = 'reserved';

        if (!$booking->update($data)) {
			throw new InvalidInputException($booking->errors()->all());
        }

		if((int) $email) {
			CrmMailer::sendReservationConf($booking->id);
		}

		return $booking;
	}

	public function save($id)
	{
		if(is_null($id)) {
			throw new InvalidInputException(['Please provide a booking ID']);
		}

		$booking = $this->booking_repo->get($id, null);

        if (in_array($booking->status, array('reserved', 'expired', 'confirmed', 'on hold', 'cancelled'))) {
			throw new HTTPForbiddenException(['The booking cannot be saved, as it is ' . $booking->status . '.']);
        }

        if (!$booking->update(array('status' => 'saved', 'reserved' => null))) {
			throw new InvalidInputException($booking->errors()->all());
		}

		return $booking;
	}

	public function cancel($id)
	{
		$booking = $this->booking_repo->get($id, null);

        // Bookings that have not been reserved, confirmed, cancelled or are on hold can be safely deleted
        if ($booking->status === null || in_array($booking->status, ['saved', 'initialised', 'expired', 'temporary'])) {
            $booking->delete();
            return;
        }

        if ($booking->status === 'cancelled') {
            return Response::json(array('errors' => array('The booking is already cancelled.')), 403);
        } // 403 Forbidden

        if ($this->moreThan5DaysAgo($booking->last_return_date)) {
            return Response::json(array('errors' => array('The booking can not be cancelled anymore because it ended more than 5 days ago.')), 403); // 403 Forbidden
        }

        if (!$booking->update(array('status' => 'cancelled', 'reserved' => null, 'cancellation_fee' => Input::get('cancellation_fee')))) {
            return Response::json(array('errors' => $booking->errors()->all()), 406); // 406 Not Acceptable
        }
	}

	public function confirm($id)
	{
		$booking = $this->booking_repo->get($id, null);

        if ($booking->price != 0 && $booking->agent_id === null) {
			throw new HTTPForbiddenException(['The confirmation method is only allowed for bookings by a travel agent or free-of-charge bookings.']);
        }

        if ($booking->status === 'cancelled') {
			throw new ConflictException('The booking cannot be confirmed, as it is cancelled.');
        }

        if (Helper::isPast($booking->arrival_date)) {
			throw new HTTPForbiddenException('Cannot confirm booking because it already started.');
        }

        if (!$booking->update([
            'status' => 'confirmed',
            'reserved' => null,
        ])) {
			throw new InvalidInputException($booking->errors()->all());
        }

		$payment = null;

		if($booking->agent_id !== null) {
			$agent = $booking->agent()->first(); // @note, should I use lazy loading, i.e. $booking->load('agent') here, any benefits?
			if($agent->terms === 'deposit') {
				$data = [];
				$data['currency_id'] = Context::get()->currency->id;
				$data['amount'] = round($booking->decimal_price * ($agent->commission / 100), 2);
				$data['paymentgateway_id'] = Paymentgateway::where('name', 'Agent Deposit')->pluck('id');
				$data['received_at'] = Helper::localtime()->format('Y-m-d');

				$payment = $this->payment_repo->create($data);
			}
		}

        CrmMailer::sendBookingConf($booking->id);
		return $payment;
	}

    private function moreThan5DaysAgo($date)
    {
        $local_time = Helper::localTime();
        $test_date = new DateTime($date, new DateTimeZone(Context::get()->timezone));

        if ($local_time->diff($test_date)->format('%R%a') < -5) {
            return true;
        }

        return false;
    }

}
