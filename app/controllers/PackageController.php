<?php

use Illuminate\Support\Facades\Response;
use Scubawhere\Services\PackageService;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class PackageController extends Controller {

    /** @var \Scubawhere\Services\PackageService */
    protected $package_service;

    /**
     * Response Object to create http responses
     *
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    public function __construct(PackageService $package_service, Response $response) {
        $this->package_service = $package_service;
        $this->response = $response;
    }

    /**
     * Get a single package by ID
     *
     * @api /api/package
     *
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     *
     * @return \Scubawhere\Entities\Package
     */
    public function getIndex() 
    {
        $id = Input::get('id');

        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide a package ID.']);
        }

        return $this->package_service->get($id);
    }

    /**
     * Get all packages belonging to a company
     *
     * @api /api/package/all
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->package_service->getAll();
    }

    /**
     * Get all packages belonging to a company including soft deleted models
     *
     * @api /api/package/all-with-trashed
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithTrashed()
    {
        return $this->package_service->getAllWithTrashed();
    }

    /**
     * Get all packages belonging to a company
     *
     * @api /api/package/only-available
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOnlyAvailable()
    {
        return $this->package_service->getAvailable();
    }

    /**
     * Create a new package
     *
     * @api /api/package/add
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAdd()
    {
        $data = Input::only('name', 'description', 'parent_id', 'available_from', 'available_until', 'available_for_from', 'available_for_until'); // Please NEVER use parent_id in the front-end!
        $tickets = Input::get('tickets', []);
        $courses = Input::get('courses', []);
        $accommodations = Input::get('accommodations', []);
        $addons = Input::get('addons', []);
        $base_prices = Input::get('base_prices');
        $prices = Input::get('prices');
               
        $package = $this->package_service->create($data, $tickets, $courses, $accommodations, $addons, $base_prices, $prices);
        return Response::json( array('status' => 'OK. Package created', 'id' => $package->id), 201 ); // 201 Created
    }

    /**
     * Edit an existing package
     *
     * @api /api/package/edit
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEdit()
    {
        $id = Input::get('id');
        $data = Input::only('name', 'description', 'parent_id', 'available_from', 'available_until', 'available_for_from', 'available_for_until'); // Please NEVER use parent_id in the front-end!
        $tickets = Input::get('tickets', []);
        $courses = Input::get('courses', []);
        $accommodations = Input::get('accommodations', []);
        $addons = Input::get('addons', []);
        $base_prices = Input::get('base_prices');
        $prices = Input::get('prices');

        $package = $this->package_service->update($id, $data, $tickets, $courses, $accommodations, $addons, $base_prices, $prices);
        return $this->response->json(array('status' => 'OK. Package updated', 'id' => $package->id), 200);
    }

    /**
     * Delete an package and remove it from any quotes or packages
     *
     * @api /api/package/delete
     *
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDelete()
    {
        $id = Input::get('id');
        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide a package ID.']);
        }
        
        $this->package_service->delete($id);
        return $this->response->json(array('status' => 'OK. Package deleted'), 200); // 200 Success
    }

}
