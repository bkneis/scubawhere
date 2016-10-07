<?php

use Illuminate\Support\Facades\Response;
use ScubaWhere\Services\PackageService;
use ScubaWhere\Exceptions\NotFoundException;
use ScubaWhere\Exceptions\InvalidInputException;

class PackageController extends Controller {

    /**
     * Service to manage packages
     * @var \ScubaWhere\Services\PackageService
     */
    protected $package_service;

    /**
     * Response Object to create http responses
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    /**
     * @param PackageService Injected using laravel's IOC container
     * @param Response       Injected using laravel's IOC container
     */
    public function __construct(PackageService $package_service, Response $response) {
        $this->package_service = $package_service;
        $this->response = $response;
    }

    /**
     * /api/package
     * Get a single package by ID
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @return json Package model
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        //if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        return $this->package_service->get($id);
    }

    /**
     * /api/package/all
     * Get all packages belonging to a company
     * @return array Collection Package models
     */
    public function getAll()
    {
        return $this->package_service->getAll();
    }

    /**
     * /api/package/all-with-trashed
     * Get all packages belonging to a company including soft deleted models
     * @return array Collection Package models
     */
    public function getAllWithTrashed()
    {
        return $this->package_service->getAllWithTrashed();
    }

    /**
     * /api/package/only-available
     * Get all packages belonging to a company
     * @return array Collection Package models
     */
    public function getOnlyAvailable()
    {
        return $this->package_service->getAvailable();
    }

    /**
     * /api/package/add
     * Create a new package
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 201 Created with newly created package
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
     * /api/package/edit
     * Edit an existing package
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 200 Success with updated package
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
     * /api/package/delete
     * Delete an package and remove it from any quotes or packages
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('id');
        //if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        
        $this->package_service->delete($id);
        return $this->response->json(array('status' => 'OK. Package deleted'), 200); // 200 Success
    }

}
