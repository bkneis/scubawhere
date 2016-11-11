<?php

use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Services\AccommodationService;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class AccommodationController extends Controller {

    /** @var \Scubawhere\Services\AccommodationService */
    protected $accommodation_service;

    public function __construct(AccommodationService $accommodation_service) {
        $this->accommodation_service = $accommodation_service;
    }

    /**
     * Get a single accommodation by ID
     *
     * @api /api/accommodation
     *
     * @throws HttpNotFound
     *
     * @return \Scubawhere\Entities\Accommodation
     */
    public function getIndex() 
    {
        $data = Input::get('id');

        $rules = array('id' => 'required');
        $messages = array('id.required' => 'The accommodation could not be found.');
        $validator = Validator::make(Input::all(), $rules, $messages);

        if($validator->fails()) {
            throw new HttpNotFound(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        return $this->accommodation_service->get($data);
    }

    /**
     * Get all accommodations belonging to a company
     *
     * @api /api/accommodation/all
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->accommodation_service->getAll();
    }

    /**
     * Get all accommodations belonging to a company including soft deleted models
     *
     * @api /api/accommodation/all-with-trashed
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithTrashed()
    {
        return $this->accommodation_service->getAllWithTrashed();
    }

    /**
     * Get all accommodations belonging to a company
     * @api /api/accommodation/filter
     * @throws HttpNotFound
     * @return array Collection Accommodation models
     */
    public function getFilter()
    {
        $data = Input::only('after', 'before', 'accommodation_id');

        $rules = array(
            'after' => 'date|required_with:before',
            'before' => 'date',
            'accommodation_id' => 'integer|min:1'
        );
        $validator = Validator::make($data, $rules);

        if($validator->fails()) {
            throw new HttpNotFound(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        return $this->accommodation_service->getFilter($data);
    }

    /**
     * Retrieve a manifest for an accommodation
     *
     * @api /accommodation/manifest
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpUnprocessableEntity
     */
    public function getManifest()
    {
        $data = Input::only('id', 'date');

        $rules = array(
            'id' => 'required|integer',
            'date' => 'required|date'
        );

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new HttpUnprocessableEntity(__CLASS__ . __METHOD__, $validator->errors()->all());
        }

        return Response::json(array(
            'status' => 'Success. Manifest retrieved',
            'data' => $this->accommodation_service->getManifest($data['id'], $data['date'])
        ), 200);
    }

    public function getAvailability()
    {
        $dates = Input::only('after', 'before');

        $rules = array(
            'after'  => 'required|date',
            'before' => 'required|date'
        );

        $validator = Validator::make($dates, $rules);

        if($validator->fails()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        return Response::json(array(
            'status' => 'Sucess. Avaialability retrieved',
            'data'   => $this->accommodation_service->getAvailability($dates)
        ));
    }

    /**
     * Create a new accommodation
     *
     * @api /api/accommodation/add
     *
     * @throws HttpUnprocessableEntity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAdd()
    {
        $data = Input::only('name', 'description', 'capacity', 'parent_id'); // Please NEVER use parent_id in the front-end!

        $rules = array(
            'name'        => 'required',
            'capacity'    => 'required',
            'base_prices' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        $accommodation = $this->accommodation_service->create($data, Input::get('base_prices'), Input::get('prices'));

        return Response::json(array('status' => 'OK. Accommodation created', 'model' => $accommodation->load('basePrices', 'prices')), 201); // 201 Created
    }

    /**
     * Edit an existing accommodation
     *
     * @api /api/accommodation/edit
     *
     * @throws HttpUnprocessableEntity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEdit()
    {
        $data = Input::only('name', 'description', 'capacity', 'parent_id'); // Please NEVER use parent_id in the front-end!

        $rules = array(
            'name' => 'required',
            'capacity' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        $accommodation = $this->accommodation_service->update(Input::get('id'), $data, Input::get('base_prices'), Input::get('prices'));

        return Response::json(array('status' => 'OK. Accommodation updated', 'model' => $accommodation->load('basePrices', 'prices')), 200);
    }

    /**
     * Delete an accommodation and remove it from any quotes or packages
     *
     * @api /api/accommodation/delete
     *
     * @throws HttpNotFound
     * @throws Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDelete()
    {
        $id = Input::get('id');

        if(!$id) {
            throw new HttpNotFound(__CLASS__.__METHOD__, ['The Accommodation was not found']);
        }

        $this->accommodation_service->delete($id);

        return Response::json(array('status' => 'OK. Accommodation deleted'), 200); // 200 Success
    }

}
