<?php

use ScubaWhere\Services\CourseService;
use ScubaWhere\Exceptions\NotFoundException;
use ScubaWhere\Exceptions\InvalidInputException;

class CourseController extends Controller {

    /**
     * Service to manage courses
     * \ScubaWhere\Services\CourseService
     */
    protected $course_service;

    /**
     * @param CourseService Injected using laravel's IOC container
     */
    public function __construct(CourseService $course_service) {
        $this->course_service = $course_service;
    }

    /**
     * /api/course
     * Get a single course by ID
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @return json Course model
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        return $this->course_service->get($id);
    }

    /**
     * /api/course/all
     * Get all courses belonging to a company
     * @return array Collection Course models
     */
    public function getAll()
    {
        return $this->course_service->getAll();
    }

    /**
     * /api/course/all-with-trashed
     * Get all courses belonging to a company including soft deleted models
     * @return array Collection Course models
     */
    public function getAllWithTrashed()
    {
        return $this->course_service->getAllWithTrashed();
    }

    /**
     * /api/course/add
     * Create a new course
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 201 Created with newly created course
     */
    public function postAdd()
    {
        $data = Input::only('name', 'description', 'capacity', 'certificate_id');
        $tickets = Input::get('tickets', []);
        $trainings = Input::get('trainings', []);
        $base_prices = Input::get('base_prices');
        $prices = Input::get('prices');

        if (empty($trainings) && empty($tickets)) {
            throw new InvalidInputException(['Either a class or a ticket is required.']);
        }

        if (empty($data['certificate_id'])) {
            $data['certificate_id'] = null;
        }
       
        $course = $this->course_service->create($data, $tickets, $trainings, $base_prices, $prices);
        return Response::json(array('status' => 'OK. Course created', 'model' => $course), 201); // 201 Created
    }

    /**
     * /api/course/edit
     * Edit an existing course
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 200 Success with updated course
     */
    public function postEdit()
    {
        $id = Input::get('id');
        $data = Input::only('name', 'description', 'capacity', 'certificate_id');
        $base_prices = Input::get('base_prices');
        $prices = Input::get('prices');

        $course = $this->course_service->update($id, $data, $base_prices, $prices);
        return Response::json(array('status' => 'OK. Course updated', 'model' => $course), 200); // 200 Success
    }

    /**
     * /api/course/delete
     * Delete an course and remove it from any quotes or packages
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('id');
        if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        $this->course_service->delete($id);
        return Response::json(array('status' => 'OK. Course deleted'), 200); // 200 Success
    }

}
