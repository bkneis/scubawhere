<?php

use Illuminate\Support\Facades\Response;
use ScubaWhere\Services\LocationService;
use ScubaWhere\Exceptions\NotFoundException;
use ScubaWhere\Exceptions\InvalidInputException;

class LocationController extends Controller {

    /**
     * Service to manage locations
     * @var \ScubaWhere\Services\LocationService
     */
    protected $location_service;

    /**
     * Response Object to create http responses
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    /**
     * @param LocationService Injected using laravel's IOC container
     * @param Response        Injected using laravel's IOC container
     */
    public function __construct(LocationService $location_service, Response $response) {
        $this->location_service = $location_service;
        $this->response         = $response;
    }

    /**
     * /api/location
     * Get a single location by ID
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @return json Location model
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        return $this->location_service->get($id);
    }

    /**
     * /api/location/all
     * Get all locations belonging to a company
     * @return array Collection Location models
     */
    public function getAll()
    {
        return $this->location_service->getAll();
    }

    /**
     * /api/location/all-with-trashed
     * Get all locations belonging to a company including soft deleted models
     * @return array Collection Location models
     */
    public function getAllWithTrashed()
    {
        return $this->location_service->getAllWithTrashed();
    }

    /**
     * /api/location/tags
     * Get all available tags associated to any location
     * @return array Collection of Tag models
     */
    public function getTags()
    {
        return Tag::where('for_type', 'Location')->orderBy('name')->get();
    }

    /**
     * /api/location/add
     * Create a new location
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 201 Created with newly created location
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
     * /api/location/edit
     * Edit an existing location
     * @throws \ScubaWhere\Exceptions\InvalidInputException
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
     * /api/location/delete
     * Delete an location and remove it from any quotes or packages
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('location_id');
        if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        $this->location_service->delete($id);
        return $this->response->json(array('status' => 'OK. Location deleted'), 200); // 200 Success
    }

    /**
     * /api/location/attach
     * Attach a location to a company
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postAttach()
    {
        $id = Input::get('location_id');
        if(!$id) throw new InvalidInputException(['Please provide an ID']);
        $locaion = $this->location_service->attach($id);
        return $this->response->json(array('status' => 'The location has been attached to your profile.'), 200);
    }

    /**
     * /api/location/dettach
     * Dettach a location to a company
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDetach()
    {
        $id = Input::get('location_id');
        if(!$id) throw new InvalidInputException(['Please provide an ID']);
        $locaion = $this->location_service->dettach($id);
        return $this->response->json(array('status' => 'The location has been dettached to your profile.'), 200);
    }

}
