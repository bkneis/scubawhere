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
			return Auth::user()->tickets()->withTrashed()->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->tickets()->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')->get();
	}

	public function getAllWithTrashed()
	{
		return Auth::user()->tickets()->withTrashed()->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')->get();
	}

	public function postAdd()
	{
		$data = Input::only('name', 'description', 'parent_id'); // Please NEVER use parent_id in the front-end!

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

		// Normalise base_prices array
		$base_prices = Helper::normaliseArray($base_prices);
		// Create base_prices
		foreach($base_prices as &$base_price)
		{
			$base_price = new Price($base_price);

			if( !$base_price->validate() )
				return Response::json( array('errors' => $base_price->errors()->all()), 406 ); // 406 Not Acceptable
		}

		if($prices)
		{
			// Normalise prices array
			$prices = Helper::normaliseArray($prices);
			// Create prices
			foreach($prices as &$price)
			{
				$price = new Price($price);

				if( !$price->validate() )
					return Response::json( array('errors' => $price->errors()->all()), 406 ); // 406 Not Acceptable
			}
		}

		// Required input has been validated, save the model
		$ticket = Auth::user()->tickets()->save($ticket);

		// Ticket has been created, let's connect it to trips
		// TODO Validate existence and ownership of trip IDs
		$ticket->trips()->sync( $trips );

		// Save prices
		$ticket->basePrices()->saveMany($base_prices);
		if($prices)
		{
			$ticket->prices()->saveMany($prices);
		}

		// Ticket has been created, let's connect it to boats
		$boats = Input::get('boats');
		if( $boats && !empty($boats) ) // only if the parameter is given/submitted
		{
			// TODO Validate that all boat_ids belong to company
			$ticket->boats()->sync( $boats );
		}

		// Ticket has been created, let's connect it to boatrooms
		$boatrooms = Input::get('boatrooms');
		if( $boatrooms && !empty($boatrooms) ) // only if the parameter is given/submitted
		{
			// TODO Validate that all boatroom_ids belong to company
			$ticket->boatrooms()->sync( $boatrooms );
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
		if( $ticket->has_bookings && (
			   (!empty($trips) && $this->checkRemovedTripBookings($ticket->id, $ticket->trips()->lists('id'), $trips))
			|| ($base_prices   && Helper::checkPricesChanged($ticket->base_prices, $base_prices, true))
			|| ($prices        && Helper::checkPricesChanged($ticket->prices, $prices))
		) )
		{
			// If yes, create a new ticket with the input data

			$data['base_prices'] = $base_prices;

			// Only submit $prices, when input has been submitted: Otherwise, all seasonal prices are removed.
			if( $prices )
				$data['prices'] = $prices;

			$data['parent_id'] = $ticket->id;

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
			}

			if( Input::has('boatrooms') )
			{
				$boatrooms = Input::get('boatrooms');
				if( !empty( $boatrooms ) )
					$data['boatrooms'] = $boatrooms;
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
			$base_prices_changed = $base_prices && Helper::checkPricesChanged($ticket->base_prices, $base_prices, true);
			$prices_changed      = $prices && Helper::checkPricesChanged($ticket->prices, $prices);

			if($base_prices_changed)
			{
				// Normalise base_prices array
				$base_prices = Helper::normaliseArray($base_prices);
				// Create base_prices
				foreach($base_prices as &$base_price)
				{
					$base_price = new Price($base_price);

					if( !$base_price->validate() )
						return Response::json( array('errors' => $base_price->errors()->all()), 406 ); // 406 Not Acceptable
				}
			}

			if($prices_changed)
			{
				// Normalise prices array
				$prices = Helper::normaliseArray($prices);
				// Create prices
				foreach($prices as &$price)
				{
					Clockwork::info($price);
					$price = new Price($price);
					Clockwork::info($price);

					if( !$price->validate() )
						return Response::json( array('errors' => $price->errors()->all()), 406 ); // 406 Not Acceptable
				}
			}

			// If not, simply update it
			if( !$ticket->update($data) )
				return Response::json( array('errors' => $ticket->errors()->all()), 406 ); // 406 Not Acceptable

			if( $base_prices_changed )
			{
				// Delete old base_prices
				$ticket->basePrices()->delete();
				$ticket->basePrices()->saveMany($base_prices);

				$base_prices = true; // Signal the front-end to reload the form to show the new base_price IDs
			}

			if( $prices_changed )
			{
				// Delete old prices
				$ticket->prices()->delete();
				$ticket->prices()->saveMany($prices);

				$prices = true; // Signal the front-end to reload the form to show the new price IDs
			}
			elseif( !$prices )
			{
				$ticket->prices()->delete();
			}

			// Ticket has been updated, let's connect it to boats
			$boats = Input::get('boats');
			if( $boats && !empty($boats) ) // only if the parameter is given/submitted
			{
				// TODO Validate that all boat_ids belong to company
				$ticket->boats()->sync( $boats );
			}
			else
			{
				// Remove all boats from this ticket
				$ticket->boats()->detach();
			}

			// Ticket has been updated, let's connect it to boatrooms
			$boatrooms = Input::get('boatrooms');
			if( $boatrooms && !empty($boatrooms) ) // only if the parameter is given/submitted
			{
				// TODO Validate that all boatroom_ids belong to company
				$ticket->boatrooms()->sync( $boatrooms );
			}
			else
			{
				// Remove all boatrooms from this ticket
				$ticket->boatrooms()->detach();
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
