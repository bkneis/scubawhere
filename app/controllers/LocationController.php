<?php

use Scubawhere\Entities\Tag;
use Illuminate\Support\Facades\Response;
use Scubawhere\Services\LocationService;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class LocationController extends Controller {

    /** @var \Scubawhere\Services\LocationService */
    protected $location_service;

    /**
     * Response Object to create http responses
     *
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    public function __construct(LocationService $location_service, Response $response) {
        $this->location_service = $location_service;
        $this->response         = $response;
    }

    /**
     * Get a single location by ID
     *
     * @api /api/location
     *
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     *
     * @return \Scubawhere\Entities\Location
     */
    public function getIndex() 
    {
        $id = Input::get('id');

        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide an ID.']);
        }

        return $this->location_service->get($id);
    }

    /**
     * Get all locations belonging to a company
     *
     * @api /api/location/all
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->location_service->getAll();
    }

    /**
     * Get all locations belonging to a company including soft deleted models
     *
     * @api /api/location/all-with-trashed
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithTrashed()
    {
        return $this->location_service->getAllWithTrashed();
    }

    /**
     * Get all available tags associated to any location
     *
     * @api /api/location/tags
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTags()
    {
        return Tag::where('for_type', 'Location')->orderBy('name')->get();
    }

    /**
     * Create a new location
     *
     * @api /api/location/add
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAdd()
    {
        $data = Input::only('name', 'description', 'latitude', 'longitude');
        $tags = Input::get('tags', false);
        
        if( !$tags || empty($tags) ) {
            $tags = false;
        }
       
        $location = $this->location_service->create($data, $tags);
        return $this->response->json(array('status' => 'OK. Location created', 'model' => $location), 201); // 201 Created
    }

    /**
     * Edit an existing location
     *
     * @api /api/location/edit
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \Illuminate\Http\Response 200 Success with updated location
     */
    public function postUpdate()
    {
        $id = Input::get('location_id');
        $description = Input::get('description');

        $location = $this->location_service->update($id, $description);
        return $this->response->json(array('status' => 'OK. Location updated', 'model' => $location), 200); // 200 Success
    }

    /**
     * Delete an location and remove it from any quotes or packages
     *
     * @api /api/location/delete
     *
     * @throws \Scubawhere\Exceptions\NotFoundException
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDelete()
    {
        $id = Input::get('location_id');

        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__ . __METHOD__, ['Please provide a location ID.']);
        }
        $this->location_service->delete($id);

        return $this->response->json(array('status' => 'OK. Location deleted'), 200);
    }

    /**
     * Attach a location to a company
     *
     * @api /api/location/attach
     *
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAttach()
    {
        $id = Input::get('location_id');

        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide a location ID']);
        }
        $this->location_service->attach($id);

        return $this->response->json(array('status' => 'The location has been attached to your profile.'), 200);
    }

    /**
     * Dettach a location to a company
     *
     * @api /api/location/dettach
     *
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDetach()
    {
        $id = Input::get('location_id');

        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide an ID']);
        }
        $this->location_service->dettach($id);

        return $this->response->json(array('status' => 'The location has been dettached to your profile.'), 200);
    }

}
