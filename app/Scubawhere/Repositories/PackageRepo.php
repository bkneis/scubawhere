<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Package;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpBadRequest;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;

/**
 * Class PackageRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\PackageRepoInterface
 */
class PackageRepo extends BaseRepo implements PackageRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     *
     * @var \Scubawhere\Entities\Company
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all packages for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return Package::onlyOwners()->with($relations)->get();
        /*return Package::onlyOwners()->with(
            'tickets',
            'courses',
                'courses.trainings',
                'courses.tickets',
            'accommodations',
            'addons',
            'basePrices',
            'prices'
            )->get();*/
    }

    /**
     * Get all packages for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) {
        return Package::onlyOwners()->with($relations)->withTrashed()->get();
    }

    /**
     * Get an package for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function get($id, array $relations = [], $fail = true) {
        $package = Package::onlyOwners()->with($relations)->find($id);

        if(is_null($package) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The package could not be found']);
        }

        return $package;
    }

    /**
     * Get an package for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $package = Package::onlyOwners()->where($query)->with($relations)->find();

        if(is_null($package) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The package could not be found']);
        }

        return $package;
    }

    public function getAvailable() {
        $now = Helper::localTime();
        $now = $now->format('Y-m-d');

        return Package::onlyOwners()
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

    /**
     * Get a package any any booking details associated to it that are booked for the future
     *
     * @param $id
     * @param bool $fail
     *
     * @throws HttpNotFound
     *
     * @return mixed
     */
    public function getUsedInFutureBookings($id, $fail = true)
    {
        $package = Package::onlyOwners()
            ->with(['bookingdetails.session' => function($q) {
                $q->where('start', '>=', Helper::localtime());
            },
                'bookingdetails.training_session' => function($q) {
                    $q->where('start', '>=', Helper::localtime());
                }
            ])
            ->find( $id );

        if(is_null($package) && $fail) {
            throw new HttpNotFound(__CLASS__.__METHOD__, ['The package could not be found']);
        }

        return $package;
    }

    private function associateTickets($package, $tickets) 
    {
        if($tickets && !empty($tickets))
        {
            try {
                $package->tickets()->sync( $tickets ); 
            } 
            catch(\Exception $e) {
                throw new HttpBadRequest(__CLASS__.__METHOD__,
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
                throw new HttpBadRequest(__CLASS__.__METHOD__,
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
                throw new HttpBadRequest(__CLASS__.__METHOD__,
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
                throw new HttpBadRequest(__CLASS__.__METHOD__,
                    ['Their was a problem associating the addons array, please ensure it meets the API requirements']);
            }
        }
    }

    /**
     * Create an package and associate it with its company.
     *
     * In addition, associate it with all of its 'packageables' such as addons,
     * accommodations, tickets and courses
     *
     * @param array $data Information about the package to save
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \Scubawhere\Entities\Package
     */
    public function create(array $data, array $tickets, array $courses, array $accommodations, array $addons)
    {
        $package = new Package($data);
        if (!$package->validate()) {
            throw new HttpNotAcceptable(__CLASS__.__METHOD__, $package->errors()->all());
        }

        $package = $this->company_model->packages()->save($package);

        $this->associateTickets($package, $tickets);
        $this->associateCourses($package, $courses);
        $this->associateAddons($package, $addons);
        $this->associateAccommodations($package, $accommodations);

        return $package;
    }

    /**
     * Update a package and associate it with all its relations
     *
     * In addition, associate it with all of its 'packageables' such as addons,
     * accommodations, tickets and courses
     *
     * @param array $data Information about the package to save
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     *
     * @return \Scubawhere\Entities\Package
     */
    public function update($id, array $data, array $tickets, array $courses, array $accommodations, array $addons, $fail = true)
    {
        $package = $this->get($id);

        if(!$package->update($data)) {
            throw new HttpNotAcceptable(__CLASS__.__METHOD__, $package->errors()->all());
        }

        $this->associateTickets($package, $tickets);
        $this->associateCourses($package, $courses);
        $this->associateAddons($package, $addons);
        $this->associateAccommodations($package, $accommodations);

        return $package;
    }

}

