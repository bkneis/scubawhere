<?php

use Illuminate\Support\Facades\Response;
use ScubaWhere\Services\BoatService;
use ScubaWhere\Exceptions\NotFoundException;
use ScubaWhere\Exceptions\InvalidInputException;

class BoatController extends Controller {

    /**
     * Service to manage boats
     * \ScubaWhere\Services\BoatService
     */
    protected $boat_service;

    /**
     * Response Object to create http responses
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    /**
     * @param BoatService Injected using laravel's IOC container
     */
    public function __construct(BoatService $boat_service, Response $response) {
        $this->boat_service = $boat_service;
        $this->response = $response;
    }

    /**
     * /api/boat
     * Get a single boat by ID
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @return json Boat model
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        //if(!$id) throw new InvalidInputException(['Please provide an ID.']);

        return $this->boat_service->get($id);
    }

    /**
     * /api/boat/all
     * Get all boats belonging to a company
     * @return array Collection Boat models
     */
    public function getAll()
    {
        return $this->boat_service->getAll();
    }

    /**
     * /api/boat/all-with-trashed
     * Get all boats belonging to a company including soft deleted models
     * @return array Collection Boat models
     */
    public function getAllWithTrashed()
    {
        return $this->boat_service->getAllWithTrashed();
    }

    /**
     * /api/boat/add
     * Create a new boat
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 201 Created with newly created boat
     */
    public function postAdd()
    {
        $data = Input::only(
            'name',
            'description',
            'capacity'
        );
        $boatrooms = Input::get('boatrooms', []);
       
        $boat = $this->boat_service->create($data, $boatrooms);
        return $this->response->json(array('status' => 'OK. Boat created', 'model' => $boat), 201); // 201 Created
    }

    /**
     * /api/boat/edit
     * Edit an existing boat
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 200 Success with updated boat
     */
    public function postEdit()
    {
        $id = Input::get('id');
        $data = Input::only(
            'name',
            'description',
            'capacity'
        );
        $boatrooms = Input::get('boatrooms', []);

        $boat = $this->boat_service->update($id, $data, $boatrooms);
        return $this->response->json(array('status' => 'OK. Boat updated', 'model' => $boat), 200); // 200 Success
    }

    /**
     * /api/boat/delete
     * Delete an boat and remove it from any quotes or packages
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('id');
        //if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        
        $this->boat_service->delete($id);
        return $this->response->json(array('status' => 'OK. Boat deleted'), 200); // 200 Success
    }

}
