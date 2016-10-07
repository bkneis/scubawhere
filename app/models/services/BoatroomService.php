<?php

namespace ScubaWhere\Services;

use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use ScubaWhere\Exceptions\ConflictException;
use ScubaWhere\Exceptions\BadRequestException;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\BoatroomRepoInterface;

class BoatroomService {

	/** 
	 *	Repository to access the boatroom models
	 *	\ScubaWhere\Repositories\BoatroomRepo
	 */
	protected $boatroom_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * \ScubaWhere\Services\LogService
	 */
	protected $log_service;

	/**
	 * @param BoatroomRepoInterface     Injected using \ScubaWhere\Repositories\BoatroomRepoServiceProvider
	 * @param LogService                Injected using laravel's IOC container
	 */
	public function __construct(BoatroomRepoInterface $boatroom_repo, LogService $log_service) {
		$this->boatroom_repo = $boatroom_repo;
		$this->log_service = $log_service;
	}

	/**
     * Get an boatroom for a company from its id
     * @param int ID of the boatroom
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an boatroom for a company
     */
	public function get($id) {
		return $this->boatroom_repo->get($id);
	}

	/**
     * Get all boatrooms for a company
     * @param int ID of the boatroom
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all boatrooms for a company
     */
	public function getAll() {
		return $this->boatroom_repo->all();
	}

	/**
     * Get all boatrooms for a company including soft deleted models
     * @param int ID of the boatroom
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all boatrooms for a company including soft deleted models
     */
	public function getAllWithTrashed($id) {
		return $this->boatroom_repo->allWithTrashed();
	}

	/**
	 * Validate, create and save the boatroom and prices to the database
	 * @param  array Data to autofill boatroom model
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model for the boatroom
	 */
	public function create($data) 
	{
		return $this->boatroom_repo->create($data);
	}

	/**
	 * Validate, update and save the boatroom and prices to the database
	 * @param  int   $id           ID of the boatroom
	 * @param  array $data         Information about boatroom
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model of the boatroom
	 */
	public function update($id, $data) 
	{
    	return $this->boatroom_repo->update($id, $data);
	}

	/**
	 * Remove the boatroom from the database.
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the boatroom, and the booking ids are then logged
	 * @throws \ScubaWhere\Exceptions\ConflictException
	 * @throws Exception
	 * @param  int $id ID of the boatroom
	 */
	public function delete($id)
	{
		/**
		 * 1 - Retrieve the boatroom.
		 * 2 - Get any future booking's reference code. This needs to be done through booking details as cabins are not
		 * directly related to bookings.
		 * 3 - If there are future bookings, create an error log and add entries telling the user which bookings must be changed 
		 * 4 - Check if the boatroom has any tickets or boats associated to it
		 * (5) - If so, soft delete their pivot tables, DO NOT DETACH. As past bookings may need to refrence previous states
		 * 6 - Soft delete the boat, 
		 * 
		 * @todo if there are no bookings in the past, force delete it. But this adds computation
		 * to the API. Maybe a cron job should be in charge of that. Or push a notification to a queue.
		 * @todo investigate how to remove this by using bookingdetails.booking.reference
		 */
		$boatroom = Context::get()->boatrooms()->with('boats', 'tickets', 'bookingdetails')->findOrFail($id);

		$future_bookings = $boatroom->bookingdetails()
			->with(['booking' => function($q) {
				return $q->select('id');
			}])
			->whereHas('departure', function($query) {
				return $query->where('start', '>=', Helper::localTime()->format('Y-m-d H:i:s'));
			})
			->get();

		if(!$future_bookings->isEmpty())
		{
			$logger = $this->logging_service->create('Attempting to delete the boatroom ' . $boatroom->name);
			$booking_ids = $future_bookings->map(function($obj) {
				return $obj->booking_id;
			});

			$bookings = Context::get()->bookings()
				->whereIn('id', $booking_ids->toArray())
				->get(['reference', 'status', 'id']);

			$quotes = $bookings->filter(function($obj) { if($obj->status === 'saved') return $obj->id; })->toArray();

			Booking::whereIn('id', $quotes)->delete();

			$bookings = $bookings->filter(function($obj) {
				if($obj->status != 'cancelled' && $obj->status != 'saved' && $obj->status != 'expired') return $obj;
			})
			->toArray();

			foreach($bookings as $obj) 
			{
				$logger->append('Could not delete the cabin as it is used in the booking [' . $obj['reference'] . ']');
			}

			throw new ConflictException(
				['The cabin could not be delete, please visit the troubleshooting tab for more information on how to resolve this.']);
		}

		if(!$boatroom->deleteable)
		{
			foreach($boatroom->tickets as $obj) 
			{
				\DB::table('ticketables')
					->where('ticketable_type', 'Boatroom')
					->where('ticketable_id', $boatroom->id)
					->where('ticket_id', $obj->id)
					->update(array('deleted_at' => \DB::raw('NOW()')));    
			}	
			foreach($boatroom->boats as $obj) 
			{
				\DB::table('boat_boatroom')
					->where('boat_id', $obj->id)
					->where('boatroom_id', $boatroom->id)
					->update(array('deleted_at' => \DB::raw('NOW()')));    
			}
		}

		$boatroom->delete();	
	}

}