<?php

use ScubaWhere\Exceptions\NotFoundException;
use ScubaWhere\Services\AccommodationService;
use ScubaWhere\Exceptions\InvalidInputException;

class AccommodationController extends Controller {

    /**
     * Service to manage accommodations
     * \ScubaWhere\Services\AccommodationService
     */
    protected $accommodation_service;

    /**
     * @param AccommodationService Injected using laravel's IOC container
     */
    public function __construct(AccommodationService $accommodation_service) {
        $this->accommodation_service = $accommodation_service;
    }

    /**
     * /api/accommodation
     * Get a single accommodation by ID
     * @throws \ScubaWhere\Exceptions\NotFOundException
     * @return json Accommodation model
     */
    public function getIndex() 
    {
        $data = Input::get('id');
        $rules = array('id' => 'required');
        $messages = array('id.required' => 'The accommodation could not be found.');
        $validator = Validator::make(Input::all(), $rules, $messages);
        if($validator->fails()) throw new NotFoundException($validator->errors()->all());

        return $this->accommodation_service->get($data);
    }

    /**
     * /api/accommodation/all
     * Get all accommodations belonging to a company
     * @return array Collection Accommodation models
     */
    public function getAll()
    {
        return $this->accommodation_service->getAll();
    }

    /**
     * /api/accommodation/all-with-trashed
     * Get all accommodations belonging to a company including soft deleted models
     * @return array Collection Accommodation models
     */
    public function getAllWithTrashed()
    {
        return $this->accommodation_service->getAllWithTrashed();
    }

    /**
     * /api/accommodation/filter
     * Get all accommodations belonging to a company
     * @throws \ScubaWhere\Exceptions\NotFoundException
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
        if($validator->fails()) throw new NotFoundException($validator->errors()->all());

        return $this->accommodation_service->getFilter($data);
    }

    /**
     * /api/accommodation/add
     * Create a new accommodation
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 201 Created with newly created accommodation
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
        if($validator->fails()) throw new InvalidInputException($validator->errors()->all());

        $accommodation = $this->accommodation_service->create($data, Input::get('base_prices'), Input::get('prices'));
        return Response::json(array('status' => 'OK. Accommodation created', 'model' => $accommodation->load('basePrices', 'prices')), 201); // 201 Created
    }

    /**
     * /api/accommodation/edit
     * Edit an existing accommodation
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 200 Success with updated accommodation
     */
    public function postEdit()
    {
        $data = Input::only('name', 'description', 'capacity', 'parent_id'); // Please NEVER use parent_id in the front-end!
        $rules = array(
            'name' => 'required',
            'capacity' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if($validator->fails()) throw new InvalidInputException($validator->errors()->all());

        $accommodation = $this->accommodation_service->update(Input::get('id'), $data, Input::get('base_prices'), Input::get('prices'));
        return Response::json(array('status' => 'OK. Accommodation updated', 'model' => $accommodation->load('basePrices', 'prices')), 200); // 200 Success
    }

    /**
     * /api/accommodation/delete
     * Delete an accommodation and remove it from any quotes or packages
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('id');
        if(!$id) throw new NotFoundException(['The Accommodation was not found']);
        $this->accommodation_service->delete($id);
        return Response::json(array('status' => 'OK. Accommodation deleted'), 200); // 200 Success
    }

}
