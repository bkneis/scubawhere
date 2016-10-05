<?php

use ScubaWhere\Services\AddonService;
use ScubaWhere\Exceptions\NotFoundException;
use ScubaWhere\Exceptions\InvalidInputException;

class AddonController extends Controller {

    /**
     * Service to manage addons
     * \ScubaWhere\Services\AddonService
     */
    protected $addon_service;

    /**
     * @param AddonService Injected using laravel's IOC container
     */
    public function __construct(AddonService $addon_service) {
        $this->addon_service = $addon_service;
    }

    /**
     * /api/addon
     * Get a single addon by ID
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @return json Addon model
     */
    public function getIndex() 
    {
        $data = Input::get('id');
        $rules = array('id' => 'required');
        $messages = array('id.required' => 'The addon could not be found.');
        $validator = Validator::make(Input::all(), $rules, $messages);
        if($validator->fails()) throw new NotFoundException($validator->errors()->all());

        return $this->addon_service->get($data);
    }

    /**
     * /api/addon/all
     * Get all addons belonging to a company
     * @return array Collection Accommodation models
     */
    public function getAll()
    {
        return $this->addon_service->getAll();
    }

    /**
     * /api/addon/all-with-trashed
     * Get all addons belonging to a company including soft deleted models
     * @return array Collection Accommodation models
     */
    public function getAllWithTrashed()
    {
        return $this->addon_service->getAllWithTrashed();
    }

    /**
     * /api/addon/add
     * Create a new addon
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 201 Created with newly created addon
     */
    public function postAdd()
    {
        $data = Input::only('name', 'description', 'capacity', 'parent_id'); // Please NEVER use parent_id in the front-end!
        $data['compulsory'] = 0; // @todo Compulsory is always 0 until we can safely remove it
        $rules = array(
            'name'        => 'required',
            'base_prices' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if($validator->fails()) throw new InvalidInputException($validator->errors()->all());

        $addon = $this->addon_service->create($data, Input::get('base_prices'), Input::get('prices'));
        return Response::json(array('status' => 'OK. Addon created', 'model' => $addon), 201); // 201 Created
    }

    /**
     * /api/addon/edit
     * Edit an existing addon
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 200 Success with updated accommodation
     */
    public function postEdit()
    {
        $data = Input::only('name', 'description', 'capacity', 'parent_id'); // Please NEVER use parent_id in the front-end!
        $data['compulsory'] = 0; // @todo Compulsory is always 0 until we can safely remove it
        $rules = array(
            'name' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if($validator->fails()) throw new InvalidInputException($validator->errors()->all());

        $addon = $this->addon_service->update(Input::get('id'), $data, Input::get('base_prices'), Input::get('prices'));
        return Response::json(array('status' => 'OK. Addon updated', 'model' => $addon), 200); // 200 Success
    }

    /**
     * /api/addon/delete
     * Delete an addon and remove it from any quotes or packages
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('id');
        if(!$id) throw new NotFoundException(['The Addon was not found']);
        $this->addon_service->delete($id);
        return Response::json(array('status' => 'OK. Addon deleted'), 200); // 200 Success
    }

}
