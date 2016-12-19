<?php

use Illuminate\Http\Request;
use Scubawhere\Services\CourseService;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class CourseController extends ApiController{

    /** @var CourseService */
    protected $courseService;

    public function __construct(CourseService $courseService, Request $request) {
        $this->courseService = $courseService;
        parent::__construct($request);
    }

    /**
     * Get a single course by ID
     *
     * @api GET /api/course/{id}
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     * @return \Scubawhere\Entities\Course
     */
    public function show($id) 
    {
        if (!$id) {
            throw new HttpNotAcceptable(__CLASS__.__METHOD__, ['Please provide an ID.']);
        }
        return $this->courseService->get($id);
    }

    /**
     * Get all courses belonging to a company
     *
     * @api GET /api/course
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        $with_deleted = (bool) $this->request->get('with_deleted');
        
        if ($with_deleted) {
            return $this->courseService->getAllWithTrashed();
        }
        
        return $this->courseService->getAll();
    }

    /**
     * Create a new course
     *
     * @api POST /api/course
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $rules = array(
            'name'           => 'required',
            'description'    => '',
            'capacity'       => 'required',
            'certificate_id' => '',
            'prices'         => 'required',
            'tickets'        => 'required_without_all:trainings',
            'trainings'      => 'required_without_all:tickets'
        );
        
        $data = $this->validateInput($rules);
        
        if($data['certificate_id'] === '') {
            unset($data['certificate_id']);
        } else {
            $data['certificate_id'] = (int) $data['certificate_id'];
        }
        
        $course = $this->courseService->create($data);
        
        return $this->responseCreated('Ok. Course created', $course);
    }

    /**
     * Edit an existing course
     *
     * @param $id
     * @api PUT /api/course/{id}
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpUnprocessableEntity
     */
    public function update($id)
    {
        $rules = array(
            'name'           => 'required',
            'description'    => '',
            'capacity'       => 'required',
            'certificate_id' => '',
            'prices'         => 'required'
        );
        
        $data = $this->validateInput($rules);

        if($data['certificate_id'] === '') {
            unset($data['certificate_id']);
        } else {
            $data['certificate_id'] = (int) $data['certificate_id'];
        }

        $course = $this->courseService->update($id, $data);
        return $this->responseOK('OK. Course updated', array('model' => $course));
    }

    /**
     * Delete an course and remove it from any quotes or packages
     *
     * @param $id
     * @api /api/course/delete
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpUnprocessableEntity
     * @throws \Scubawhere\Exceptions\Http\HttpConflict
     */
    public function destroy($id)
    {
        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide an course ID.']);
        }
        $this->courseService->delete($id);
        return $this->responseOK('OK. Course deleted.');
    }

}
