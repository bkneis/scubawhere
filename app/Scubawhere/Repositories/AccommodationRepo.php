<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Helper;
use Scubawhere\Entities\Accommodation;
use Scubawhere\Exceptions\Http\HttpNotFound;
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
        return Accommodation::onlyOwners()->with($relations)->get();
    }

    /**
     * Get all accommodations for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) {
        return Accommodation::onlyOwners()->with($relations)->withTrashed()->get();
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
        $accommodation = Accommodation::onlyOwners()->with($relations)->find($id);

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
        $accommodation = Accommodation::onlyOwners()->where(function ($q) use ($query) {
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
        $accommodation = Accommodation::onlyOwners()
            ->with(['bookings' => function($q) {
                $q->where('accommodation_booking.start', '>=', Helper::localtime());
            }])
            ->find($id);

        if(is_null($accommodation) && $fail) {
            throw new HttpNotFound(__CLASS__.__METHOD__, ['The accommodation could not be found']);
        }

        return $accommodation;
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
    public function create(array $data) {
        $accommodation = new Accommodation($data);

        if (!$accommodation->validate()) {
            throw new InvalidInputException($accommodation->errors()->all());
        }

        return $this->company_model->accommodations()->save($accommodation);
    }

}
