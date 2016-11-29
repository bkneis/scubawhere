<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Course;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;
use Scubawhere\Exceptions\InvalidInputException;

/**
 * Class CourseRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\CourseRepoInterface
 */
class CourseRepo extends BaseRepo implements CourseRepoInterface {

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
     * Get all courses for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return Course::onlyOwners()->with($relations)->get();
        //return Course::onlyOwners()->with('trainings', 'tickets', 'basePrices', 'prices')->get();
    }

    /**
     * Get all courses for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) {
        return Course::onlyOwners()->with($relations)->withTrashed()->get();
        //return Course::onlyOwners()->with('trainings', 'tickets', 'basePrices', 'prices')->withTrashed()->get();
    }

    /**
     * Get an course for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\Course
     */
    public function get($id, array $relations = [], $fail = true) {
        $course = Course::onlyOwners()->with($relations)->find($id);

        if(is_null($course) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The course could not be found']);
        }

        return $course;
        //return Course::onlyOwners()->with('trainings', 'tickets', 'basePrices', 'prices')->findOrFail($id);
    }

    /**
     * Get an course for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\Course
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $course = Course::onlyOwners()->where($query)->with($relations)->find();

        if(is_null($course) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The course could not be found']);
        }

        return $course;
    }

    /**
     * Get a course with all of its bookings that are scheduled in the future
     *
     * @param int  $id
     * @param bool $fail
     *
     * @throws HttpNotFound
     *
     * @return mixed
     */
    public function getUsedInFutureBookings($id, $fail = true)
    {
        $course = Course::onlyOwners()
            ->with(['packages',
                'bookingdetails.session' => function($q) {
                    $q->where('start', '>=', Helper::localtime());
                },
                'bookingdetails.training_session' => function($q) {
                    $q->where('start', '>=', Helper::localtime());
                }])
            ->find($id);

        if(is_null($course) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The course could not be found']);
        }

        return $course;
    }

    /**
     * Create an course and associate it with its company
     *
     * @param array $data
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \ScubaWhere\Entities\Course
     */
    public function create($data) {
        $course = new Course($data);
        if (!$course->validate()) {
            throw new InvalidInputException($course->errors()->all());
        }
        return Context::get()->courses()->save($course);
    }

    /**
     * Update a course
     *
     * @param int   $id   ID of the addon
     * @param array $data Information about the addon to update
     * @param bool  $fail Whether to fail or not
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \ScubaWhere\Entities\Agent
     */
    public function update($id, array $data, $fail = true) {
        $addon = $this->get($id);

        if(!$addon->update($data)) {
            throw new HttpNotAcceptable(__CLASS__ . __METHOD__, [$addon->errors()->all()]);
        }

        return $addon;
    }

}