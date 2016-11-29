<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Training;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;
use Scubawhere\Exceptions\InvalidInputException;

/**
 * Class TrainingRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\TrainingRepoInterface
 */
class TrainingRepo extends BaseRepo implements TrainingRepoInterface {

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
     * Get all trainings for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return Training::onlyOwners()->with($relations)->get();
    }

    /**
     * Get all trainings for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection 
     */
    public function allWithTrashed(array $relations = []) {
        return Training::onlyOwners()->with($relations)->withTrashed()->get();
    }

    /**
     * Get an training for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Scubawhere\Entities\Training
     */
    public function get($id, array $relations = [], $fail = true) {
        $training = Training::onlyOwners()->with($relations)->find($id);

        if(is_null($training) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The training could not be found']);
        }

        return $training;
    }

    /**
     * Get an training for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Scubawhere\Entities\Training
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $training = Training::onlyOwners()->where($query)->with($relations)->find();

        if(is_null($training) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The training could not be found']);
        }

        return $training;
    }

    /**
     * Get a training and any bookings that are scheudled for the future.
     *
     * Since trainings are not directly related to a booking, we need to go
     * through the courses that they are contained in
     *
     * @param $id
     * @param bool $fail
     *
     * @throws HttpNotFound
     *
     * @return \Scubawhere\Entities\Training
     */
    public function getUsedInFutureBookings($id, $fail = true)
    {
        $training = Training::onlyOwners()
            ->with([
                'courses.bookingdetails.training_session' => function($q) {
                    $q->where('start', '>=', Helper::localtime());
                }])
            ->find($id);

        if(is_null($training) && $fail) {
            throw new HttpNotFound(__CLASS__.__METHOD__, ['The class could not be found']);
        }

        return $training;
    }

    /**
     * Create an training and associate it with its company
     *
     * @param array $data
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \Scubawhere\Entities\Training
     */
    public function create($data) {
        $training = new Training($data);

        if (!$training->validate()) {
            throw new InvalidInputException($training->errors()->all());
        }
        
        return Context::get()->trainings()->save($training);
    }

    /**
     * Update an training
     *
     * @param int   $id
     * @param array $data
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \ScubaWhere\Entities\Training
     */
    public function update($id, array $data, $fail = true) {
        $training = $this->get($id, [], $fail);

        if(!$training->update($data)) {
            throw new HttpNotAcceptable(__CLASS__ . __METHOD__, [$training->errors()->all()]);
        }

        return $training;
    }

}
