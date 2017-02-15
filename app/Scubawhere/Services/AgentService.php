<?php

namespace Scubawhere\Services;

use Scubawhere\Entities\Accommodation;
use Scubawhere\Entities\Addon;
use Scubawhere\Entities\Ticket;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;
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
		return $this->agent_repo->all(['commissionRules']);
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
		$rules = $this->transformCommissionRules($data['commission_rules']);
		unset($data['commission_rules']);
		return $this->agent_repo
			->create($data)
			->syncCommissionRules($rules);
	}
	
	private function isDefault($str)
	{
		return preg_match('/-default/', $str) ? true : false;
	}
	
	private function transformCommissionRules(array $rules)
	{
		foreach ($rules as &$rule) {
			// Nullify the owner_id if its the default so that the integer
			// validation can pass
			if (/*$this->isDefault($rule['owner_id'])*/ $rule['owner_id'] === 'default') {
				$rule['owner_id'] = null;
			}
			// If the agent receives commission via a total sum, e.g.
			// $50 instead of 20% of the trip, save the amount to the
			// commission_value attribute and multiply by 100, e.g.
			// save 500 as $5 so that you dont have doubles in the db
			if ($rule['unit'] !== 'percentage') {
				$rule['commission_value'] = (int) ((double) $rule['commission'] * 100);
				unset($rule['commission']);
			}
			switch($rule['owner_type']) {
				case ('ticket'):
					$rule['owner_type'] = Ticket::class;
					break;
				case ('course'):
					$rule['owner_type'] = Course::class;
					break;
				case ('package'):
					$rule['owner_type'] = Package::class;
					break;
				case ('addon'):
					$rule['owner_type'] = Addon::class;
					break;
				case ('accommodation'):
					$rule['owner_type'] = Accommodation::class;
					break;
				default:
					throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Unknown bookable type']);
					break;
			}
		}
		return $rules;
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
		$rules = $this->transformCommissionRules($data['commission_rules']);
		unset($data['commission_rules']);
    	return $this->agent_repo
			->update($id, $data)
			->syncCommissionRules($rules);
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