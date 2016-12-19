<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Entities\Addon;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;
use Scubawhere\Exceptions\InvalidInputException;

/**
 * Class AddonRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\AddonRepoInterface
 */
class AddonRepo extends BaseRepo implements AddonRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     *
     * @var \ScubaWhere\Entities\Company
     */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all addons for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return Addon::with($relations)->get();
    }

    /**
     * Get all addons for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) {
        return Addon::with($relations)->withTrashed()->get();
    }

    /**
     * Get an addon for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Scubawhere\Entities\Addon
     */
    public function get($id, array $relations = [], $fail = true) {
        $addon = Addon::with($relations)->find($id);

        if($addon === null && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The addon could not be found']);
        }

        return $addon;
    }

    /**
     * Get an addon for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \ScubaWhere\Entities\Addon
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $addon = Addon::where($query)->with($relations)->get();

        if($addon === null && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The addon could not be found']);
        }

        return $addon;
    }

    /**
     * Get an addon that is used within bookings in the future.
     *
     * Addons are not directly related to bookings so we need to go through the booking's
     * details. Here we use the belongsToMany relationship on addon to bookingdetails. Then
     * we check if there are any sessions or training sessions past the localtime.
     *
     * @param int  $id
     * @param bool $fail
     *
     * @throws HttpNotFound
     *
     * @return \Scubawhere\Entities\Addon
     */
    public function getWithFutureBookings($id, $fail = true)
    {
        $addon = Addon::with([
                'bookingdetails.session' => function($q) {
                    $q->where('start', '>=', Helper::localtime());
                },
                'bookingdetails.training_session' => function($q) {
                    $q->where('start', '>=', Helper::localtime());
                }])
            ->find($id);

        if(is_null($addon) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The addon could not be found']);
        }

        return $addon;
    }

    /**
     * Create an addon and associate it with its company
     *
     * @param array $data Information about the addon to save
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     * @deprecated 
     *
     * @return \ScubaWhere\Entities\Addon
     */
    public function create(array $data) {
        $addon = new Addon($data);

        if (!$addon->validate()) {
            throw new InvalidInputException($addon->errors()->all());
        }

        return $this->company_model->addons()->save($addon);
    }

    /**
     * Update an addon
     *
     * @param int   $id   ID of the addon
     * @param array $data Information about the addon to update
     * @param bool  $fail Whether to fail or not
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotAcceptable
     * @deprecated 
     *
     * @return \ScubaWhere\Entities\Addon
     */
    public function update($id, array $data, $fail = true) {
        $addon = $this->get($id);

        if(!$addon->update($data) && $fail) {
            throw new HttpNotAcceptable(__CLASS__ . __METHOD__, [$addon->errors()->all()]);
        }

        return $addon;
    }

}
