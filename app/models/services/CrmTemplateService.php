<?php

namespace ScubaWhere\Services;

use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use ScubaWhere\Exceptions\ConflictException;
use ScubaWhere\Exceptions\BadRequestException;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\CrmTemplateRepoInterface;

class CrmTemplateService {

	/** 
	 *	Repository to access the crmtemplate models
	 *	@var \ScubaWhere\Repositories\CrmTemplateRepo
	 */
	protected $crm_template_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * @var \ScubaWhere\Services\LogService
	 */
	protected $log_service;

	/**
	 * @param CrmTemplateRepoInterface     Injected using \ScubaWhere\Repositories\CrmTemplateRepoServiceProvider
	 * @param LogService                   Injected using laravel's IOC container
	 */
	public function __construct(CrmTemplateRepoInterface $crm_template_repo, LogService $log_service) {
		$this->crm_template_repo = $crm_template_repo;
		$this->log_service = $log_service;
	}

	/**
     * Get an crmtemplate for a company from its id
     * @param int ID of the CrmTemplate
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \CrmTemplate
     */
	public function get($id) {
		return $this->crm_template_repo->get($id);
	}

	/**
     * Get all CrmTemplates for a company
     * @param int ID of the crmtemplate
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all crmtemplates for a company
     */
	public function getAll() {
		return $this->crm_template_repo->all();
	}

	/**
     * Get all crmtemplates for a company including soft deleted models
     * @param int ID of the crmtemplate
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all crmtemplates for a company including soft deleted models
     */
	public function getAllWithTrashed() {
		return $this->crm_template_repo->allWithTrashed();
	}

	/**
	 * Validate, create and save the crmtemplate and prices to the database
	 * @param  array Data to autofill crmtemplate model
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model for the crmtemplate
	 */
	public function create($data) 
	{
		return $this->crm_template_repo->create($data);
	}

	/**
	 * Validate, update and save the crmtemplate and prices to the database
	 * @param  int   $id           ID of the crmtemplate
	 * @param  array $data         Information about crmtemplate
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model of the crmtemplate
	 */
	public function update($id, $data) 
	{
    	return $this->crm_template_repo->update($id, $data);
	}

	/**
	 * Remove the crmtemplate from the database.
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the crmtemplate, and the booking ids are then logged
	 * @throws \ScubaWhere\Exceptions\ConflictException
	 * @throws Exception
	 * @param  int $id ID of the crmtemplate
	 */
	public function delete($id)
	{
		
	}

}