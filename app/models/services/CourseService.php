<?php

namespace ScubaWhere\Services;

use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use ScubaWhere\Exceptions\ConflictException;
use ScubaWhere\Exceptions\BadRequestException;
use ScubaWhere\Repositories\CourseRepoInterface;
use ScubaWhere\Exceptions\InvalidInputException;

class CourseService {

	/** 
	 *	Repository to access the course models
	 *	\ScubaWhere\Repositories\CourseRepo
	 */
	protected $course_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * \ScubaWhere\Services\LogService
	 */
	protected $log_service;

	/**
	 * Service used to validate and associate prices
	 * \ScubaWhere\Services\PriceService
	 */
	protected $price_service;

	/**
	 * @param CourseRepoInterface     Injected using \ScubaWhere\Repositories\CourseRepoServiceProvider
	 * @param LogService              Injected using laravel's IOC container
	 * @param PriceService            Injected using laravel's IOC container
	 */
	public function __construct(CourseRepoInterface $course_repo, LogService $log_service, PriceService $price_service) {
		$this->course_repo = $course_repo;
		$this->log_service = $log_service;
		$this->price_service = $price_service;
	}

	/**
     * Get an course for a company from its id
     * @param int ID of the course
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an course for a company
     */
	public function get($id) {
		return $this->course_repo->get($id);
	}

	/**
     * Get all courses for a company
     * @param int ID of the course
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all courses for a company
     */
	public function getAll() {
		return $this->course_repo->all();
	}

	/**
     * Get all courses for a company including soft deleted models
     * @param int ID of the course
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all courses for a company including soft deleted models
     */
	public function getAllWithTrashed() {
		return $this->course_repo->allWithTrashed();
	}

	/**
	 * Validate, create and save the course and prices to the database
	 * @param  array Data to autofill course model
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model for the course
	 */
	public function create($data, $tickets, $trainings, $base_prices, $prices) 
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
	 * @param  int   $id           ID of the course
	 * @param  array $data         Information about course
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model of the course
	 */
	public function update($id, $data, $base_prices, $prices) 
	{
    	$prices = $this->price_service->validatePrices($base_prices, $prices);
		$course = $this->course_repo->update($data);

		if($prices['base']) $this->price_service->associatePrices($addon->basePrices(), $prices['base']);
		if($prices['seasonal']) $this->price_service->associatePrices($addon->prices(), $prices['seasonal']);

		$course->load(['trainings', 'tickets', 'basePrices', 'prices']);
		return $course;
	}

	/**
	 * Remove the course from the database.
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the course, and the booking ids are then logged
	 * @throws \ScubaWhere\Exceptions\ConflictException
	 * @throws Exception
	 * @param  int $id ID of the course
	 */
	public function delete($id)
	{
	    $course = \Course::onlyOwners()
			->with(['packages',
			'bookingdetails.session' => function($q) {
				$q->where('start', '>=', Helper::localtime());
			},
			'bookingdetails.training_session' => function($q) {
				$q->where('start', '>=', Helper::localtime());
			}])
            ->findOrFail($id);

		$booking_ids = $course->bookingdetails
			->map(function($obj) {
				if($obj->session != null || $obj->training_session != null) {
					return $obj->booking_id;
				}
			})
			->toArray();

		$bookings = \Booking::onlyOwners()
			->whereIn('id', $booking_ids)
			->get(['id', 'reference', 'status']);

		$quotes = $bookings->map(function($obj) {
			if($obj->status == 'saved') return $obj->id;
		})
		->toArray();

		\Booking::onlyOwners()->whereIn('id', $quotes)->delete();

		$bookings = $bookings->filter(function($obj){
			if($obj->status != 'cancelled' && $obj->status != 'saved') return $obj;	
		})->toArray();

		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the course, ' . $course->name);
			foreach($bookings as $obj) {
				$logger->append('The course is used in the future in booking ' . '['.$obj['reference'].']');
			}
			throw new ConflictException(
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