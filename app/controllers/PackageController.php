<?php

use Illuminate\Http\Request;
use Scubawhere\Services\PackageService;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class PackageController extends ApiController {

    /** @var \Scubawhere\Services\PackageService */
    protected $packageService;

    public function __construct(PackageService $packageService, Request $request) {
        $this->packageService = $packageService;
        parent::__construct($request);
    }

    /**
     * Get a single package by ID
     *
     * @api GET /api/package/{id}
     * @param $id
     * @return \Scubawhere\Entities\Package
     * @throws HttpUnprocessableEntity
     */
    public function show($id) 
    {
        if (!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide a package ID.']);
        }
        return $this->packageService->get($id);
    }

    /**
     * Get all packages belonging to a company
     *
     * @api GET /api/package
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        $with_deleted   = (bool) Input::get('with_deleted');
        $only_available = (bool) Input::get('only_available');
        if ($with_deleted) {
            return $this->packageService->getAllWithTrashed();
        } elseif ($only_available) {
            return $this->packageService->getAvailable();
        }
        return $this->packageService->getAll();
    }

    /**
     * Create a new package
     *
     * @api POST /api/package
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $input = array(
            'name'                => 'required',
            'description'         => '',
            'parent_id'           => '',
            'available_from'      => 'date',
            'available_until'     => 'date',
            'available_for_from'  => 'date',
            'available_for_until' => 'date',
            'tickets'             => 'array',
            'courses'             => 'array',
            'accommodations'      => 'array',
            'addons'              => 'array',
            'prices'              => 'required|array'
        );
        
        $data = $this->validate($input);
        $package = $this->packageService->create($data);
        
        return $this->responseCreated('OK. Package created', $package);
        //return Response::json( array('status' => 'OK. Package created', 'id' => $package->id), 201 );
    }

    /**
     * Edit an existing package
     *
     * @api PUT /api/package
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     * @throws HttpUnprocessableEntity
     */
    public function update($id)
    {
        $input = array(
            'name'                => 'required',
            'description'         => '',
            'parent_id'           => '',
            'available_from'      => 'date',
            'available_until'     => 'date',
            'available_for_from'  => 'date',
            'available_for_until' => 'date',
            'tickets'             => 'array',
            'courses'             => 'array',
            'accommodations'      => 'array',
            'addons'              => 'array',
            'prices'              => 'required|array'
        );
        
        $data = $this->validate($input);

        $package = $this->packageService->update($id, $data);
        
        // @todo return the updated package and not just the id?
        return $this->responseOK('OK. Package updated', array('id' => $package->id));
    }

    /**
     * Delete an package and remove it from any quotes or packages
     *
     * @api DELETE /api/package
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpUnprocessableEntity
     * @throws \Scubawhere\Exceptions\Http\HttpConflict
     */
    public function destroy($id)
    {
        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide a package ID.']);
        }
        
        $this->packageService->delete($id);
        
        return $this->responseOK('Ok. Package deleted');
    }

}
