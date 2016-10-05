<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Repositories\CourseRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CourseRepo implements CourseRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * \Company 
    */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all courses for a company
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all courses for a company
     */
    public function all() {
        return \Course::onlyOwners()->with('trainings', 'tickets', 'basePrices', 'prices')->get();
    }

    /**
     * Get all courses for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all courses for a company including soft deleted models
     */
    public function allWithTrashed() {
        return \Course::onlyOwners()->with('trainings', 'tickets', 'basePrices', 'prices')->withTrashed()->get();
    }

    /**
     * Get an course for a company from its id
     * @param  int   ID of the course
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an course for a company
     */
    public function get($id) {
        return \Course::onlyOwners()->with('trainings', 'tickets', 'basePrices', 'prices')->findOrFail($id);
    }

    /**
     * Get an course for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the course
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an course for a company
     */
    public function getWhere($column, $value) {
        return \Course::onlyOwners()->where($column, $value)->with('trainings', 'tickets', 'basePrices', 'prices')->get();
    }

    /**
     * Create an course and associate it with its company
     * @param array Information about the course to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an course for a company
     */
    public function create($data) {
        $course = new \Course($data);
        if (!$course->validate()) {
            throw new InvalidInputException($course->errors()->all());
        }
        return Context::get()->courses()->save($course);
    }

    /**
     * Update an course by id with specified data
     * @param  int   ID of the course
     * @param  array Data to update the course with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an course for a company
     */
    public function update($id, $data) {
        $course = $this->get($id);
        if(!$course->update($data)) {
            throw new InvalidInputException($course->errors()->all());
        }
        return $course;
    }

    /**
     * Delete an course by its id
     * @param  int ID of the course
     * @throws Exception
     */
    public function delete($id) {
        $course = $this->get($id);
        $course->delete();
    }

    /**
     * Delete an course by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the course
     * @throws Exception
     */
    public function deleteWhere($column, $value) {
        $course = $this->getWhere($column, $value);
        $course->delete();
    }
}