<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Booking;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Exceptions\Http\HttpNotFound;

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
class BookingRepo /*extends BaseRepo*/ implements BookingRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
	 *
     * @var \ScubaWhere\Entities\Company
     */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all bookings for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function all($from, $take, array $relations = [])
	{
		return Booking::onlyOwners()
			->with($relations)
			->orderBy('id', 'DESC')
			->skip($from)
			->take($take)
			->get();

        /*return Booking::onlyOwners()->with(
			'agent',
			'lead_customer',
				'lead_customer.country',
			'payments',
				'payments.paymentgateway',
			'refunds',
				'refunds.paymentgateway'
		)
		->orderBy('id', 'DESC')
		->skip($from)
		->take($take)
		->get();*/
    }

    /**
     * Get all bookings for a company including soft deleted models
	 *
	 * @param int   $from Number of models to skip
	 * @param int   $take Number of models to retrieve
	 * @param array $relations
	 *
     * @return \Illuminate\Database\Eloquent\Collection 
     */
	public function allWithTrashed($from, $take, array $relations = [])
	{
        return Booking::onlyOwners()
			->with($relations)
			->withTrashed()
			->orderBy('id', 'DESC')
			->skip($from)
			->take($take)
			->get();
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
        return Booking::onlyOwners()
            ->with($relations)
			->filterByReference($data['reference'])
			->filterDate($data['date'])
			->filterLeadCustomerByLastName($data['lastname'])
            ->orderBy('id', 'DESC')
            ->skip($from)
            ->take($take)
            ->get();
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
	 * Get a booking by ID of by ref
	 *
	 * @param int    $id  ID of the booking
	 * @param string $ref Reference of the booking
	 * @param array  $relations
	 * @param bool   $fail
	 *
	 * @throws \Scubawhere\Exceptions\Http\HttpNotFound
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function get($id, $ref, array $relations = [], $fail = true)
	{
		$booking = Booking::onlyOwners()->with($relations)->fetch($id, $ref);

		if(is_null($booking) && $fail) {
			throw new HttpNotFound(__CLASS__ . __METHOD__, ['The booking could not be found']);
		}

		return $booking;
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

    /**
     * Get an booking for a company by a specified column and value
	 *
     * @param array $query Query used to find the model
	 * @param array $relations
	 * @param bool  $fail
	 *
	 * @throws HttpNotFound
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getWhere(array $query, array $relations = [], $fail = true)
	{
		$booking = Booking::onlyOwners()->where($query)->with($relations)->get();

		if(is_null($booking) && $fail) {
			throw new HttpNotFound(__CLASS__ . __METHOD__, ['The booking could not be found']);
		}

		return $booking;
	}

    /**
     * Create an booking and associate it with its company
	 *
     * @param array $data Information about the booking to save
	 *
     * @throws \Scubawhere\Exceptions\InvalidInputException
	 *
     * @return \ScubaWhere\Entities\Booking
     */
	public function create($data) 
	{
		$booking = new Booking($data);
		$booking->reference = Helper::booking_reference_number();

        if (!$booking->validate()) {
            throw new InvalidInputException($booking->errors()->all());
        }

        return $this->company_model->bookings()->save($booking);
    }

    /**
     * Update an booking by id with specified data
	 *
     * @param  int   $id   ID of the booking
     * @param  array $data Data to update the booking with
	 *
     * @throws \Scubawhere\Exceptions\InvalidInputException
	 *
     * @return \ScubaWhere\Entities\Booking
     */
	public function update($id, $data) 
	{
        $booking = $this->get($id, null);
        if(!$booking->update($data)) {
            throw new InvalidInputException($booking->errors()->all());
        }
        return $booking;
    }

}

