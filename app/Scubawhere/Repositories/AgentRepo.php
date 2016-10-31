<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Agent;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;

/**
 * Class AgentRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\AgentRepoInterface
 */
class AgentRepo extends BaseRepo implements AgentRepoInterface {

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
     * Get all agents for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) {
        return Agent::onlyOwners()->with($relations)->get();
    }

    /**
     * Get all agents for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) {
        return Agent::onlyOwners()->withTrashed()->with($relations)->get();
    }

    /**
     * Get an agents for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws HttpNotFound
     *
     * @return \Scubawhere\Entities\Agent
     */
    public function get($id, array $relations = [], $fail = true) {
        $agent = Agent::onlyOwners()->with($relations)->find($id);

        if($agent === null && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The agent could not be found']);
        }

        return $agent;
    }

    /**
     * Get an agent for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws HttpNotFound
     *
     * @return \Scubawhere\Entities\Agent
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $agent = Agent::onlyOwners()->where($query)->with($relations)->find();

        if($agent === null && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The agent could not be found']);
        }

        return $agent;
    }

    /**
     * Create an agent and associate it with its company
     *
     * @param array $data Information about the agent to save
     *
     * @throws HttpNotAcceptable
     *
     * @return \Scubawhere\Entities\Agent
     */
    public function create($data) {
        $agent = new Agent($data);

        if (!$agent->validate()) {
            throw new HttpNotAcceptable(__CLASS__.__METHOD__, $agent->errors()->all());
        }

        return Context::get()->agents()->save($agent);
    }

    /**
     * Update an agent
     *
     * @param int   $id   ID of the addon
     * @param array $data Information about the addon to update
     * @param bool  $fail Whether to fail or not
     *
     * @throws HttpNotAcceptable
     *
     * @return \ScubaWhere\Entities\Agent
     */
    public function update($id, array $data, $fail = true) {
        $addon = $this->get($id, $fail);

        if(!$addon->update($data)) {
            throw new HttpNotAcceptable(__CLASS__ . __METHOD__, [$addon->errors()->all()]);
        }

        return $addon;
    }

    /**
     * Delete an agent by their ID
     *
     * @param $id
     *
     * @throws HttpNotFound
     * @throws \Exception
     */
    public function delete($id)
    {
        $agent = $this->get($id);
        $agent->delete();
    }

}
