<?php

use Illuminate\Http\Request;
use Scubawhere\Services\AddonService;
use Scubawhere\Exceptions\Http\HttpNotFound;

/**
 * Class AddonController
 *
 * @todo Move all validation to the service layer
 * @todo Compulsory is always 0 until we can safely remove it
 *
 * @api /api/addon
 * @author Bryan Kneis
 * @version 1.0
 */
class AddonController extends ApiController {

    /** @var \Scubawhere\Services\AddonService */
    protected $addonService;

    public function __construct(AddonService $addon_service, Request $request) 
    {
        $this->addonService = $addon_service;
        parent::__construct($request);
    }

    /**
     * Get a single addon by ID
     *
     * @api GET /api/addon/{id}
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpNotFound
     */
    public function show($id) 
    {
        $rules = array('id' => 'required');
        $this->validateInput($id, $rules);
        return $this->addonService->get($id);
    }

    /**
     * Get all addons belonging to a company
     *
     * @api GET /api/addon
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        $with_deleted = (bool) $this->request->get('with_deleted');
        if ($with_deleted) {
            return $this->addonService->getAllWithTrashed();
        } else {
            return $this->addonService->getAll();
        }
    }

    /**
     * Create a new addon
     *
     * @api POST /api/addon
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $data = $this->request->only('name', 'description', 'parent_id', 'prices');
        $data['compulsory'] = 0; 
        $rules = array(
            'name'        => 'required',
            'prices'      => 'required'
        );
        
        $this->validateInput($data, $rules);
        $addon = $this->addonService->create($data);
        
        return $this->responseCreated(array('status' => 'Ok. Addon created', 'model' => $addon->load('prices')));
    }

    /**
     * Edit an existing addon
     *
     * @api PUT /api/addon
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     */
    public function update($id)
    {
        $data = $this->request->only('name', 'description', 'capacity', 'parent_id', 'prices');
        $data['compulsory'] = 0; 

        $rules = array(
            'name'   => 'required',
            'prices' => 'required'
        );
        
        $this->validateInput($data, $rules);
        $addon = $this->addonService->update($id, $data);

        return $this->responseOK(array('status' => 'OK. Addon updated', 'model' => $addon->load('prices')));
    }

    /**
     * Delete an addon and remove it from any quotes or packages
     *
     * @api DELETE /api/addon
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpNotFound
     * @throws \Scubawhere\Exceptions\ConflictException
     */
    public function destroy($id)
    {
        if(!$id) {
            throw new HttpNotFound(__CLASS__.__METHOD__, ['The Addon was not found']);
        }
        $this->addonService->delete($id);
        
        return $this->responseOK(array('status' => 'OK. Addon deleted'));
    }

}
