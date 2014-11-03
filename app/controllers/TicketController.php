<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;

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
		return Auth::user()->tickets()->with('boats', 'trips', 'prices')->get();
	}

	public function getAllWithTrashed()
	{
		return Auth::user()->tickets()->withTrashed()->with('boats', 'trips', 'prices')->get();
	}

	public function postAdd()
	{
		$data = Input::only('name', 'description');

		$ticket = new Ticket($data);

		if( !$ticket->validate() )
		{
			return Response::json( array('errors' => $ticket->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// Check if 'trips' input array is given and not empty
		$trips = Input::get('trips');
		if( !is_array($trips) || empty($trips) )
			return Response::json( array( 'errors' => array('The "trips" value must be an array and cannot be empty!')), 400 ); // 400 Bad Request

		$prices = Input::get('prices');
		// Filter out empty price inputs
		$prices = array_filter($prices, function($element)
		{
			return $element['new_decimal_price'] !== '';
		});
		// Check if 'prices' input array is given and not empty
		if( !is_array($prices) || empty($prices) )
			return Response::json( array( 'errors' => array('The "prices" value must be an array and cannot be empty!')), 400 ); // 400 Bad Request

		// Required input has been validated, save the model
		$ticket = Auth::user()->tickets()->save($ticket);

		// Ticket has been created, let's connect it to trips
		// TODO Validate existence and ownership of trip IDs
		$ticket->trips()->sync( $trips );

		// Normalise prices array
		$prices = Helper::normaliseArray($prices);
		// Create prices
		foreach($prices as &$price)
		{
			$price = new Price($price);
		}
		$ticket->prices()->saveMany($prices);

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
		return Response::json( array('status' => 'Ticket created and connected OK', 'id' => $ticket->id, 'prices' => $ticket->prices()->get()), 201); // 201 Created
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

		$data = Input::only('name', 'description');

		$prices = Input::get('prices');
		if( Input::has('prices') )
		{
			// Filter out empty price inputs
			$prices = array_filter($prices, function($element)
			{
				return $element['new_decimal_price'] !== '';
			});
		}
		if( Input::has('prices') && !is_array($prices) || empty($prices) )
			return Response::json( array('errors' => array('"Prices" must be of type array and cannot be empty.')), 406 ); // 406 Not Acceptable
		elseif( Input::has('prices') )
			$prices = Helper::normaliseArray($prices);

		// Check if 'trips' input is an array, if given
		$trips = Input::get('trips');
		if( Input::has('trips') && !is_array($trips) )
			return Response::json( array( 'errors' => array('The "trips" value must be an array!')), 400 ); // 400 Bad Request

		// Check if a booking exists for the ticket and whether a critical value is updated
		if( $ticket->bookingdetails()->count() > 0 && (
			   (!empty($trips) && $this->checkRemovedTripBookings($ticket->id, $ticket->trips()->lists('id'), $trips))
			|| ($prices        && $this->checkPricesChanged($ticket->prices, $prices))
		) )
		{
			// If yes, create a new ticket with the input data
			$data['prices'] = $prices;

			// Replace all unavailable input data with data from the old ticket object
			if( empty($data['name']) )        $data['name']        = $ticket->name;
			if( empty($data['description']) ) $data['description'] = $ticket->description;
			if( empty($data['prices']) )      $data['prices']      = $ticket->prices;

			if( Input::has('boats') )
			{
				$boats = Input::get('boats');
				if( !empty( $boats ) )
					$data['boats'] = $boats;
				elseif( $ticket->boats()->count() > 0 )
				{
					$data['boats'] = array();
					foreach($ticket->boats as $boat) // Includes pivot data by default
					{
						$data['boats'][$boat->id] = $boat->pivot->accommodation_id;
					}
				}
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

			// Dispatch add-ticket route with all data
			$request = Request::create('api/ticket/add', 'POST', $data);
			$response = Route::dispatch($request);

			// Connect the new ticket to the same packages as the old one (trips is done during creation)
			$newID = $response->getData()->id;
			$newTicket = Auth::user()->tickets()->find($newID);

			$packages = $ticket->packages;

			// Transform packages into syncable array
			$array = [];
			foreach($packages as $package)
			{
				$array[$package->id]['quantity'] = $package->pivot->quantity;
			}

			$newTicket->packages()->sync( $array );

			return $response;
		}
		else
		{
			// If not, simply update it
			if( !$ticket->update($data) )
				return Response::json( array('errors' => $ticket->errors()->all()), 406 ); // 406 Not Acceptable

			if( Input::has('prices') )
			{
				// Delete old prices
				$ticket->prices()->delete();

				// Create new prices
				foreach($prices as &$price)
				{
					$price = new Price($price);
				}
				$ticket->prices()->saveMany($prices);
			}

			if( Input::has('boats') )
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
			}
			else
			{
				// Remove all boats from this ticket
				$ticket->boats()->detach();
			}

			// Check if 'trips' input array is not empty
			if( !empty($trips) )
			{
				// TODO Validate existence and ownership of trip IDs
				$ticket->trips()->sync( $trips );
			}

			// When no problems occur, we return a success response
			return Response::json( array('status' => 'OK. Ticket updated', 'prices' => $ticket->prices()->get()), 200 ); // 200 OK
		}
	}

	protected function checkRemovedTripBookings($ticket_id, $old_trips, $new_trips)
	{
		// Check, which tripIDs have been removed
		$removed_trips = array_diff($old_trips, $new_trips);

		if( count($removed_trips) === 0 )
			return false;

		// Now check if any of these removed trips have already been booked with this ticket
		$departures = Departure::whereIn('trip_id', $removed_trips)->with('bookingdetails')->get();
		foreach($departures as $departure)
		{
			if( $departure->bookingdetails()->where('ticket_id', $ticket_id)->count() > 0 )
				return true;
		}

		return false;
	}

	protected function checkPricesChanged($old_prices, $prices)
	{
		$old_prices = $old_prices->toArray();

		if(count($prices) !== count($old_prices)) return true;

		// Reduce $old_prices to input fields
		foreach($old_prices as &$old_price)
		{
			$old_price = array_intersect_key( $old_price, array('decimal_price' => '', 'currency' => '', 'fromDay' => '', 'fromMonth' => '', 'untilDay' => '', 'untilMonth' => '') );
		}

		// Sort both to be able to compare them without keys
		array_multisort($old_prices, $prices);

		/**
		 * The following comparison works, because `array_diff` only compares the values of the arrays, not the keys.
		 * The $prices arrays have a `new_decimal_price` key, while the $old_prices arrays have a `decimal_price` key,
		 * but since they represent the same info, the comparison works and returns the expected result.
		 */
		for($i = 0; $i < count($prices); $i++)
		{
			// Compare arrays in both directions
			if( count( array_diff($prices[$i], $old_prices[$i]) ) > 0 )
			{
				return true;
			}

			if( count( array_diff($old_prices[$i], $prices[$i]) ) > 0 )
			{
				return true;
			}
		}

		return false;
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

		$id = $ticket->id;

		try
		{
			$ticket->forceDelete();
		}
		catch(QueryException $e)
		{
			return Response::json( array('errors' => array('The ticket can not be removed because it has been booked at least once. Try deactivating it instead.')), 409); // 409 Conflict
		}

		// If deletion worked, delete associated prices
		Price::where(Price::$owner_id_column_name, $id)->where(Price::$owner_type_column_name, 'Ticket')->delete();

		return array('status' => 'OK. Ticket deleted');
	}

}
