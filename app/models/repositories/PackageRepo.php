<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\BadRequestException;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\PackageRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PackageRepo extends BaseRepo implements PackageRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all packages for a company
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all packages for a company
     */
    public function all() {
        return \Package::onlyOwners()->with(
            'tickets',
            'courses',
                'courses.trainings',
                'courses.tickets',
            'accommodations',
            'addons',
            'basePrices',
            'prices'
            )->get();
    }

    /**
     * Get all packages for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all packages for a company including soft deleted models
     */
    public function allWithTrashed() {
        return \Package::onlyOwners()->withTrashed()->with(
            'tickets',
            'courses',
                'courses.trainings',
                'courses.tickets',
            'accommodations',
            'addons',
            'basePrices',
            'prices'
            )->get();
    }

    /**
     * Get an package for a company from its id
     * @param  int   ID of the package
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an package for a company
     */
    public function get($id) {
        return \Package::onlyOwners()->with(
            'tickets',
            'courses',
                'courses.trainings',
                'courses.tickets',
            'accommodations',
            'addons',
            'basePrices',
            'prices'
            )->findOrFail($id);
    }

    /**
     * Get an package for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the package
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an package for a company
     */
    public function getWhere($column, $value) {
        return \Package::onlyOwners()->where($column, $value)->with(
            'tickets',
            'courses',
                'courses.trainings',
                'courses.tickets',
            'accommodations',
            'addons',
            'basePrices',
            'prices'
        )->get();
    }

    public function getAvailable() {
        $now = Helper::localTime();
        $now = $now->format('Y-m-d');

        return \Package::onlyAvailable()
            ->where(function($query) use ($now)
            {
                $query->whereNull('available_from')->orWhere('available_from', '<=', $now);
            })
            ->where(function($query) use ($now)
            {
                $query->whereNull('available_until')->orWhere('available_until', '>=', $now);
            })
            ->with(
            'tickets',
            'courses',
                'courses.trainings',
                'courses.tickets',
            'accommodations',
            'addons',
            'basePrices',
            'prices'
        )->get();
    }

    private function associateTickets($package, $tickets) 
    {
        if($tickets && !empty($tickets))
        {
            try {
                $package->tickets()->sync( $tickets ); 
            } 
            catch(\Exception $e) {
                throw new BadRequestException(
                    ['Their was a problem associating the tickets array, please ensure it meets the API requirements']);
            }
        }
    }

    private function associateCourses($package, $courses) 
    {
        if($courses && !empty($courses))
        {
            try {
                $package->courses()->sync($courses); 
            } 
            catch(\Exception $e) {
                throw new BadRequestException(
                    ['Their was a problem associating the courses array, please ensure it meets the API requirements']);
            }
        }
    }

    private function associateAccommodations($package, $accommodations) 
    {
        if($accommodations && !empty($accommodations))
        {
            try {
                $package->accommodations()->sync($accommodations); 
            } 
            catch(\Exception $e) {
                throw new BadRequestException(
                    ['Their was a problem associating the accommodations array, please ensure it meets the API requirements']);
            }
        }
    }

    private function associateAddons($package, $addons) 
    {
        if($addons && !empty($addons))
        {
            try {
                $package->addons()->sync($addons); 
            } 
            catch(\Exception $e) {
                throw new BadRequestException(
                    ['Their was a problem associating the addons array, please ensure it meets the API requirements']);
            }
        }
    }

    /**
     * Create an package and associate it with its company.
     * In addition, associate it with all of its 'packageables' such as addons,
     * accommodations, tickets and courses
     * @param array Information about the package to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an package for a company
     */
    public function create($data, $tickets, $courses, $accommodations, $addons) 
    {
        $package = new \Package($data);
        if (!$package->validate()) {
            throw new InvalidInputException($package->errors()->all());
        }
        $package = $this->company_model->packages()->save($package);
        $this->associateTickets($package, $tickets);
        $this->associateCourses($package, $courses);
        $this->associateAddons($package, $addons);
        $this->associateAccommodations($package, $accommodations);
        return $package;
    }

    /**
     * Update an package by id with specified data
     * @param  int   ID of the package
     * @param  array Data to update the package with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an package for a company
     */
    public function update($id, $data, $tickets, $courses, $accommodations, $addons) 
    {
        $package = $this->get($id);
        if(!$package->update($data)) {
            throw new InvalidInputException($package->errors()->all());
        }
        $this->associateTickets($package, $tickets);
        $this->associateCourses($package, $courses);
        $this->associateAddons($package, $addons);
        $this->associateAccommodations($package, $accommodations);
        
        return $package;
    }

    /**
     * Delete an package by its id
     * @param  int ID of the package
     * @throws Exception
     */
    public function delete($id) {
        $package = $this->get($id);
        $package->delete();
    }

    /**
     * Delete an package by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the package
     * @throws Exception
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     */
    public function deleteWhere($column, $value) {
        $package = $this->getWhere($column, $value);
        $package->delete();
    }

    public function begin() 
    {
        return \DB::beginTransaction();
    }

    public function undo()
    {
        return \DB::rollback();
    }

    public function finish()
    {
        return \DB::commit();
    }
}