<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Entities\Bookingdetail;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;
use ScubaWhere\Exceptions\MethodNotSupportedException;

/**
 * Class BookingDetailRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @todo rename this BookingdetailRepo as the model has no captial D
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\BoatRepoInterface
 */
class BookingDetailRepo extends BaseRepo implements BookingDetailRepoInterface {

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
	 * @param array $relations
	 *
     * @throws \ScubaWhere\Exceptions\MethodNotSupportedException
	 */
	public function all(array $relations = [])
	{
		throw new MethodNotSupportedException(['Error']);
    }

    /**
	 * @param array $relations
	 *
     * @throws \ScubaWhere\Exceptions\MethodNotSupportedException
	 */
	public function allWithTrashed(array $relations = [])
	{
		throw new MethodNotSupportedException(['Error']);
    }

    /**
     * Get an booking for a company from its id
	 *
     * @param int   $id
	 * @param array $relations
	 * @param bool  $fail
	 *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
	 *
     * @return \ScubaWhere\Entities\Booking
     */
	public function get($id, array $relations = [], $fail = true)
	{
		$bookingdetail = Bookingdetail::with($relations)->find($id);

		if(is_null($bookingdetail) && $fail) {
			throw new HttpNotFound(__CLASS__ . __METHOD__, ['The bookingdetail could not be found']);
		}

		return $bookingdetail;
    }

    /**
     * Get an booking for a company by a specified column and value
	 *
     * @param array $query
	 * @param array $relations
	 * @param bool  $fail
	 *
	 * @throws \Scubawhere\Exceptions\Http\HttpNotFound
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
		$bookingdetail = Bookingdetail::where($query)->with($relations)->find();

		if(is_null($bookingdetail) && $fail) {
			throw new HttpNotFound(__CLASS__ . __METHOD__, ['The bookingdetail could not be found']);
		}

		return $bookingdetail;
    }

    /**
     * Create an booking and associate it with its company
	 *
     * @param array $data      Information about the booking to save
	 * @param bool  $temporary Set if the booking is being edited
	 * @param \Scubawhere\Entities\Training $training
	 *
	 * @todo Why is training being passed here? It should be added to the data, is it mass assignable?
	 *
     * @throws \Scubawhere\Exceptions\InvalidInputException
	 *
     * @return \Scubawhere\Entities\Booking
     */
	public function create($data, $temporary, $training)
	{
		$bookingdetail = new Bookingdetail($data);

		if(!is_null($temporary)) {
			$bookingdetail->temporary = true;
		}
		if($training) {
			$bookingdetail->training_id = $training->id;
		}
		if(!$bookingdetail->validate()) {
			throw new InvalidInputException(['errors' => $bookingdetail->errors()->all()]);
		}

		return $bookingdetail;
    }

	/**
	 * Update a bookingdetail
	 *
	 * @param int   $id   ID of the addon
	 * @param array $data Information about the addon to update
	 * @param bool  $fail Whether to fail or not
	 *
	 * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
	 *
	 * @return \ScubaWhere\Entities\Bookingdetail
	 */
	public function update($id, array $data, $fail = true) {
		$addon = $this->get($id);

		if(!$addon->update($data)) {
			throw new HttpNotAcceptable(__CLASS__ . __METHOD__, [$addon->errors()->all()]);
		}

		return $addon;
	}

}

