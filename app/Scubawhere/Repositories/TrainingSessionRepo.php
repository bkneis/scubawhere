<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\TrainingSession;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\InvalidInputException;

/**
 * Class TrainingSessionRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\TrainingSessionRepoInterface
 */
class TrainingSessionRepo extends BaseRepo implements TrainingSessionRepoInterface {

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
    public function all(array $relations  = []) {
		return $this->company_model
            ->training_sessions()
            ->with($relations)
            ->firstOrFail(['training_sessions.*']);
    }

    /**
     * Get all trainings for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection 
     */
    public function allWithTrashed(array $relations  = []) {
		return $this->company_model
            ->training_sessions()
            ->withTrashed()
            ->firstOrFail(['training_sessions.*']);
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
     * @return \Scubawhere\Entities\TrainingSession
     */
    public function get($id, array $relations = [], $fail = true) {
		$training_session = $this->company_model
			->training_sessions()
            ->with($relations)
			->where('training_sessions.id', $id)
			->firstOrFail(['training_sessions.*']);

        if(is_null($training_session) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The training session could not be found']);
        }

        return $training_session;
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
     * @return \Scubawhere\Entities\TrainingSession
     */
    public function getWhere(array $query, array $relations = [], $fail = true)
    {
        $training_session = $this->company_model
            ->training_sessions()
            ->with($relations)
            ->where($query)
            ->firstOrFail(['training_sessions.*']);

        if(is_null($training_session) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The training session could not be found']);
        }

        return $training_session;
    }

    /**
     * Create an training and associate it with its company
     *
     * @param array $data
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \Scubawhere\Entities\TrainingSession
     */
    public function create($data) {
        $training = new TrainingSession($data);
        if (!$training->validate()) {
            throw new InvalidInputException($training->errors()->all());
        }
        return Context::get()->training_sessions()->save($training);
    }

}
