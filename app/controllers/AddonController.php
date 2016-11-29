<?php

use Scubawhere\Services\AddonService;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;

/**
 * Class AddonController
 *
 * @todo Move all validation to the service layer
 *
 * @api /api/addon
 * @author Bryan Kneis
 * @version 1.0
 */
class AddonController extends Controller {

    /** @var \Scubawhere\Services\AddonService */
    protected $addon_service;

    public function __construct(AddonService $addon_service) {
        $this->addon_service = $addon_service;
    }

    /**
     * Get a single addon by ID
     *
     * @api /api/addon
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndex() 
    {
        $data = Input::get('id');

        $rules = array('id' => 'required');
        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails()) {
            throw new HttpNotFound(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        return $this->addon_service->get($data);
    }

    /**
     * Get all addons belonging to a company
     *
     * @api /api/addon/all
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->addon_service->getAll();
    }

    /**
     * Get all addons belonging to a company including soft deleted models
     *
     * @api /api/addon/all-with-trashed
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithTrashed()
    {
        return $this->addon_service->getAllWithTrashed();
    }

    /**
     * Create a new addon
     *
     * @api /api/addon/add
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAdd()
    {
        $data        = Input::only('name', 'description', 'capacity', 'parent_id'); // Please NEVER use parent_id in the front-end!
        $base_prices = Input::get('base_prices', []);
        $prices      = Input::get('prices', []);
        $data['compulsory'] = 0; // @todo Compulsory is always 0 until we can safely remove it

        $rules = array(
            'name'        => 'required',
            'base_prices' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails()) {
            throw new HttpNotAcceptable(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        $addon = $this->addon_service->create($data, $base_prices, $prices);

        return Response::json(array('status' => 'OK. Addon created', 'model' => $addon), 201);
    }

    /**
     * Edit an existing addon
     *
     * @api /api/addon/edit
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEdit()
    {
        $id          = Input::get('id');
        $data        = Input::only('name', 'description', 'capacity', 'parent_id'); // Please NEVER use parent_id in the front-end!
        $base_prices = Input::get('base_prices', []);
        $prices      = Input::get('prices', []);

        $data['compulsory'] = 0; // @todo Compulsory is always 0 until we can safely remove it

        $rules = array(
            'name' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails()) {
            throw new HttpNotAcceptable(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        $addon = $this->addon_service->update($id, $data, $base_prices, $prices);

        return Response::json(array('status' => 'OK. Addon updated', 'model' => $addon), 200);
    }

    /**
     * Delete an addon and remove it from any quotes or packages
     *
     * @api /api/addon/delete
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDelete()
    {
        $id = Input::get('id');

        if(!$id) {
            throw new HttpNotFound(__CLASS__.__METHOD__, ['The Addon was not found']);
        }
        $this->addon_service->delete($id);

        return Response::json(array('status' => 'OK. Addon deleted'), 200);
    }

}
