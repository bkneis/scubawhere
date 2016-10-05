<?php

namespace ScubaWhere\Services;

use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use ScubaWhere\Exceptions\ConflictException;
use ScubaWhere\Exceptions\BadRequestException;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\AgentRepoInterface;

class AgentService {

	/** 
	 *	Repository to access the agent models
	 *	\ScubaWhere\Repositories\AgentRepo
	 */
	protected $agent_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * \ScubaWhere\Services\LogService
	 */
	protected $log_service;

	/**
	 * @param AgentRepoInterface Injected using \ScubaWhere\Repositories\AgentRepoServiceProvider
	 * @param LogService                 Injected using laravel's IOC container
	 */
	public function __construct(AgentRepoInterface $agent_repo, LogService $log_service) {
		$this->agent_repo = $agent_repo;
		$this->log_service = $log_service;
	}

	/**
     * Get an agent for a company from its id
     * @param int ID of the agent
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an agent for a company
     */
	public function get($id) {
		return $this->agent_repo->get($id);
	}

	/**
     * Get all agents for a company
     * @param int ID of the agent
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all agents for a company
     */
	public function getAll() {
		return $this->agent_repo->all();
	}

	/**
     * Get all agents for a company including soft deleted models
     * @param int ID of the agents
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all agents for a company including soft deleted models
     */
	public function getAllWithTrashed($id) {
		return $this->agent_repo->allWithTrashed();
	}

	/**
	 * Validate, create and save the agent and prices to the database
	 * @param  [type] $data        [description]
	 * @param  [type] $base_prices [description]
	 * @param  [type] $prices      [description]
	 * @return [type]              [description]
	 */
	public function create($data) 
	{
		return $this->agent_repo->create($data);
	}

	/**
	 * Validate, update and save the agent and prices to the database
	 * @param  int   $id           ID of the agent
	 * @param  array $data         Information about agent
	 * @param  array $base_prices  Prices to associate to the agent model
	 * @param  array $prices       Seasonal prices to 
	 * @return [type]              [description]
	 */
	public function update($id, $data) 
	{
    	return $this->agent_repo->update($id, $data);
	}

	/**
	 * Remove the agent from the database.
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the agent, and the booking ids are then logged
	 * @throws \ScubaWhere\Exceptions\ConflictException
	 * @throws Exception
	 * @param  int $id ID of the agent
	 */
	public function delete($id)
	{
		$this->agent_repo->delete($id);
	}

}