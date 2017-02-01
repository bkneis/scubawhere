<?php

namespace Scubawhere\Services;

use Scubawhere\Entities\Addon;
use Scubawhere\Entities\Ticket;
use Scubawhere\Repositories\AgentRepoInterface;

class AgentService {

	/** @var \Scubawhere\Repositories\AgentRepo */
	protected $agent_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 *
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;

	public function __construct(AgentRepoInterface $agent_repo, LogService $log_service) {
		$this->agent_repo = $agent_repo;
		$this->log_service = $log_service;
	}

	/**
     * Get an agent for a company from its id
	 *
     * @param int $id ID of the agent
	 *
     * @return \Scubawhere\Entities\Agent
     */
	public function get($id) {
		return $this->agent_repo->get($id);
	}

	/**
     * Get all agents for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->agent_repo->all();
	}

	/**
     * Get all agents for a company including soft deleted models
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->agent_repo->allWithTrashed();
	}

	/**
	 * Validate, create and save the agent and prices to the database
	 *
	 * @param array $data
	 *
	 * @return \Scubawhere\Entities\Agent
	 */
	public function create($data) 
	{
		$agent = $this->agent_repo->create($data);
		foreach ($data['rules'] as $rule) {
			switch($rule['type']) {
				case ('ticket'):
					$rule['type'] = Ticket::class;
					break;
				case ('course'):
					$rule['type'] = Course::class;
					break;
				case ('package'):
					$rule['type'] = Package::class;
					break;
				case ('addon'):
					$rule['type'] = Addon::class;
					break;
			}
		}
		return $agent->syncCommissionRules($data['rules']);
	}

	/**
	 * Validate, update and save the agent and prices to the database
	 *
	 * @param  int   $id    ID of the agent
	 * @param  array $data  Information about agent
	 *
	 * @return \Scubawhere\Entities\Agent
	 */
	public function update($id, $data) 
	{
    	return $this->agent_repo->update($id, $data);
	}

	/**
	 * Remove the agent from the database.
	 *
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the agent, and the booking ids are then logged.
	 *
	 * @param int $id ID of the agent
	 */
	public function delete($id)
	{
		$this->agent_repo->delete($id);
	}

}