<?php

use Illuminate\Support\Facades\Response;
use Scubawhere\Services\BoatService;

class BoatController extends Controller {

    /** @var \Scubawhere\Services\BoatService */
    protected $boat_service;

    /**
     * Response Object to create http responses
     *
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    public function __construct(BoatService $boat_service, Response $response) {
        $this->boat_service = $boat_service;
        $this->response = $response;
    }

    /**
     * Get a single boat by ID
     *
     * @api /api/boat
     *
     * @throws \Scubawhere\Exceptions\NotFoundException
     *
     * @return \Scubawhere\Entities\Boat
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        //if(!$id) throw new InvalidInputException(['Please provide an ID.']);

        return $this->boat_service->get($id);
    }

    /**
     * Get all boats belonging to a company
     *
     * @api /api/boat/all
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->boat_service->getAll();
    }

    /**
     * Get all boats belonging to a company including soft deleted models
     *
     * @api /api/boat/all-with-trashed
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithTrashed()
    {
        return $this->boat_service->getAllWithTrashed();
    }

    /**
     * Create a new boat
     *
     * @api /api/boat/add
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Edit an existing boat
     *
     * @api /api/boat/edit
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Delete an boat and remove it from any quotes or packages
     *
     * @api /api/boat/delete
     *
     * @throws \Scubawhere\Exceptions\NotFoundException
     * @throws Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDelete()
    {
        $id = Input::get('id');
        //if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        
        $this->boat_service->delete($id);
        return $this->response->json(array('status' => 'OK. Boat deleted'), 200); // 200 Success
    }

}
