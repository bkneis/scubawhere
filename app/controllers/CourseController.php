<?php

use Scubawhere\Services\CourseService;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class CourseController extends Controller {

    /** @var \Scubawhere\Services\CourseService */
    protected $course_service;

    public function __construct(CourseService $course_service) {
        $this->course_service = $course_service;
    }

    /**
     * Get a single course by ID
     *
     * @var /api/course
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \Scubawhere\Entities\Course
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        if(!$id) throw new HttpNotAcceptable(__CLASS__.__METHOD__, ['Please provide an ID.']);
        return $this->course_service->get($id);
    }

    /**
     * Get all courses belonging to a company
     *
     * @api /api/course/all
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->course_service->getAll();
    }

    /**
     * Get all courses belonging to a company including soft deleted models
     *
     * @api /api/course/all-with-trashed
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithTrashed()
    {
        return $this->course_service->getAllWithTrashed();
    }

    /**
     * Create a new course
     *
     * @api /api/course/add
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAdd()
    {
        $data = Input::only('name', 'description', 'capacity', 'certificate_id');
        $tickets = Input::get('tickets', []);
        $trainings = Input::get('trainings', []);
        $base_prices = Input::get('base_prices');
        $prices = Input::get('prices');

        if (empty($trainings) && empty($tickets)) {
            throw new HttpNotAcceptable(__CLASS__.__METHOD__, ['Either a class or a ticket is required.']);
        }

        if (empty($data['certificate_id'])) {
            $data['certificate_id'] = null;
        }
       
        $course = $this->course_service->create($data, $tickets, $trainings, $base_prices, $prices);
        return Response::json(array('status' => 'OK. Course created', 'model' => $course), 201); // 201 Created
    }

    /**
     * Edit an existing course
     *
     * @api POST /api/course/edit
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEdit()
    {
        $id = Input::get('id');
        $data = Input::only('name', 'description', 'capacity', 'certificate_id');
        $base_prices = Input::get('base_prices', []);
        $prices = Input::get('prices', []);

        // @todo add validation decorators to services instead of in here
        if($data['certificate_id'] === '') {
            unset($data['certificate_id']);
        }

        $course = $this->course_service->update($id, $data, $base_prices, $prices);
        return Response::json(array('status' => 'OK. Course updated', 'model' => $course), 200); // 200 Success
    }

    /**
     * Delete an course and remove it from any quotes or packages
     *
     * @api /api/course/delete
     *
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDelete()
    {
        $id = Input::get('id');
        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide an course ID.']);
        }
        $this->course_service->delete($id);
        return Response::json(array('status' => 'OK. Course deleted'), 200); // 200 Success
    }

}
