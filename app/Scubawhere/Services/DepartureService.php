<?php

namespace Scubawhere\Services;

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Services\LogService;
use Scubawhere\Entities\Departure;
use Scubawhere\Exceptions\ConflictException;
use Scubawhere\Exceptions\BadRequestException;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\RepositoriesDepartureRepoInterface;

class DepartureService {

	/** 
	 *	Repository to access the departure models
	 *	@var \Scubawhere\Repositories\DepartureRepo
	 */
	protected $departure_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;


	public function __construct(DepartureRepoInterface $departure_repo, LogService $log_service) {
		$this->departure_repo = $departure_repo;
		$this->log_service = $log_service;
	}

	/**
     * Get an departure for a company from its id
     * @param int ID of the departure
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an departure for a company
     */
	public function get($id) {
		return $this->departure_repo->get($id, ['trip', 'boat']);
	}

	/**
     * Get all departures for a company
     * @param int ID of the departure
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all departures for a company
     */
	public function getAll() {
		return $this->departure_repo->all(['trip', 'boat']);
	}

	/**
     * Get all departures for a company including soft deleted models
     * @param int ID of the departure
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all departures for a company including soft deleted models
     */
	public function getAllWithTrashed() {
		return $this->departure_repo->allWithTrashed(['trip', 'boat']);
	}

	/**
	 * Validate, create and save the departure and prices to the database
	 * @param  array Data to autofill departure model
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model for the departure
	 */
	public function create($data) 
	{
		return $this->departure_repo->create($data);
	}

	/**
	 * Validate, update and save the departure and prices to the database
	 * @param  int   $id           ID of the departure
	 * @param  array $data         Information about departure
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model of the departure
	 */
	public function update($id, $data) 
	{
    	return $this->departure_repo->update($id, $data);
	}

	/**
	 * Remove the departure from the database.
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the departure, and the booking ids are then logged
	 * @throws \Scubawhere\Exceptions\ConflictException
	 * @throws Exception
	 * @param  int $id ID of the departure
	 */
	public function delete($id)
	{
		
	}

}