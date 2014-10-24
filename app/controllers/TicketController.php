<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;
use PhilipBrown\Money\Currency;

class TicketController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->tickets()->withTrashed()->with('boats', 'trips')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->tickets()->withTrashed()->with('boats', 'trips')->get();
	}

	public function postAdd()
	{
		$data = Input::only('name', 'description', 'price', 'currency');
		$data['currency'] = Helper::currency( Input::get('currency') );

		// Convert price to subunit
		try
		{
			$currency = new Currency( $data['currency'] );
		}
		catch(InvalidCurrencyException $e)
		{
			return Response::json( array( 'errors' => array('The currency is not a valid currency code!')), 400 ); // 400 Bad Request
		}
		$data['price'] = (int) round( $data['price'] * $currency->getSubunitToUnit() );

		$ticket = new Ticket($data);

		if( !$ticket->validate() )
		{
			return Response::json( array('errors' => $ticket->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// Check if 'trips' input array is given and not empty
		$trips = Input::get('trips');
		if( !is_array($trips) || empty($trips) )
			return Response::json( array( 'errors' => array('The "trips" value must be an array and cannot be empty!')), 400 ); // 400 Bad Request

		// Required input has been validated, save the model
		$ticket = Auth::user()->tickets()->save($ticket);

		// Ticket has been created, let's connect it to trips
		// TODO Validate existence and ownership of trip IDs
		$ticket->trips()->sync( $trips );

		// Ticket has been created, let's connect it to boats
		$boats = Input::get('boats');
		if( $boats && !empty($boats) ) // only if the parameter is given/submitted
		{
			$sync = array();
			foreach( $boats as $boat_id => $accommodation_id )
			{
				// The validator fails when accommodation_id is submitted as '' (which means null but is valid), so we have to conditionally route around it
				if( !empty($accommodation_id) )
				{
					$validator = Validator::make(
						array(
							'boat_id'          => $boat_id,
							'accommodation_id' => $accommodation_id
						),
						array(
							'boat_id'          => 'integer|exists:boats,id',
							'accommodation_id' => 'integer|exists:accommodations,id'
						)
					);
				}
				else
				{
					$accommodation_id = null;
					$validator = Validator::make(
						array(
							'boat_id' => $boat_id
						),
						array(
							'boat_id' => 'integer|exists:boats,id'
						)
					);
				}

				if( $validator->fails() )
				{
					return Response::json( array('errors' => $validator->messages()->all()), 406 ); // 406 Not Acceptable
				}

				$sync[$boat_id] = array('accommodation_id' => $accommodation_id);
			}
			$ticket->boats()->sync( $sync );
		}

		// Success
		return Response::json( array('status' => 'Ticket created and connected OK', 'id' => $ticket->id), 201); // 201 Created
	}

	public function postEdit()
	{
		// Check if the ticket exists
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$ticket = Auth::user()->tickets()->withTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only('name', 'description', 'price', 'currency');

		// Convert price to subunit
		try
		{
			$currency = new Currency( $data['currency'] );
		}
		catch(InvalidCurrencyException $e)
		{
			return Response::json( array( 'errors' => array('The currency is not a valid currency code!')), 400 ); // 400 Bad Request
		}
		$data['price'] = (int) round( $data['price'] * $currency->getSubunitToUnit() );

		// Check if 'trips' input array is given and not empty
		$trips = Input::get('trips');
		if( !empty($trips) && !is_array($trips) )
			return Response::json( array( 'errors' => array('The "trips" value must be an array!')), 400 ); // 400 Bad Request

		// Check if a booking exists for the ticket and whether a critical value is updated
		if( $ticket->bookings()->count() > 0 && (
			   (!empty($trips)            && checkRemovedTripBookings($ticket->id, $ticket->trips()->lists('id'), $trips))
			|| (!empty($data['price'])    && $data['price'] != $ticket->price)
			|| (!empty($data['currency']) && Helper::currency( $data['currency'] ) != $ticket->currency)
		) )
		{
			// If yes, create a new ticket with the input data

			// Replace all unavailable input data with data from the old ticket object
			if( empty($data['name']) )        $data['name']        = $ticket->name;
			if( empty($data['description']) ) $data['description'] = $ticket->description;
			if( empty($data['price']) )       $data['price']       = $ticket->price;
			if( empty($data['currency']) )    $data['currency']    = $ticket->currency;

			if( !Input::get('boats') )
			{
				$data['boats'] = array();
				foreach($ticket->boats() as $boat) // Includes pivot data by default
				{
					$data['boats'][$boat->id] = $boat->pivot->accommodation_id;
				}
			}
			else {
				$data['boats'] = Input::get('boats');
			}

			if( empty($trips) )
			{
				$data['trips'] = $ticket->trips()->lists('id');
			}
			else {
				$data['trips'] = $trips;
			}

			// SoftDelete the old ticket
			$ticket->delete();

			// TODO MAYBE: Unconnect the original ticket from boats

			// Dispatch add-ticket route with all data and return result
			$request = Request::create('api/ticket/add', 'POST', $data);
			return Route::dispatch($request);
		}
		else
		{
			// If not, simply update it
			if( !$ticket->update($data) )
			{
				return Response::json( array('errors' => $ticket->errors()->all()), 406 ); // 406 Not Acceptable
			}
			else
			{
				// Ticket has been updated, let's connect it to boats
				$boats = Input::get('boats');
				if( $boats && !empty($boats) ) // only if the parameter is given/submitted
				{
					$sync = array();
					foreach( $boats as $boat_id => $accommodation_id )
					{
						// If the boat array is submitted empty, meaning all boats should be detached, skip all this and go directly to sync
						if( empty($boat_id) )
						{
							$sync = array();
							break;
						}

						// The validator fails when accommodation_id is submitted as '' (which means null but is valid), so we have to conditionally route around it
						if( !empty($accommodation_id) )
						{
							$validator = Validator::make(
								array(
									'boat_id'          => $boat_id,
									'accommodation_id' => $accommodation_id
								),
								array(
									'boat_id'          => 'integer|exists:boats,id',
									'accommodation_id' => 'integer|exists:accommodations,id'
								)
							);
						}
						else
						{
							$accommodation_id = null;
							$validator = Validator::make(
								array(
									'boat_id' => $boat_id
								),
								array(
									'boat_id' => 'integer|exists:boats,id'
								)
							);
						}

						if( $validator->fails() )
						{
							return Response::json( array('errors' => $validator->messages()->all()), 406 ); // 406 Not Acceptable
						}

						$sync[$boat_id] = array('accommodation_id' => $accommodation_id);
					}
					$ticket->boats()->sync( $sync );
				}

				// Check if 'trips' input array is not empty
				if( !empty($trips) )
				{
					// TODO Validate existence and ownership of trip IDs
					$ticket->trips()->sync( $trips );
				}

				// When no problems occur, we return a success response
				return Response::json( array('status' => 'OK. Ticket updated'), 200 ); // 200 OK
			}
		}
	}

	protected function checkRemovedTripBookings($ticket_id, $old_trips, $new_trips)
	{
		// Check, which tripIDs have been removed
		$removed_trips = array_diff($old_trips, $new_trips);

		// Now check if any of these removed trips has already been booked with this ticket
		$check = Session::whereIn('trip_id', $removed_trips)->bookingdetails()->where('ticket_id', $ticket_id)->count() > 0;
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$ticket = Auth::user()->tickets()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			$ticket->forceDelete();
		}
		catch(QueryException $e)
		{
			return Response::json( array('errors' => array('The ticket can not be removed because it has been booked at least once. Try deactivating it instead.')), 409); // 409 Conflict
		}

		return array('status' => 'OK. Ticket deleted');
	}

}
