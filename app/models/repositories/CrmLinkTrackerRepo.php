<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\CrmLinkTrackerRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrmLinkTrackerRepo implements CrmLinkTrackerRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all CrmLinkTrackers for a company
     * @throws \ScubaWhere\Exceptions\MethodNotSupported
     */
    public function all() {
        throw new MethodNotSupportedException();
    }

    /**
     * Get all CrmLinkTrackers for a company including soft deleted models
     * @throws \ScubaWhere\Exceptions\MethodNotSupported
     */
    public function allWithTrashed() {
        throw new MethodNotSupportedException();
    }

    /**
     * Get an CrmLinkTracker for a company from its id
     * @param  int   ID of the CrmLinkTracker
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \CrmLinkTracker
     */
    public function get($id) {
        return \CrmLinkTracker::findOrFail($id);
    }

    /**
     * Get an CrmLinkTracker for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the CrmLinkTracker
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere($query) {
        return \CrmLinkTracker::where($query)->get();
    }

    /**
     * Create an CrmLinkTracker and associate it with its company
     * @param array Information about the CrmLinkTracker to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmLinkTracker
     */
    public function create($data) {
        $crm_link_tracker = new \CrmLinkTracker($data);
        if (!$crm_link_tracker->validate()) {
            throw new InvalidInputException($crm_link_tracker->errors()->all());
        }
        return $crm_link_tracker->save();
    }

    /**
     * Update an CrmLinkTracker by id with specified data
     * @param  int   ID of the CrmLinkTracker
     * @param  array Data to update the CrmLinkTracker with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \CrmLinkTracker
     */
    public function update($id, $data) {
        $crm_link_tracker = $this->get($id);
        if(!$crm_link_tracker->update($data)) {
            throw new InvalidInputException($crm_link_tracker->errors()->all());
        }
        return $crm_link_tracker;
    }

    /**
     * Delete an CrmLinkTracker by its id
     * @param  int ID of the CrmLinkTracker
     * @throws \Exception
     */
    public function delete($id) {
        $crm_link_tracker = $this->get($id);
        $crm_link_tracker->delete();
    }

    /**
     * Delete an CrmLinkTracker by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the CrmLinkTracker
     * @throws \Exception
     */
    public function deleteWhere($query) {
        $crm_link_tracker = $this->getWhere($query);
        $crm_link_tracker->delete();
    }
}