<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Helper;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Package;
use Scubawhere\Exceptions\Http\HttpNotFound;

/**
 * Class PackageRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see EloquentRepo
 * @see PackageRepoInterface
 */
class PackageRepo extends EloquentRepo implements PackageRepoInterface {

    public function __construct()
    {
        parent::__construct(Package::class);
    }
    
    /**
     * Get all packages that are available for the current local time.
     * 
     * Packages can have an 'available_from' and 'available_until' timestamp
     * where the package can only be booked between these periods.
     * 
     * @return mixed
     */
    public function getAvailable() 
    {
        $now = Helper::localTime();
        $now = $now->format('Y-m-d');

        return Package::where(function($query) use ($now) {
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
     * @throws HttpNotFound
     * @return mixed
     */
    public function getUsedInFutureBookings($id, $fail = true)
    {
        $package = Package::with(['bookingdetails.session' => function($q) {
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

}

