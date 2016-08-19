<?php

use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TrainingController extends Controller
{

    protected $log_service;

    public function __construct(LogService $log_service)
    {
        $this->log_service = $log_service;
    }

    public function getIndex()
    {
        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }

            return Context::get()->trainings()->withTrashed()->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The class could not be found.')), 404); // 404 Not Found
        }
    }

    public function getAll()
    {
        return Context::get()->trainings()->get();
    }

    public function getAllWithTrashed()
    {
        return Context::get()->trainings()->withTrashed()->get();
    }

    public function postAdd()
    {
        $data = Input::only('name', 'description', 'duration');

        $training = new Training($data);

        if (!$training->validate()) {
            // The validator failed
            return Response::json(array('errors' => $training->errors()->all()), 406); // 406 Not Acceptable
        }

        // Input has been validated, save the model
        $training = Context::get()->trainings()->save($training);

        // When no problems occur, we return a success response
        return Response::json(array('status' => 'OK. Class created', 'id' => $training->id), 201); // 201 Created
    }

    public function postEdit()
    {
        $data = Input::only('name', 'description', 'duration');

        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }
            $training = Context::get()->trainings()->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The class could not be found.')), 404); // 404 Not Found
        }

        if (!$training->update($data)) {
            // When validation fails
            return Response::json(array('errors' => $training->errors()->all()), 406); // 406 Not Acceptable
        }

        // When no problems occur, we return a success response
        return Response::json(array('status' => 'OK. Trip updated'), 200); // 200 OK
    }

    public function getTest()
    {
        return Context::get()->trainings()->with('test')->findOrfail(Input::get('id'));
    }

    public function postDelete()
    {
        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }
            $training = Context::get()->trainings()->with('courses')->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The class could not be found.')), 404); // 404 Not Found
        }

        if(!$training->getDeleteableAttribute()) {
            $problem_courses = array();
            foreach($training->courses as $obj) {
                if($obj->tickets()->exists()) {
                    DB::table('course_training')
                        ->where('course_id', $obj->id)
                        ->where('training_id', $training->id)
                        ->update(array('deleted_at' => DB::raw('NOW()')));    
                }
                else {
                    array_push($problem_courses, $obj);
                }
            }
            if(sizeof($problem_courses) > 0)
                $logger = $this->log_service->create('Attempting to delete the class ' . $training->name);
            else {
                $training->delete();
                return Response::json(array('status' => 'Ok. Class deleted'), 200);
            }

            foreach($problem_courses as $prob) 
            {
                $logger->append('The class can not be deleted becuase it belongs to the course ' . $obj->name . ', please assign a diffrent class or ticket to it');
            }
            return Response::json('The class could not be deleted as it is assigned to a course, please visit the error logs to view how to correct it before deleting it', 409);
        }
    }
}
