<?php

use Illuminate\Http\Request;
use Scubawhere\Services\AddonService;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

/**
 * Class AddonController
 *
 * Compulsory addons were removed from the user features after release/hammerhead-v1.1.
 * We need to force it to 0, even if the user supplies the compulsory flag until v2 of the API is released
 *
 * @api /api/addon
 * @author Bryan Kneis
 * @version 1.1
 */
class AddonController extends ApiController {

    /** @var AddonService */
    protected $addonService;

    public function __construct(AddonService $addon_service, Request $request) 
    {
        $this->addonService = $addon_service;
        parent::__construct($request);
    }

    /**
     * Get a single addon by ID
     *
     * @param $id
     * @api GET /api/addon/{id}
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpUnprocessableEntity
     */
    public function show($id) 
    {
        if (is_null($id)) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['The id field is required']);
        }
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
        $input = array(
            'name'        => 'required',
            'description' => '',
            'parent_id'   => '',
            'prices'      => 'required'
        );
        
        $data = $this->validate($input);
        
        $data['compulsory'] = 0; 
        $addon = $this->addonService->create($data);
        
        return $this->responseCreated('Ok. Addon created', $addon->load('prices', 'basePrices'));
    }

    /**
     * Edit an existing addon
     *
     * @param $id
     * @api PUT /api/addon/{id}
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpUnprocessableEntity
     */
    public function update($id)
    {
        $input = array(
            'name'        => 'required',
            'description' => '',
            'parent_id'   => '',
            'prices'      => 'required'
        );

        $data = $this->validate($input);
        
        $data['compulsory'] = 0; 
        $addon = $this->addonService->update($id, $data);

        return $this->responseOK('OK. Addon updated', array('model' => $addon->load('prices', 'basePrices')));
    }

    /**
     * Delete an addon and remove it from any quotes or packages
     *
     * @param $id
     * @api DELETE /api/addon/{id}
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
        
        return $this->responseOK('OK. Addon deleted');
    }

}
