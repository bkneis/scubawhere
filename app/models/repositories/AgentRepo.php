<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Repositories\AgentRepoInterface;
use ScubaWhere\Exceptions\InvalidInputException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AgentRepo extends BaseRepo implements AgentRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all agents for a company
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all agents for a company
     */
    public function all() {
        return \Agent::onlyOwners()->get();
    }

    /**
     * Get all agents for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all agents for a company including soft deleted models
     */
    public function allWithTrashed() {
        return \Agent::onlyOwners()->withTrashed()->get();
    }

    /**
     * Get an agents for a company from its id
     * @param  int   ID of the agent
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Agent
     */
    public function get($id) {
        return \Agent::onlyOwners()->findOrFail($id);
    }

    /**
     * Get an agent for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the agent
     * @return \Agent
     */
    public function getWhere($column, $value) {
        return \Agent::onlyOwners()->where($column, '=', $value)->get();
    }

    /**
     * Get an agent for a company with specified relationships
     * @param  int    ID of the agent
     * @param  array  Relationships to retrieve with the model
     * @return \Agent
     */
    public function getWith($id, $relations) {
        return \Agent::onlyOwners()->with($relations)->findOrFail($id);
    }

    /**
     * Create an agent and associate it with its company
     * @param array Information about the agent to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Agent
     */
    public function create($data) {
        $agent = new \Agent($data);
        if (!$agent->validate()) {
            throw new InvalidInputException($agent->errors()->all());
        }
        return Context::get()->agents()->save($agent);
    }

    /**
     * Update an agent by id with specified data
     * @param  int   ID of the agent
     * @param  array Data to update the agent with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Agent
     */
    public function update($id, $data) {
        $agent = $this->get($id);
        if(!$agent->update($data)) {
            throw new InvalidInputException($agent->errors()->all());
        }
        return $agent;
    }

    /**
     * Delete an agent by its id
     * @param  int ID of the agent
     * @throws Exception
     */
    public function delete($id) {
        $agent = $this->get($id);
        $agent->delete();
    }

    /**
     * Delete an agent by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the agent
     * @throws Exception
     */
    public function deleteWhere($column, $value) {
        $agent = $this->getWhere($column, $value);
        $agent->delete();
    }
}