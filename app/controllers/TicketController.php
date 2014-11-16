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
			return Auth::user()->tickets()->withTrashed()->with('boats', 'trips', 'basePrices', 'prices')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->tickets()->with('boats', 'trips', 'basePrices', 'prices')->get();
	}

	public function getAllWithTrashed()
	{
		return Auth::user()->tickets()->withTrashed()->with('boats', 'trips', 'basePrices', 'prices')->get();
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

		// ####################### Prices #######################
		$base_prices = Input::get('base_prices');
		if( !is_array($base_prices) )
			return Response::json( array( 'errors' => array('The "base_prices" value must be of type array!')), 400 ); // 400 Bad Request
		// Filter out empty price inputs
		$base_prices = array_filter($base_prices, function($element)
		{
			return $element['new_decimal_price'] !== '';
		});
		// Check if 'prices' input array is now empty
		if( empty($base_prices) )
			return Response::json( array( 'errors' => array('You must submit at least one base price!')), 400 ); // 400 Bad Request

		if( Input::has('prices') )
		{
			$prices = Input::get('prices');
			if( !is_array($prices) )
				return Response::json( array( 'errors' => array('The "prices" value must be of type array!')), 400 ); // 400 Bad Request
			// Filter out empty price inputs
			$prices = array_filter($prices, function($element)
			{
				return $element['new_decimal_price'] !== '';
			});
			// Check if 'prices' input array is now empty
			if( empty($prices) )
				$prices = false;
		}
		else
			$prices = false;
		// ##################### End Prices #####################

		// Required input has been validated, save the model
		$ticket = Auth::user()->tickets()->save($ticket);

		// Ticket has been created, let's connect it to trips
		// TODO Validate existence and ownership of trip IDs
		$ticket->trips()->sync( $trips );

		// Normalise base_prices array
		$base_prices = Helper::normaliseArray($base_prices);
		// Create base_prices
		foreach($base_prices as &$base_price)
		{
			$base_price = new Price($base_price);
		}
		$ticket->basePrices()->saveMany($base_prices);

		if($prices)
		{
			// Normalise prices array
			$prices = Helper::normaliseArray($prices);
			// Create prices
			foreach($prices as &$price)
			{
				$price = new Price($price);
			}
			$ticket->prices()->saveMany($prices);
		}

		// Ticket has been created, let's connect it to boats
		$boats = Input::get('boats');
		if( $boats && !empty($boats) ) // only if the parameter is given/submitted
		{
			$sync = array();
			foreach( $boats as $boat_id => $boatroom_id )
			{
				// The validator fails when boatroom_id is submitted as '' (which means null but is valid), so we have to conditionally route around it
				if( !empty($boatroom_id) )
				{
					$validator = Validator::make(
						array(
							'boat_id'     => $boat_id,
							'boatroom_id' => $boatroom_id
						),
						array(
							'boat_id'     => 'integer|exists:boats,id',
							'boatroom_id' => 'integer|exists:boatrooms,id'
						)
					);
				}
				else
				{
					$boatroom_id = null;
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

				$sync[$boat_id] = array('boatroom_id' => $boatroom_id);
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

		// ####################### Prices #######################
		if( Input::has('base_prices') )
		{
			$base_prices = Input::get('base_prices');
			if( !is_array($base_prices) )
				return Response::json( array( 'errors' => array('The "base_prices" value must be of type array!')), 400 ); // 400 Bad Request
			// Filter out empty price inputs
			$base_prices = array_filter($base_prices, function($element)
			{
				return $element['new_decimal_price'] !== '';
			});
			// Check if 'base_prices' input array is now empty
			if( empty($base_prices) )
				$base_prices = false;
		}
		else
			$base_prices = false;

		if( Input::has('prices') )
		{
			$prices = Input::get('prices');
			if( !is_array($prices) )
				return Response::json( array( 'errors' => array('The "prices" value must be of type array!')), 400 ); // 400 Bad Request
			// Filter out empty price inputs
			$prices = array_filter($prices, function($element)
			{
				return $element['new_decimal_price'] !== '';
			});
			// Check if 'prices' input array is now empty
			if( empty($prices) )
				$prices = false;
		}
		else
			$prices = false;
		// ##################### End Prices #####################

		// Check if 'trips' input is an array, if given
		$trips = Input::get('trips');
		if( Input::has('trips') && !is_array($trips) )
			return Response::json( array( 'errors' => array('The "trips" value must be an array!')), 400 ); // 400 Bad Request

		// Check if a booking exists for the ticket and whether a critical value is updated
		if( $ticket->bookingdetails()->count() > 0 && (
			   (!empty($trips) && $this->checkRemovedTripBookings($ticket->id, $ticket->trips()->lists('id'), $trips))
			|| ($base_prices   && $this->checkPricesChanged($ticket->base_prices, $base_prices, true))
			|| ($prices        && $this->checkPricesChanged($ticket->prices, $prices))
		) )
		{
			// If yes, create a new ticket with the input data

			$data['base_prices'] = $base_prices;

			// Only submit $prices, when input has been submitted: Otherwise, all seasonal prices are removed.
			if( $prices )
				$data['prices'] = $prices;

			// Replace all unavailable input data with data from the old ticket object
			if( empty($data['name']) )        $data['name']        = $ticket->name;
			if( empty($data['description']) ) $data['description'] = $ticket->description;
			if( empty($data['base_prices']) ) $data['base_prices'] = $ticket->base_prices;
			// if( empty($data['prices']) )      $data['prices']      = $ticket->prices;

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
						$data['boats'][$boat->id] = $boat->pivot->boatroom_id;
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
			$originalInput = Request::input();
			$data['_token'] = Input::get('_token');
			$request = Request::create('api/ticket/add', 'POST', $data);
			Request::replace($request->input());
			$response = Route::dispatch($request);
			Request::replace($originalInput);

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

			if( $base_prices && $this->checkPricesChanged($ticket->base_prices, $base_prices, true) )
			{
				// Delete old base_prices
				$ticket->basePrices()->delete();

				// Normalise base_prices array
				$base_prices = Helper::normaliseArray($base_prices);

				// Create new base_prices
				foreach($base_prices as &$base_price)
				{
					$base_price = new Price($base_price);
				}
				$ticket->basePrices()->saveMany($base_prices);

				$base_prices = true; // Signal the front-end to reload the form to show the new base_price IDs
			}
			else
				$base_prices = false; // Signal the front-end to NOT reload the form, because the base_price IDs didn't change

			if( $prices && $this->checkPricesChanged($ticket->prices, $prices) )
			{
				// Delete old prices
				$ticket->prices()->delete();

				// Normalise prices array
				$prices = Helper::normaliseArray($prices);

				// Create new prices
				foreach($prices as &$price)
				{
					$price = new Price($price);
				}
				$ticket->prices()->saveMany($prices);

				$prices = true; // Signal the front-end to reload the form to show the new price IDs
			}
			elseif( !$prices )
			{
				$ticket->prices()->delete();
				$prices = false; // Signal the front-end to NOT reload the form, because the price IDs didn't change
			}
			else
				$prices = false; // Signal the front-end to NOT reload the form, because the price IDs didn't change

			if( Input::has('boats') )
			{
				// Ticket has been updated, let's connect it to boats
				$boats = Input::get('boats');
				if( $boats && !empty($boats) ) // only if the parameter is given/submitted
				{
					$sync = array();
					foreach( $boats as $boat_id => $boatroom_id )
					{
						// If the boat array is submitted empty, meaning all boats should be detached, skip all this and go directly to sync
						if( empty($boat_id) )
						{
							$sync = array();
							break;
						}

						// The validator fails when boatroom_id is submitted as '' (which means null but is valid), so we have to conditionally route around it
						if( !empty($boatroom_id) )
						{
							$validator = Validator::make(
								array(
									'boat_id'     => $boat_id,
									'boatroom_id' => $boatroom_id
								),
								array(
									'boat_id'     => 'integer|exists:boats,id',
									'boatroom_id' => 'integer|exists:boatrooms,id'
								)
							);
						}
						else
						{
							$boatroom_id = null;
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

						$sync[$boat_id] = array('boatroom_id' => $boatroom_id);
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
			return array('status' => 'OK. Ticket updated', 'base_prices' => $base_prices, 'prices' => $prices);
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

	protected function checkPricesChanged($old_prices, $prices, $isBase = false)
	{
		$old_prices = $old_prices->toArray();

		// Compare number of prices
		if(count($prices) !== count($old_prices)) return true;

		// Keyify $old_prices and reduce them to input fields
		$array = array();
		$input_keys = array('decimal_price' => '', 'from' => '');
		if(!$isBase)
			$input_keys['until'] = '';

		foreach($old_prices as $old_price)
		{
			$array[ $old_price['id'] ] = array_intersect_key($old_price, $input_keys);
		}
		$old_prices = $array; unset($array);

		// Compare price IDs
		if( count( array_merge( array_diff_key($prices, $old_prices), array_diff_key($old_prices, $prices) ) ) > 0 )
			return true;

		/**
		 * The following comparison works, because `array_diff` only compares the values of the arrays, not the keys.
		 * The $prices arrays have a `new_decimal_price` key, while the $old_prices arrays have a `decimal_price` key,
		 * but since they represent the same info, the comparison works and returns the expected result.
		 */
		foreach($old_prices as $id => $old_price)
		{
			// Compare arrays in both directions
			if( count( array_merge( array_diff($prices[$id], $old_price), array_diff($old_price, $prices[$id]) ) ) > 0 )
				return true;
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
