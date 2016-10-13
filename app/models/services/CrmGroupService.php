<?php

namespace ScubaWhere\Services;

use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use ScubaWhere\Exceptions\ConflictException;
use ScubaWhere\Exceptions\BadRequestException;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\CrmGroupRepoInterface;
use ScubaWhere\Repositories\CrmGroupRuleRepoInterface;

class CrmGroupService {

	/** 
	 *	Repository to access the crmgroup models
	 *	@var \ScubaWhere\Repositories\CrmGroupRepo
	 */
	protected $crm_group_repo;

	/** 
	 *	Repository to access the CrmGroupRule models
	 *	@var \ScubaWhere\Repositories\CrmGroupRuleRepo
	 */
	protected $crm_group_rule_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * @var \ScubaWhere\Services\LogService
	 */
	protected $log_service;

	/**
	 * @param CrmGroupRepoInterface     Injected using \ScubaWhere\Repositories\CrmGroupRepoServiceProvider
	 * @param LogService                Injected using laravel's IOC container
	 * @param CrmGroupRuleRepoInterface Injected using \ScubaWhere\Repositories\CrmGroupRuleRepoServiceProvider
	 */
	public function __construct(CrmGroupRepoInterface $crm_group_repo,
								LogService $log_service,
								CrmGroupRuleRepoInterface $crm_group_rule_repo) 
	{
		$this->crm_group_repo      = $crm_group_repo;
		$this->log_service         = $log_service;
		$this->crm_group_rule_repo = $crm_group_rule_repo;
	}

	/**
     * Get an CrmGroup for a company from its id
     * @param int ID of the crmgroup
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \CrmGroup
     */
	public function get($id) {
		return $this->crm_group_repo->get($id);
	}

	/**
     * Get all CrmGroups for a company
     * @param int ID of the CrmGroup
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->crm_group_repo->all();
	}

	/**
     * Get all crmgroups for a company including soft deleted models
     * @param int ID of the crmgroup
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->crm_group_repo->allWithTrashed();
	}

	/**
	 * @todo This function is horrifically expensive. During first round of refactoring we need to :
	 * 1. Use join instead of eager loading customer and token relationships
	 * 2. Use mysql to use aggregate functions such as count instead of returning all of the tokens
	 * 3. Use mysql to aggregate the customers so we do not require php to filter through unique customers
	 * 4. Seperate the logic of retrieving tokens and customers to reuse this function when generating customer list for campaigns
	*/
	public function getCustomerAnalytics($group_id) 
	{
        $customers = [];
		$tmpRules = [];
		$rules = [];
		$rules['certs'] = [];
		$rules['classes'] = [];
		$rules['tickets'] = [];

		// FORMAT RULES INTO IDS TO FILTER THROUGH
        $group = Context::get()->crmGroups()->where('id', '=', $group_id)->with('rules')->first();
        $tmpRules = $group->rules;
        foreach ($tmpRules as $rule) {
            // Translate agency to certification ids
            if($rule->agency_id !== null) {
                $agency = \Agency::with('certificates')->findOrFail( $rule->agency_id );
                $certs = $agency->certificates;
                foreach ($certs as $cert) {
                    array_push($rules['certs'], $cert->id); // ->id
                }
            }
            else if($rule->certificate_id !== null) {
                array_push($rules['certs'], $rule->certificate_id);
            }
            else if($rule->training_id !== null) {
                array_push($rules['classes'], $rule->training_id);
            }
            else if($rule->ticket_id !== null) {
                array_push($rules['tickets'], $rule->ticket_id);
            }
        }

		$certificates_customers = Context::get()->customers()
			->with('tokens')
			->whereHas('certificates', function($query) use ($rules){
				$query->whereIn('certificates.id', $rules['certs']);
			})
			->get()//;->lists('email', 'firstname', 'lastname', 'id');
			->map(function($obj) {
				$data = array();
				$num_sent = 0;
				$num_read = 0;
				foreach($obj->tokens as $token) 
				{
					$num_sent++;
					if($token->opened > 0) $num_read++;
				}
				$data['num_sent']    = $num_sent;
				$data['num_read']    = $num_read;
				$data['firstname']   = $obj->firstname;
				$data['id']   		 = $obj->id;
				$data['lastname']    = $obj->lastname;
				$data['email']       = $obj->email;
				$data['unsubscribe'] = $obj->unsubscribed;
				$data['opened_rate'] = $num_sent != 0 ? ($num_read / $num_sent) * 100 : 0; 
				return $data;
			})
			->toArray();

		$customers = $certificates_customers;

		$booked_customers = Context::get()->bookingdetails()
			->whereHas('booking', function($query) {
				$query->whereIn('status', \Booking::$counted); 
			})
			->whereIn('ticket_id', $rules['tickets'])
			->orWhereIn('training_id', $rules['classes'])
			->with('customer.tokens')
			/*->with(['customer' => function($q) {
				$q->select('firstname', 'lastname', 'email');
			},
			'customer.tokens' => function($q) {
				$q->select(DB::raw('count(opened)'));	
			}])*/
			->get()
			->map(function($obj) {
				$data = array();
				$num_sent = 0;
				$num_read = 0;
				foreach($obj->customer->tokens as $token) 
				{
					$num_sent++;
					if($token->opened > 0) $num_read++;
				}
				$data['num_sent']    = $num_sent;
				$data['num_read']    = $num_read;
				$data['firstname']   = $obj->customer->firstname;
				$data['lastname']    = $obj->customer->lastname;
				$data['id']			 = $obj->customer->id;
				$data['email']       = $obj->customer->email;
				$data['unsubscribe'] = $obj->customer->unsubscribed;
				$data['opened_rate'] = $num_sent != 0 ? ($num_read / $num_sent) * 100 : 0; 
				return $data;
			})
			->toArray();

		$customers = array_merge($customers, $booked_customers);
		$customers = array_unique($customers, SORT_REGULAR);
		$customers = array_filter($customers, function($obj) { return !$obj['unsubscribe']; });

		return $customers;
	}

	/**
	 * Validate, create and save the crmgroup and prices to the database
	 * @param  array Data to autofill crmgroup model
	 * @return \CrmGroup
	 */
	public function create($data, $trainings, $tickets, $certificates, $agencies) 
	{
		$group = $this->crm_group_repo->create($data);
		$this->associateRules($group, 'training_id', $trainings);
		$this->associateRules($group, 'ticket_id', $tickets);
		$this->associateRules($group, 'certificate_id', $certificates);
		$this->associateRules($group, 'agency_id', $agencies);
		return $group;
	}

	/**
	 * Associate a customer group with a rule used to filter customers when generating the group.
	 * A rule can either be a foreign key for the id of a ticket, training, certificate or agency.
	 * @param \CrmGroup $group   The CrmGroup to associate to
	 * @param string    $pivot   The name of the foreign key column
	 * @param array     $targets The IDs to associate to the group
	 */
	public function associateRules($group, $pivot, $targets) 
	{
		$rules = array();
		foreach ($targets as $target) 
		{
			$rule_data = array($pivot => (int) $target);
			$rule = $this->crm_group_rule_repo->create($rule_data);
			array_push($rules, $rule);
		}	
		if(!empty($rules)) $group->rules()->saveMany($rules);
	}

	/**
	 * Validate, update and save the crmgroup and prices to the database
	 * @param  int   $id           ID of the crmgroup
	 * @param  array $data         Information about crmgroup
	 * @return \CrmGroup
	 */
	public function update($id, $data, $trainings, $tickets, $certificates, $agencies) 
	{
    	$group = $this->crm_group_repo->update($id, $data);
    	// @todo Instead of deleting the current rules and replacing them, filter through
    	// and compare them then add / remove the diffrence
    	$group->rules()->delete();
    	$this->associateRules($group, 'training_id', $trainings);
    	$this->associateRules($group, 'ticket_id', $tickets);
    	$this->associateRules($group, 'certificate_id', $certificates);
    	$this->associateRules($group, 'agency_id', $agencies);
    	return $group;
	}

	/**
	 * Remove the crmgroup from the database.
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the crmgroup, and the booking ids are then logged
	 * @throws \ScubaWhere\Exceptions\ConflictException
	 * @throws \Exception
	 * @param  int ID of the CrmGroup
	 */
	public function delete($id)
	{
		$group = $this->crm_group_repo->get($id);

		try {
			$group->forceDelete();
		}
		catch(QueryException $e) {
			// @note Do I need to retrieve the group again?
			$group = $this->crm_group_repo->get($id);
			$group->delete();
		}	
	}

}