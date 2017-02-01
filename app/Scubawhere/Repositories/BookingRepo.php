<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Exceptions;
use Scubawhere\Entities\Booking;
use Scubawhere\Contracts\BookingRepoInterface;

/**
 * Class BookingRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\BoatRepoInterface
 */
class BookingRepo extends EloquentRepo implements BookingRepoInterface {

    public function __construct() {
        parent::__construct(Booking::class);
    }

	/**
	 * Get all bookings for a company that match a filter
	 *
	 * @param array $data Filter parameters
	 * @param int   $from Number of models to skip
	 * @param int   $take Number of models to retrieve
	 * @param array $relations
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function filter($data, $from, $take, array $relations = [])
	{
        return Booking::with($relations)
			->filterByReference($data['reference'])
			->filterDate($data['date'])
			->filterLeadCustomerByLastName($data['lastname'])
            ->orderBy('id', 'DESC')
            ->skip($from)
            ->take($take)
            ->get();
	}

	public function onlySurcharged()
	{
		return Booking::with(array('lead_customer',
			'payments' => function ($q) {
				//$q->select('amount', 'card_ref', 'surcharge');
				// @todo Find out why nothing is returned selecting columns
				$q->where('surcharge', '>', 0);
			},
			'refunds' => function ($q) {
				$q->where('surcharge', '>', 0);
			}))
			->whereHas('payments', function ($q) {
				$q->whereNotNull('surcharge');
			})
			->orWhereHas('refunds', function ($q) {
				$q->whereNotNull('surcharge');
			})
			->get(['id', 'reference', 'price', 'status', 'lead_customer_id']);
	}

	/**
	 * Get all bookings related to a customer
	 *
	 * @param int $id   ID of the customer
	 * @param int $from Number of models to skip
	 * @param int $take Number of models to retrieve
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function customerBookings($id, $from, $take)
	{
		return Booking::onlyOwners()
			->filterLeadCustomerByID($id)
            ->orderBy('id', 'DESC')
            ->skip($from)
            ->take($take)
            ->get();
	}

    /**
     * Get an booking for a company from its id
	 *
     * @param int $id ID of the booking
	 *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
	 *
     * @return \ScubaWhere\Entities\Booking
     */
	public function getWithTrimmings($id, $ref)
	{
		return Booking::onlyOwners()->with(
			'agent',
			'lead_customer',
				'lead_customer.country',
			'bookingdetails',
				'bookingdetails.customer',
					'bookingdetails.customer.country',
				'bookingdetails.session',
					'bookingdetails.session.trip',
				'bookingdetails.ticket',
				'bookingdetails.packagefacade',
					'bookingdetails.packagefacade.package',
				'bookingdetails.course',
				'bookingdetails.training',
				'bookingdetails.training_session',
				'bookingdetails.addons',
			'accommodations',
			'payments',
				'payments.paymentgateway',
			'refunds',
				'refunds.paymentgateway',
			'pick_ups'
		)
		->fetch($id, $ref);
    }

}

