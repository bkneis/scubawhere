<?php

use Illuminate\Support\Facades\Response;
use ScubaWhere\Services\BoatroomService;
use ScubaWhere\Exceptions\NotFoundException;
use ScubaWhere\Exceptions\InvalidInputException;

class BoatroomController extends Controller {

    /**
     * Service to manage boatrooms
     * \ScubaWhere\Services\BoatroomService
     */
    protected $boatroom_service;

    /**
     * Response Object to create http responses
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    /**
     * @param BoatroomService Injected using laravel's IOC container
     */
    public function __construct(BoatroomService $boatroom_service, Response $response) {
        $this->boatroom_service = $boatroom_service;
        $this->response = $response;
    }

    /**
     * /api/boatroom
     * Get a single boatroom by ID
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @return json Boatroom model
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        return $this->boatroom_service->get($id);
    }

    /**
     * /api/boatroom/all
     * Get all boatrooms belonging to a company
     * @return array Collection Boatroom models
     */
    public function getAll()
    {
        return $this->boatroom_service->getAll();
    }

    /**
     * /api/boatroom/all-with-trashed
     * Get all boatrooms belonging to a company including soft deleted models
     * @return array Collection Boatroom models
     */
    public function getAllWithTrashed()
    {
        return $this->boatroom_service->getAllWithTrashed();
    }

    /**
     * /api/boatroom/add
     * Create a new boatroom
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 201 Created with newly created boatroom
     */
    public function postAdd()
    {
        $data = Input::only('name', 'description');
       
        $boatroom = $this->boatroom_service->create($data);
        return $this->response->json(array('status' => 'OK. Boatroom created', 'model' => $boatroom), 201); // 201 Created
    }

    /**
     * /api/boatroom/edit
     * Edit an existing boatroom
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 200 Success with updated boatroom
     */
    public function postEdit()
    {
        $id = Input::get('id');
        $data = Input::only('name', 'description');

        $boatroom = $this->boatroom_service->update($id, $data);
        return $this->response->json(array('status' => 'OK. Boatroom updated', 'model' => $boatroom), 200); // 200 Success
    }

    /**
     * /api/boatroom/delete
     * Delete an boatroom and remove it from any quotes or packages
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('id');
        if(!$id) throw new InvalidInputException(['Please provide an ID.']);

        $this->boatroom_service->delete($id);
        return $this->response->json(array('status' => 'OK. Boatroom deleted'), 200); // 200 Success
    }

}
