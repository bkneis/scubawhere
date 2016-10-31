<?php

namespace Scubawhere\Services;

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Entities\Training;
use Scubawhere\Exceptions\Http\HttpConflict;
use Scubawhere\Repositories\TrainingRepoInterface;

class TrainingService {

	/** @var \Scubawhere\Repositories\TrainingRepo */
	protected $training_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;

	public function __construct(TrainingRepoInterface $training_repo, LogService $log_service) {
		$this->training_repo = $training_repo;
		$this->log_service = $log_service;
	}

	/**
     * Get an training for a company from its id
	 *
     * @param int $id ID of the training
	 *
     * @return \Scubawhere\Entities\Training
     */
	public function get($id) {
		return $this->training_repo->get($id);
	}

	/**
     * Get all trainings for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->training_repo->all();
	}

	/**
     * Get all trainings for a company including soft deleted models
     *
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all trainings for a company including soft deleted models
     */
	public function getAllWithTrashed() {
		return $this->training_repo->allWithTrashed();
	}

	/**
	 * Validate, create and save the training to the database
	 *
	 * @param array $data Data to autofill training model
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function create($data) 
	{
		return $this->training_repo->create($data);
	}

	/**
	 * Validate, update and save the training to the database
	 *
	 * @param  int   $id           ID of the training
	 * @param  array $data         Information about training
	 *
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model of the training
	 */
	public function update($id, $data) 
	{
    	return $this->training_repo->update($id, $data);
	}

	/**
	 * Remove the training from the database.
	 *
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the training, and the booking ids are then logged
	 *
	 * @param  int $id ID of the training
	 *
	 * @throws \Scubawhere\Exceptions\ConflictException
	 * @throws \Exception
	 */
	public function delete($id)
	{
		/**
		 * 1. Get the training model with any sessions it is booked for in the future, since classes can't
		 * be booked. We need to retrieve it through its courses
		 * 2. Go through the courses and retrieve their booking id
		 * 3. Get the reference and status for all implicated bookings
		 * 4. Filter through bookings that have not been cancelled
		 * (5). If there are valid (not cancelled) bookings, log all of their refrences and return a conflict
		 * 6. Check if the tickets belongs to any courses
		 * 7. Check if any of the courses rely on the class (the class is the only session on the course)
		 * (8). If any are a depedancy for the courses, log the courses and return a conflict
		 * 9. Delete the class and return OK.
		 *
		 * @todo
		 * - Reduce the number of db transactions by joining the booking info with the booking details
		 * - Find a way to not retieve the appended attributes of the booking model
		 */
		
		$booking_ids = array();

		// STEP 1.
		$training = $this->training_repo->getUsedInFutureBookings($id);

		// STEP 2.
		foreach($training->courses as $obj) 
		{
			$ids = $obj->bookingdetails
				->filter(function($obj) {
					if($obj->training_session) {
						return $obj->booking_id;
					}
				})
				->toArray();	  
			$booking_ids = array_merge($booking_ids, $ids);
		}

		// STEP 3.
		$bookings = Context::get()->bookings()
			->whereIn('id', $booking_ids)
			->get(['reference', 'status']);

		// STEP 4.
		$bookings = $bookings->filter(function($obj){
			if($obj->status != 'cancelled') return $obj;	
		})
		->toArray();

		// STEP 5.
		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the class, '. $training->name);
			foreach($bookings as $obj) {
				$logger->append('The class is used in the future in booking ' . '['.$obj['reference'].']');
			}

			throw new HttpConflict(__CLASS__.__METHOD__,
				['The class could not be deleted as it is used in bookings in the future, '.
								'Please visit the troubleshooting tab for more info on how to delete it.']);
		}
		// STEP 6.
		if(!$training->getDeleteableAttribute()) 
		{
            $problem_courses = array();
			foreach($training->courses as $obj) 
			{
				// STEP 7.
				if($obj->tickets()->exists()) 
				{
                    \DB::table('course_training')
                        ->where('course_id', $obj->id)
                        ->where('training_id', $training->id)
                        ->update(array('deleted_at' => \DB::raw('NOW()')));    
                }
				else 
				{
                    array_push($problem_courses, $obj);
                }
            }
            // STEP 8.
			if(sizeof($problem_courses) > 0)
			{
                $logger = $this->log_service->create('Attempting to delete the class ' . $training->name);

				foreach($problem_courses as $prob) 
				{
					$logger->append('The class can not be deleted becuase it belongs to the course ' . $prob->name . ', please assign a diffrent class or ticket to it');
				}
				throw new HttpConflict(__CLASS__.__METHOD__,
					['The class could not be deleted as it is assigned to a course, '.
							'please visit the error logs to view how to correct it before deleting it']);
			}
        }
        // STEP 9.
		$training->delete();
	}

}