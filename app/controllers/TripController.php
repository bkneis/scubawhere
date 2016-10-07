<?php

use Illuminate\Support\Facades\Response;
use ScubaWhere\Services\TripService;
use ScubaWhere\Exceptions\NotFoundException;
use ScubaWhere\Exceptions\InvalidInputException;

class TripController extends Controller {

    /**
     * Service to manage trips
     * \ScubaWhere\Services\TripService
     */
    protected $trip_service;

    /**
     * Response Object to create http responses
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    /**
     * @param TripService Injected using laravel's IOC container
     */
    public function __construct(TripService $trip_service, Response $response) {
        $this->trip_service = $trip_service;
        $this->response = $response;
    }

    /**
     * /api/trip
     * Get a single trip by ID
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @return json Trip model
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        return $this->trip_service->get($id);
    }

    /**
     * /api/trip/all
     * Get all trips belonging to a company
     * @return array Collection Trip models
     */
    public function getAll()
    {
        return $this->trip_service->getAll();
    }

    /**
     * /api/trip/all-with-trashed
     * Get all trips belonging to a company including soft deleted models
     * @return array Collection Trip models
     */
    public function getAllWithTrashed()
    {
        return $this->trip_service->getAllWithTrashed();
    }

    public function getTags()
    {
        return Tag::where('for_type', 'Trip')->orderBy('name')->get();
    }

    /**
     * /api/trip/add
     * Create a new trip
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 201 Created with newly created trip
     */
    public function postAdd()
    {
        $data = Input::only('name', 'description', 'duration');

        // Check optional fields
        if (Input::has('boat_required')) {
            $data['boat_required'] = Input::get('boat_required'); // If not present in input array, defaults to TRUE
        }

        $locations = Input::get('locations', []);
        $tags = Input::get('tags', []);
       
        $trip = $this->trip_service->create($data, $locations, $tags);
        return $this->response->json(array('status' => 'OK. Trip created', 'model' => $trip), 201); // 201 Created
    }

    /**
     * /api/trip/edit
     * Edit an existing trip
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 200 Success with updated trip
     */
    public function postEdit()
    {
        $id = Input::get('id');
        $data = Input::only('name', 'description', 'duration');
        $locations = Input::get('locations');
        $tags = Input::get('tags');

        // Check optional fields
        if (Input::has('boat_required')) {
            $data['boat_required'] = Input::get('boat_required'); // If not present in input array, defaults to TRUE
        }

        $trip = $this->trip_service->update($id, $data, $locations, $tags);
        return $this->response->json(array('status' => 'OK. Trip updated', 'model' => $trip), 200); // 200 Success
    }

    /**
     * /api/trip/delete
     * Delete an trip and remove it from any quotes or packages
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('id');
        //if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        
        $this->trip_service->delete($id);
        return $this->response->json(array('status' => 'OK. Trip deleted'), 200); // 200 Success
    }

}
