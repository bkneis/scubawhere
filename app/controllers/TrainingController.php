<?php

use Scubawhere\Services\TrainingService;
use Scubawhere\Exceptions\NotFoundException;
use Scubawhere\Exceptions\InvalidInputException;

class TrainingController extends Controller {

    /**
     * Service to manage trainings
     * \Scubawhere\Services\TrainingService
     */
    protected $training_service;

    /**
     * @param TrainingService Injected using laravel's IOC container
     */
    public function __construct(TrainingService $training_service) {
        $this->training_service = $training_service;
    }

    /**
     * /api/training
     * Get a single training by ID
     * @throws \Scubawhere\Exceptions\NotFoundException
     * @return json Training model
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        return $this->training_service->get($id);
    }

    /**
     * /api/training/all
     * Get all trainings belonging to a company
     * @return array Collection Training models
     */
    public function getAll()
    {
        return $this->training_service->getAll();
    }

    /**
     * /api/training/all-with-trashed
     * Get all trainings belonging to a company including soft deleted models
     * @return array Collection Training models
     */
    public function getAllWithTrashed()
    {
        return $this->training_service->getAllWithTrashed();
    }

    /**
     * /api/training/add
     * Create a new training
     * @throws \Scubawhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 201 Created with newly created training
     */
    public function postAdd()
    {
        $data = Input::only('name', 'description', 'duration');

        $training = $this->training_service->create($data);
        return Response::json(array('status' => 'OK. Class created', 'model' => $training), 201); // 201 Created
    }

    /**
     * /api/training/edit
     * Edit an existing training
     * @throws \Scubawhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 200 Success with updated training
     */
    public function postEdit()
    {
        $id = Input::get('id');
        $data = Input::only('name', 'description', 'duration');

        $training = $this->training_service->update($id, $data);
        return Response::json(array('status' => 'OK. Class updated', 'model' => $training), 200); // 200 Success
    }

    /**
     * /api/training/delete
     * Delete an training and remove it from any quotes or packages
     * @throws \Scubawhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('id');

        if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        $this->training_service->delete($id);
        return Response::json(array('status' => 'OK. Class deleted'), 200); // 200 Success
    }

}
