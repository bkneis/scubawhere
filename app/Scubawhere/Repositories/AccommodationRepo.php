<?php

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;
use Scubawhere\Helper;
use Scubawhere\Entities\Accommodation;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Illuminate\Database\Eloquent\Collection;
use Scubawhere\Exceptions\InvalidInputException;

/**
 * Class AccommodationRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\AccommodationRepoInterface
 */
class AccommodationRepo extends BaseRepo implements AccommodationRepoInterface {

    /**
     * Eloquent model that acts as the root model to associate assets to
     *
     * @var \Scubawhere\Entities\Company
    */
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all accommodations for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return Accommodation::with($relations)->get();
    }

    /**
     * Get all accommodations for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) {
        return Accommodation::with($relations)->withTrashed()->get();
    }

    /**
     * Get an accommodation for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Scubawhere\Entities\Accommodation
     */
    public function get($id, array $relations = [], $fail = true) {
        $accommodation = Accommodation::with($relations)->find($id);

        if($accommodation === null && $fail) {
            throw new HttpNotFound(__CLASS__ . __FUNCTION__, ['The accommodation could not be found']);
        }

        return $accommodation;
    }

    /**
     * Get an accommodation for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Scubawhere\Entities\Accommodation
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $accommodation = Accommodation::where(function ($q) use ($query) {
            foreach ($query as $obj) {
                $q->where($obj[0], $obj[1], $obj[2]);
            }
        })->with($relations)->get();

        if($accommodation === null && $fail) {
            throw new HttpNotFound(__CLASS__ . __FUNCTION__, ['The accommodation could not be found']);
        }

        return $accommodation;
    }

    /**
     * Get an accommodation with all bookings that are scheduled for the future
     *
     * @param int  $id
     * @param bool $fail
     *
     * @throws HttpNotFound
     *
     * @return \Scubawhere\Entities\Accommodation
     */
    public function getUsedInFutureBookings($id, $fail = true)
    {
        $accommodation = Accommodation::with(['bookings' => function($q) {
                $q->where('accommodation_booking.start', '>=', Helper::localtime());
            }])
            ->find($id);

        if(is_null($accommodation) && $fail) {
            throw new HttpNotFound(__CLASS__.__METHOD__, ['The accommodation could not be found']);
        }

        return $accommodation;
    }

    /**
     * Get all the bookings for an accommodation for a specific date.
     *
     * @note Ok, so this function got extremely annoying. Basically I wanted to get the sum of the refunds with the rest of the data
     * in the first DB call. But as their is no unique id in the accommodation_booking table, when summing the amount of payments, it
     * would sum them twice, once when joining the payments, then again when getting the refunds.
     *
     * @ref http://stackoverflow.com/questions/7699269/mysql-two-left-joins-double-counting
     *
     * @todo Find a way to get the sum of the refunds in the first database call, as at the moment it is looping through each booking for each refund :/
     *
     * @param string $after
     * @param string $before
     * @param int    $id
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBookings($id, $after, $before)
    {
        $data = \DB::table('accommodation_booking')
            ->select(\DB::raw('accommodations.id as accommodation_id'), 'accommodations.name', 'accommodation_booking.booking_id', 'customer_id', 'bookings.*', 'customers.*', \DB::raw('SUM(payments.amount) as paid'))
            ->join('bookings', 'bookings.id', '=', 'accommodation_booking.booking_id')
            ->join('customers', 'customers.id', '=', 'customer_id')
            ->join('payments', 'payments.booking_id', '=', 'accommodation_booking.booking_id')
            ->join('accommodations', 'accommodations.id', '=', 'accommodation_booking.accommodation_id')
            ->where('accommodation_id', $id)
            ->where('start', '<=', $after)
            ->where('end', '>=', $before)
            ->get();

            $refunds = \DB::table('refunds')
            ->selectRaw('booking_id, SUM(amount) as refunded')
            ->whereIn('booking_id', array_map(function ($obj) { return $obj->booking_id; }, $data))
            ->get();

            foreach ($refunds as $obj) {
                foreach ($data as $key => $val) {
                    if($val->booking_id == $obj->booking_id) {
                        $data[$key]->refunded = $obj->refunded;
                    }
                }
            }

            return $data;
      }

    /**
     * Get the availability of the accommodations and their booking information
     *
     * @todo Replace the function above with this
     *
     * @param array $dates
     * @param null $id
     * @return Collection
     */
    public function getAvailability(array $dates, $id = null)
    {
        return Accommodation::where(function ($q) use ($id) {
                if(!is_null($id)) {
                    $q->where('id', '=', $id);
                }
            })
            ->with(['bookings' => function ($q) use ($dates) {
                $q->where('start', '<=', $dates['before'])
                    ->where('end', '>=', $dates['after']);
            }, 'bookings.payments', 'bookings.refunds', 'bookings.lead_customer'])
            ->get();
    }

    /**
     * Create an accommodation and associate it with its company
     *
     * @param array $data
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \Scubawhere\Entities\Accommodation
     */
    public function create(array $data)
    {
        $accommodation = new Accommodation($data);

        if (!$accommodation->validate()) {
            throw new InvalidInputException($accommodation->errors()->all());
        }

        return $this->company_model->accommodations()->save($accommodation);
    }

    public function update($id, array $data)
    {
        $accommodation = $this->get($id);
        if(!$accommodation->update($data)) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $accommodation->errors()->all());
        }
        return $accommodation;
    }

}
