<?php

namespace ScubaWhere\Services;

use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use ScubaWhere\Services\PriceService;
use ScubaWhere\Exceptions\ConflictException;
use ScubaWhere\Exceptions\BadRequestException;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\TicketRepoInterface;

class TicketService {

	/** 
	 *	Repository to access the ticket models
	 *	@var \ScubaWhere\Repositories\TicketRepo
	 */
	protected $ticket_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * @var \ScubaWhere\Services\LogService
	 */
	protected $log_service;

	/**
	 * Service used to validate and associate prices
	 * @var \ScubaWhere\Services\PriceService
	 */
	protected $price_service;

	/**
	 * @param TicketRepoInterface     Injected using \ScubaWhere\Repositories\TicketRepoServiceProvider
	 * @param LogService              Injected using laravel's IOC container
	 * @param PriceService            Injected using laravel's IOC container
	 */
	public function __construct(TicketRepoInterface $ticket_repo,
								LogService $log_service,
								PriceService $price_service) 
	{
		$this->ticket_repo = $ticket_repo;
		$this->log_service = $log_service;
		$this->price_service = $price_service;
	}

	/**
     * Get an ticket for a company from its id
     * @param int ID of the ticket
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an ticket for a company
     */
	public function get($id) {
		return $this->ticket_repo->get($id);
	}

	/**
     * Get all tickets for a company
     * @param int ID of the ticket
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all tickets for a company
     */
	public function getAll() {
		return $this->ticket_repo->all();
	}

	/**
     * Get all tickets for a company including soft deleted models
     * @param int ID of the ticket
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all tickets for a company including soft deleted models
     */
	public function getAllWithTrashed() {
		return $this->ticket_repo->allWithTrashed();
	}

	/**
     * Get all the available (can be purchased) tickets for a company
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all available tickets
     */
	public function getAvailable() {
		return $this->ticket_repo->getAvailable();
	}

	/**
	 * Validate, create and save the ticket and prices to the database
	 * @param  array Data to autofill ticket model
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model for the ticket
	 */
	public function create($data, $trips, $boats, $boatrooms, $base_prices, $prices) 
	{
		\DB::beginTransaction();
		try 
		{
			$ticket = $this->ticket_repo->create($data, $trips, $boats, $boatrooms);
			if(!$data['only_packaged']) 
			{
				$prices = $this->price_service->validatePrices($base_prices, $prices);
				$this->price_service->associatePrices($ticket->basePrices(), $prices['base']);

				if($prices['seasonal']) {
					$this->price_service->associatePrices($ticket->prices(), $prices['seasonal']);
				}
			}
			\DB::commit();
		}
		catch(Exception $e) {
			\DB::rollback();
			throw $e;
		}
		return $ticket;
	}

	/**
	 * Validate, update and save the ticket and prices to the database
	 * @param  int   $id           ID of the ticket
	 * @param  array $data         Information about ticket
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model of the ticket
	 */
	public function update($id, $data, $trips, $boats, $boatrooms, $base_prices, $prices) 
	{
		\DB::beginTransaction();
		try 
		{
			$ticket = $this->ticket_repo->update($id, $data, $trips, $boats, $boatrooms);
			if(!$data['only_packaged']) 
			{
				$prices = $this->price_service->validatePrices($base_prices, $prices);
				$this->price_service->associatePrices($ticket->basePrices(), $prices['base']);

				if($prices['seasonal']) {
					$this->price_service->associatePrices($ticket->prices(), $prices['seasonal']);
				}
			}
			\DB::commit();
		}
		catch(Exception $e) {
			\DB::rollback();
			throw $e;
		}
		return $ticket;
	}

	/**
	 * Remove the ticket from the database.
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the ticket, and the booking ids are then logged
	 * @throws \ScubaWhere\Exceptions\ConflictException
	 * @throws Exception
	 * @param  int $id ID of the ticket
	 */
	public function delete($id)
	{
		/**
		 * 1. Get the ticket model with any sessions it is booked in
		 * 2. Filter through bookings that have not been cancelled
		 * 3. If there are valid (not cancelled) bookings, log all of their refrences and return a conflict
		 * 4. Check if the ticket is used in any packages, if so, remove the ticket from them
		 * 5. Delete the ticket and its associated prices
		 *
		 * @todo This can be improved by removing the second call to the db to get the booking status by returning
		 * 		 and object from the map containing the status and reference
		 */
	
        $ticket = \Ticket::onlyOwners()
			->with(['packages', 'courses',
			'bookingdetails.session' => function($q) {
				$q->where('start', '>=', Helper::localtime());
			}])
	        ->findOrFail($id);

		$booking_ids = $ticket->bookingdetails->filter(function($obj) {
			if($obj->session != null || $obj->training_session != null) {
				return $obj->booking_id;
			}
		})
		->toArray();

		$bookings = \Booking::onlyOwners()
			->whereIn('id', $booking_ids)
			->get(['id', 'reference', 'status']);

		$quotes = $bookings->filter(function($obj) {
			if($obj->status == 'saved') return $obj->id;
		})
		->toArray();

		\Booking::onlyOwners()->whereIn('id', $quotes)->delete();

		$bookings = $bookings->filter(function($obj){
			if($obj->status != 'cancelled' && $obj->status != 'saved') return $obj;	
		})->toArray();

		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the ticket, '
												. $ticket->name);
			foreach($bookings as $obj) 
			{
				$logger->append('The ticket is used in the future in booking ' . '['.$obj['reference'].']');
			}
			throw new ConflictException(
				['The ticket could not be deleted as it is used in bookings in the future, '.
						'Please visit the troubleshooting tab for more info on how to delete it.']);
		}

        if(!$ticket->deleteable)
        {
            // Remove the ticket from all packages, as packages don't require a ticket, there is no checks
            foreach($ticket->packages as $obj) 
            {
                \DB::table('packageables')
                    ->where('package_id', $obj->id)
                    ->where('packageable_type', 'Ticket')
                    ->where('packageable_id', $ticket->id)
                    ->update(array('deleted_at' => \DB::raw('NOW()')));    
            }
            
            // Get a list of the tickets course ids
            $course_ids = array();
            foreach($ticket->courses as $obj) 
            {
                array_push($course_ids, $obj->id);
            }
            // Courses require atleast one ticket or training, before deleting, ensure that
            // the ticket is not the only one in a course
            $courses = Context::get()->courses()
				->whereIn('id', $course_ids)
				->with('tickets', 'trainings')
				->get();
            // Get a list of courses that require the ticket
            $problem_courses = array();
            foreach($courses as $obj) 
            {
                if((sizeof($obj->tickets) + sizeof($obj->trainings)) < 2)
                {
                    array_push($problem_courses, $obj);
                }
            }
            // If there are courses that rely on the ticket, log the errors and respond with a conflidt (409)
            if(sizeof($problem_courses) > 0)
            {
                $logger = $this->log_service
                               ->create('Attempting to delete the ticket ' . $ticket->name);
                foreach($problem_courses as $obj) 
                {
                    $logger->append('The ticket can not be deleted as the course ' . $obj->name .
                    	' requires atleast one ticket or class, please delete or edit the course so that the ticket can be deleted');
                }
                throw new ConflictException(
                	['The ticket cannot be deleted as it has courses that depend on it, please visit the error logs tab to see more information on how to fix this.']); 
            }
            // Otherwise soft delete the pivots to the courses
            else
            {
                foreach($ticket->courses as $obj) 
                {
                    \DB::table('packageables')
                        ->where('package_id', $obj->id)
                        ->where('packageable_type', 'Course')
                        ->where('packageable_id', $ticket->id)
                        ->update(array('deleted_at' => \DB::raw('NOW()')));    
                }
            }
        }

        $ticket->delete();
        $this->price_service->delete('Ticket', $ticket->id);
	}

}