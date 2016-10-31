<?php

use Illuminate\Support\Facades\Response;
use Scubawhere\Services\BoatroomService;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;

class BoatroomController extends Controller {

    /** @var \Scubawhere\Services\BoatroomService */
    protected $boatroom_service;

    /**
     * Response Object to create http responses
     *
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    public function __construct(BoatroomService $boatroom_service, Response $response) {
        $this->boatroom_service = $boatroom_service;
        $this->response = $response;
    }

    /**
     * Get a single boatroom by ID
     *
     * @api /api/boatroom
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \Scubawhere\Entities\Boatroom
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        if(!$id) throw new HttpNotAcceptable(__CLASS__.__METHOD__, ['Please provide an ID.']);
        return $this->boatroom_service->get($id);
    }

    /**
     * Get all boatrooms belonging to a company
     *
     * @api /api/boatroom/all
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->boatroom_service->getAll();
    }

    /**
     * Get all boatrooms belonging to a company including soft deleted models
     *
     * @api /api/boatroom/all-with-trashed
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithTrashed()
    {
        return $this->boatroom_service->getAllWithTrashed();
    }

    /**
     * Create a new boatroom
     *
     * @api /api/boatroom/add
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAdd()
    {
        $data = Input::only('name', 'description');
       
        $boatroom = $this->boatroom_service->create($data);
        return $this->response->json(array('status' => 'OK. Boatroom created', 'model' => $boatroom), 201); // 201 Created
    }

    /**
     * Edit an existing boatroom
     *
     * @api /api/boatroom/edit
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEdit()
    {
        $id = Input::get('id');
        $data = Input::only('name', 'description');

        $boatroom = $this->boatroom_service->update($id, $data);
        return $this->response->json(array('status' => 'OK. Boatroom updated', 'model' => $boatroom), 200); // 200 Success
    }

    /**
     * Delete an boatroom and remove it from any quotes or packages
     *
     * @var /api/boatroom/delete
     *
     * @throws \Scubawhere\Exceptions\NotFoundException
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDelete()
    {
        $id = Input::get('id');
        if(!$id) throw new HttpNotAcceptable(__CLASS__.__METHOD__, ['Please provide an ID.']);

        $this->boatroom_service->delete($id);
        return $this->response->json(array('status' => 'OK. Boatroom deleted'), 200); // 200 Success
    }

}
