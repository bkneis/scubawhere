<?php

namespace Scubawhere\Services;

use Scubawhere\Entities\Booking;
use Scubawhere\Entities\Course;
use Scubawhere\Exceptions\Http\HttpConflict;
use Scubawhere\Repositories\CourseRepoInterface;

class CourseService {

	/** @var \Scubawhere\Repositories\CourseRepo */
	protected $courseRepo;

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
		$this->courseRepo = $course_repo;
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
		return $this->courseRepo->get($id, ['trainings', 'tickets', 'prices', 'basePrices']);
	}

	/**
     * Get all courses for a company
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->courseRepo->all(['trainings', 'tickets', 'prices', 'basePrices']);
	}

	/**
     * Get all courses for a company including soft deleted models
	 *
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->courseRepo->allWithTrashed(['trainings', 'tickets', 'prices', 'basePrices']);
	}

	/**
	 * Validate, create and save the course and prices to the database
	 *
	 * @param array $data Data to autofill course model
	 *
	 * @return \Scubawhere\Entities\Course
	 */
	public function create(array $data)
	{
		return Course::create($data)
			->syncItems($data)
			->syncPrices($data['prices'])
			->load(['trainings', 'tickets', 'prices', 'basePrices']);
	}

	/**
	 * Validate, update and save the course and prices to the database
	 *
	 * @param $id
	 * @param array $data Information about course
	 * @return Course
	 * @throws \Scubawhere\Exceptions\Http\HttpNotFound
	 * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
	 */
	public function update($id, array $data)
	{
		return $this->courseRepo
			->get($id)
			->update($data)
			->syncPrices($data['prices'])
			->load(['trainings', 'tickets', 'prices', 'basePrices']);
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
		$course = $this->courseRepo->getWithFutureBookings($id);
		
		$bookings = $course->getActiveBookings();

		if (empty($bookings)) {
			$logger = $this->log_service->create('Attempting to delete the course, ' . $course->name);
			foreach($bookings as $obj) {
				$logger->append('The course is used in the future in booking ' . '['.$obj['reference'].']');
			}
			throw new HttpConflict(__CLASS__ . __METHOD__,
				['The course could not be deleted as it is used in bookings in the future, '.
						'Please please <a href="#troubleshooting?id='. $logger->getId() .'">click here</a> for more info on how to delete it.']);
		}

		$course->removeFromPackages()
			->deleteQuotes()
			->delete();
		
        $this->price_service->delete(get_class($course), $course->id);
	}
	
	/**
	 *
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
	 */

}