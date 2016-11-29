<?php

use Scubawhere\Entities\Tag;
use Scubawhere\Services\TripService;
use Illuminate\Support\Facades\Response;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class TripController extends Controller {

    /** @var \Scubawhere\Services\TripService */
    protected $trip_service;

    /**
     * Response Object to create http responses
     *
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    public function __construct(TripService $trip_service, Response $response) {
        $this->trip_service = $trip_service;
        $this->response = $response;
    }

    /**
     * Get a single trip by ID
     *
     * @api /api/trip
     *
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     *
     * @return \Scubawhere\Entities\Trip
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide an ID.']);
        }
        return $this->trip_service->get($id);
    }

    /**
     * Get all trips belonging to a company
     *
     * @api /api/trip/all
     *
     * @return \Scubawhere\Entities\Trip
     */
    public function getAll()
    {
        return $this->trip_service->getAll();
    }

    /**
     * Get all trips belonging to a company including soft deleted models
     *
     * @api /api/trip/all-with-trashed
     *
     * @return \Illuminate\Database\Eloquent\Collection
     *
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
     * Create a new trip
     *
     * @api /api/trip/add
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Edit an existing trip
     *
     * @api /api/trip/edit
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Delete an trip and remove it from any quotes or packages
     *
     * @api /api/trip/delete
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('id');
        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide an ID.']);
        }
        
        $this->trip_service->delete($id);
        return $this->response->json(array('status' => 'OK. Trip deleted'), 200); // 200 Success
    }

}
