<?php
use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BoatroomController extends Controller {

	protected $logging_service;

	public function __construct(LogService $logging_service)
	{
		$this->logging_service = $logging_service;
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->boatrooms()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The cabin could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Context::get()->boatrooms()->get();
	}

	public function postAdd()
	{
		$data = Input::only(
			'name',
			'description'
		);

		$boatroom = new Boatroom($data);

		if( !$boatroom->validate() )
		{
			return Response::json( array('errors' => $boatroom->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$boatroom = Context::get()->boatrooms()->save($boatroom);

		return Response::json( array('status' => 'OK. Cabin created', 'id' => $boatroom->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boatroom = Context::get()->boatrooms()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The cabin could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only(
			'name',
			'description'
		);

		if( !$boatroom->update($data) )
		{
			return Response::json( array('errors' => $boatroom->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('status' => 'OK. Cabin updated'), 200 ); // 200 OK
	}

	/*
	public function postDeactivate()
	{

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boatroom = Context::get()->boatrooms()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The cabin could not be found.')), 404 ); // 404 Not Found
		}

		$boatroom->delete();

		return array('status' => 'OK. Cabin deactivated');
	}

	public function postRestore()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boatroom = Context::get()->boatrooms()->onlyTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The cabin could not be found.')), 404 ); // 404 Not Found
		}

		$boatroom->restore();

		return array('status' => 'OK. Cabin restored');
	}
	*/

	public function postDelete()
	{
		/**
		 * 1 - Retrieve the boatroom.
		 * 2 - Get any future booking's reference code. This needs to be done through booking details as cabins are not
		 * directly related to bookings.
		 * 3 - If there are future bookings, create an error log and add entries telling the user which bookings must be changed 
		 * 4 - Check if the boatroom has any tickets or boats associated to it
		 * (5) - If so, soft delete their pivot tables, DO NOT DETACH. As past bookings may need to refrence previous states
		 * 6 - Soft delete the boat, @todo if there are no bookings in the past, force delete it. But this adds computation
		 * to the API. Maybe a cron job should be in charge of that. Or push a notification to a queue.
		 */
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$boatroom = Context::get()->boatrooms()->with('boats', 'tickets', 'bookingdetails')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The cabin could not be found.')), 404 ); // 404 Not Found
		}

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
			//return $booking_ids;
			// @todo investigate how to remove this by using bookingdetails.booking.reference
			$booking_refs = Context::get()->bookings()->whereIn('id', $booking_ids->toArray())->lists('reference');
			foreach($booking_refs as $obj) 
			{
				$logger->append('Could not delete the cabin as it is used in the booking ' . $obj);
			}
			return Response::json(
						array('errors' => 
							array('The cabin could not be delete, please visit the error logs for more information on how to resolve this.')
						), 409);
		}

		if(!$boatroom->deleteable)
		{
			foreach($boatroom->tickets as $obj) 
			{
				DB::table('ticketables')
					->where('ticketable_type', 'Boatroom')
					->where('ticketable_id', $boatroom->id)
					->where('ticket_id', $obj->id)
					->update(array('deleted_at' => DB::raw('NOW()')));    
			}	
			foreach($boatroom->boats as $obj) 
			{
				DB::table('boat_boatroom')
					->where('boat_id', $obj->id)
					->where('boatroom_id', $boatroom->id)
					->update(array('deleted_at' => DB::raw('NOW()')));    
			}
		}

		$boatroom->delete();

		return array('status' => 'Ok. Cabin deleted'); // todo, change this to proper json response, but needs to be fixed in the front end first
	}

}
