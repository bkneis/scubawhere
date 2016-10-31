<?php

namespace Scubawhere\Strategies;

use Scubawhere\Context;
use Scubawhere\Entities\Booking;

class FilterBookingReportGenerator implements ReportGeneratorInterface
{
	protected $type;

	public function __construct($type)
	{
		$this->type = $type;
	}

	protected function getBookings($type, $dates)
	{
		return Booking::onlyOwners()
            ->with(
                'lead_customer',
                'payments',
                    'payments.paymentgateway',
                'refunds',
                    'refunds.paymentgateway'
            )
            ->whereIn('status', ['confirmed'])
			->where(function ($q) use ($type) {
				if($type === 'confirmed_direct') {
					$q->whereNull('agent_id');
				} elseif ($type === 'confirmed_agent') {
					$q->whereNotNull('agent_id');
				}
			})
            ->whereBetween('created_at', [$dates['after'], $dates['before']])
            ->orderBy('id')
            ->get();
	}

	protected function convertDates($before, $after)
	{
        if (is_null($after) || is_null($before)) {
			throw new BadRequestException(['Both the "after" and the "before" parameters are required.']);
        }

        $after_utc = new \DateTime($after,  new \DateTimeZone(Context::get()->timezone));
        $after_utc->setTimezone(new \DateTimeZone('UTC'));
        $before_utc = new \DateTime($before, new \DateTimeZone(Context::get()->timezone));
        $before_utc->setTimezone(new \DateTimeZone('UTC'));
        $before_utc->add(new \DateInterval('P1D'));
		
		return array(
			'after'  => $after_utc,
			'before' => $before_utc
		);
	}

	protected function calculateAgentTotals($bookings)
	{
		$TOTALS = [
			'revenue' => 0,
			'commission' => 0,
			'invoicable' => 0,
		];

		foreach ($bookings as $booking) {
			$TOTALS['commission'] += round($booking->real_decimal_price * ($booking->agent->commission / 100), 2);

			$TOTALS['revenue'] += $booking->decimal_price - round($booking->real_decimal_price * ($booking->agent->commission / 100), 2);

			if ($booking->agent->terms === 'fullamount') {
				$TOTALS['invoicable'] += $booking->decimal_price - round($booking->real_decimal_price * ($booking->agent->commission / 100), 2);
			}
		}

		return $TOTALS;
	}

	protected function calculateTotals($bookings)
	{
		$TOTALS = [
			'revenue' => 0,
		];

		foreach ($bookings as $booking) {
			if (empty($booking->source)) {
				// By agent
				$TOTALS['revenue'] += $booking->decimal_price - round($booking->real_decimal_price * ($booking->agent->commission / 100), 2);
			} else {
				// Direct
				$TOTALS['revenue'] += $booking->decimal_price;
			}
		}

		return $TOTALS;
	}

	public function createReport($before, $after)
	{
		$dates = $this->convertDates($before, $after);
		$bookings = $this->getBookings($this->type, $dates);

		if($this->type === 'direct_agent') {
			$totals = $this->calculateAgentTotals($bookings);
		} else {
			$totals = $this->calculateTotals($bookings);
		}

		return array(
			'bookings' => $bookings,
			'totals'   => $totals
		);
	}

}

