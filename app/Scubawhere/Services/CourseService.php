<?php

namespace Scubawhere\Services;

use Scubawhere\Entities\Booking;
use Scubawhere\Services\LogService;
use Scubawhere\Exceptions\Http\HttpConflict;
use Scubawhere\Repositories\CourseRepoInterface;

class CourseService {

	/** @var \Scubawhere\Repositories\CourseRepo */
	protected $course_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;

	/**
	 * Service used to validate and associate prices
	 * @var \Scubawhere\Services\PriceService
	 */
	protected $price_service;


	public function __construct(CourseRepoInterface $course_repo,
								LogService $log_service,
								PriceService $price_service)
	{
		$this->course_repo = $course_repo;
		$this->log_service = $log_service;
		$this->price_service = $price_service;
	}

	/**
     * Get an course for a company from its id
	 *
     * @param int $id ID of the course
	 *
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an course for a company
     */
	public function get($id) {
		return $this->course_repo->get($id, ['trainings', 'tickets', 'basePrices', 'prices']);
	}

	/**
     * Get all courses for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->course_repo->all(['trainings', 'tickets', 'basePrices', 'prices']);
	}

	/**
     * Get all courses for a company including soft deleted models
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->course_repo->allWithTrashed(['trainings', 'tickets', 'basePrices', 'prices']);
	}

	/**
	 * Validate, create and save the course and prices to the database
	 *
	 * @param array $data Data to autofill course model
	 *
	 * @return \Scubawhere\Entities\Course
	 */
	public function create(array $data, array $tickets, array $trainings, array $base_prices, array $prices)
	{
		$prices = $this->price_service->validatePrices($base_prices, $prices);
		$course = $this->course_repo->create($data);

		$course->tickets()->sync($tickets);
		$course->trainings()->sync($trainings);

		$this->price_service->associatePrices($course->basePrices(), $prices['base']);
		if($prices['seasonal']) $this->price_service->associatePrices($course->prices(), $prices['seasonal']);

		$course->load(['trainings', 'tickets', 'basePrices', 'prices']);
		return $course;
	}

	/**
	 * Validate, update and save the course and prices to the database
	 *
	 * @param int   $id           ID of the course
	 * @param array $data         Information about course
	 * @param array $base_prices
	 * @param array $prices
	 *
	 * @return \Scubawhere\Entities\Course
	 */
	public function update($id, array $data, array $base_prices, array $prices)
	{
    	$prices = $this->price_service->validatePrices($base_prices, $prices);
		$course = $this->course_repo->update($id, $data);

		if($prices['base']) $this->price_service->associatePrices($course->basePrices(), $prices['base']);
		if($prices['seasonal']) $this->price_service->associatePrices($course->prices(), $prices['seasonal']);

		$course->load(['trainings', 'tickets', 'basePrices', 'prices']);
		return $course;
	}

	/**
	 * Remove the course from the database.
	 *
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the course, and the booking ids are then logged.
	 *
	 * @param int $id ID of the course
	 *
	 * @throws \Scubawhere\Exceptions\ConflictException
	 * @throws \Exception
	 */
	public function delete($id)
	{
		$course = $this->course_repo->getUsedInFutureBookings($id);

		$booking_ids = $course->bookingdetails
			->map(function($obj) {
				if($obj->session != null || $obj->training_session != null) {
					return $obj->booking_id;
				}
			})
			->toArray();

		$bookings = Booking::onlyOwners()
			->whereIn('id', $booking_ids)
			->get(['id', 'reference', 'status']);

		$quotes = $bookings->map(function($obj) {
			if($obj->status == 'saved') return $obj->id;
		})
		->toArray();

		Booking::onlyOwners()->whereIn('id', $quotes)->delete();

		$bookings = $bookings->filter(function($obj){
			if($obj->status != 'cancelled' && $obj->status != 'saved') return $obj;	
		})->toArray();

		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the course, ' . $course->name);
			foreach($bookings as $obj) {
				$logger->append('The course is used in the future in booking ' . '['.$obj['reference'].']');
			}
			throw new HttpConflict(__CLASS__ . __METHOD__,
				['The course could not be deleted as it is used in bookings in the future, '.
						'Please visit the troubleshooting tab for more info on how to delete it.']);
		}

        if(!$course->getDeleteableAttribute())
        {
            foreach($course->packages as $obj) 
            {
                \DB::table('packageables')
                    ->where('packageable_type', 'Course')
                    ->where('packageable_id', $course->id)
                    ->where('package_id', $obj->id)
                    ->update(array('deleted_at' => \DB::raw('NOW()')));    
            } 
        }

        $course->delete();
        $this->price_service->delete('Course', $course->id);
	}

}