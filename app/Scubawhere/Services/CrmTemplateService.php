<?php

namespace Scubawhere\Services;

use Scubawhere\Repositories\CrmTemplateRepoInterface;

class CrmTemplateService {

	/** @var \Scubawhere\Repositories\CrmTemplateRepo */
	protected $crm_template_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;


	public function __construct(CrmTemplateRepoInterface $crm_template_repo, LogService $log_service) {
		$this->crm_template_repo = $crm_template_repo;
		$this->log_service = $log_service;
	}

	/**
     * Get an crmtemplate for a company from its id
	 *
     * @param int $id ID of the CrmTemplate
	 *
     * @return \Scubawhere\Entities\CrmTemplate
     */
	public function get($id) {
		return $this->crm_template_repo->get($id);
	}

	/**
     * Get all CrmTemplates for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->crm_template_repo->all();
	}

	/**
     * Get all crmtemplates for a company including soft deleted models
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->crm_template_repo->allWithTrashed();
	}

	/**
	 * Validate, create and save the crmtemplate and prices to the database
	 *
	 * @param array $data Data to autofill crmtemplate model
	 *
	 * @return \Scubawhere\Entities\CrmTemplate
	 */
	public function create($data) 
	{
		return $this->crm_template_repo->create($data);
	}

	/**
	 * Validate, update and save the crmtemplate and prices to the database
	 *
	 * @param  int   $id           ID of the crmtemplate
	 * @param  array $data         Information about crmtemplate
	 *
	 * @return \Scubawhere\Entities\CrmTemplate
	 */
	public function update($id, $data) 
	{
    	return $this->crm_template_repo->update($id, $data);
	}

	/**
	 * Remove the crmtemplate from the database.
	 *
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the crmtemplate, and the booking ids are then logged
	 *
	 * @throws \Scubawhere\Exceptions\ConflictException
	 * @throws \Exception
	 *
	 * @param  int $id ID of the crmtemplate
	 */
	public function delete($id)
	{
		
	}

}