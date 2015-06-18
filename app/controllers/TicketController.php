<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;

class TicketController extends Controller {

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

	public function getOnlyAvailable() {
		$now = Helper::localTime();
		$now = $now->format('Y-m-d');

		return Auth::user()->tickets()
			->where(function($query) use ($now)
			{
				$query->whereNull('available_from')->orWhere('available_from', '<=', $now);
			})
			->where(function($query) use ($now)
			{
				$query->whereNull('available_until')->orWhere('available_until', '>=', $now);
			})
			->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')->get();
	}

	public function postAdd()
	{
		$data = Input::only('name', 'description', 'parent_id', 'available_from', 'available_until', 'available_for_from', 'available_for_until'); // Please NEVER use parent_id in the front-end!

		$ticket = new Ticket($data);

		if( !$ticket->validate() )
		{
			return Response::json( array('errors' => $ticket->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// Check if 'trips' input array is given and not empty
		$trips = Input::get('trips', []);
		if( !is_array($trips) || empty($trips) )
			return Response::json( array( 'errors' => array('Please specify at least one eligable trip.')), 400 ); // 400 Bad Request

		// ####################### Prices #######################
		$base_prices = Input::get('base_prices');
		if( !is_array($base_prices) )
			return Response::json( array( 'errors' => array('The "base_prices" value must be of type array!')), 400 ); // 400 Bad Request

		// Filter out empty and existing prices
		$base_prices = Helper::cleanPriceArray($base_prices);

		// Check if 'prices' input array is now empty
		if( empty($base_prices) )
			return Response::json( array( 'errors' => array('You must submit at least one base price!')), 400 ); // 400 Bad Request

		if( Input::has('prices') )
		{
			$prices = Input::get('prices');
			if( !is_array($prices) )
				return Response::json( array( 'errors' => array('The "prices" value must be of type array!')), 400 ); // 400 Bad Request

			// Filter out empty and existing prices
			$prices = Helper::cleanPriceArray($prices);

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
		// Create new base_prices
		foreach($base_prices as &$base_price)
		{
			$base_price = new Price($base_price);

			if( !$base_price->validate() )
				return Response::json( array('errors' => $base_price->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$ticket->basePrices()->saveMany($base_prices);

		if($prices)
		{
			// Normalise prices array
			$prices = Helper::normaliseArray($prices);
			// Create new prices
			foreach($prices as &$price)
			{
				$price = new Price($price);

				if( !$price->validate() )
					return Response::json( array('errors' => $price->errors()->all()), 406 ); // 406 Not Acceptable
			}

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

		$data = Input::only('name', 'description', 'available_from', 'available_until', 'available_for_from', 'available_for_until');

		// ####################### Prices #######################
		if( Input::has('base_prices') )
		{
			$base_prices = Input::get('base_prices');
			if( !is_array($base_prices) )
				return Response::json( array( 'errors' => array('The "base_prices" value must be of type array!')), 400 ); // 400 Bad Request

			// Filter out empty and existing prices
			$base_prices = Helper::cleanPriceArray($base_prices);

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

			// Filter out empty and existing prices
			$prices = Helper::cleanPriceArray($prices);

			// Check if 'prices' input array is now empty
			if( empty($prices) )
				$prices = false;
		}
		else
			$prices = false;
		// ##################### End Prices #####################

		// Check if 'trips' input array is given and not empty
		$trips = Input::get('trips', []);
		if( !is_array($trips) || empty($trips) )
			return Response::json( array( 'errors' => array('Please specify at least one eligable trip.')), 400 ); // 400 Bad Request

		// Check if a booking exists for the ticket and whether a critical value is updated
		/*if( $this->checkRemovedTripBookings($ticket->id, $ticket->trips()->lists('id'), $trips) )
			return Response::json(['errors' => ['Some trips cannot be removed, because the ticket has been booked for those trips already.']], 403); // 403 Forbidden*/

		// If not, simply update it
		if( !$ticket->update($data) )
			return Response::json( array('errors' => $ticket->errors()->all()), 406 ); // 406 Not Acceptable

		if($base_prices)
		{
			// Normalise base_prices array
			$base_prices = Helper::normaliseArray($base_prices);
			// Create new base_prices
			foreach($base_prices as &$base_price)
			{
				$base_price = new Price($base_price);

				if( !$base_price->validate() )
					return Response::json( array('errors' => $base_price->errors()->all()), 406 ); // 406 Not Acceptable
			}

			$base_prices = $ticket->basePrices()->saveMany($base_prices);
		}

		if($prices)
		{
			// Normalise prices array
			$prices = Helper::normaliseArray($prices);
			// Create new prices
			foreach($prices as &$price)
			{
				$price = new Price($price);

				if( !$price->validate() )
					return Response::json( array('errors' => $price->errors()->all()), 406 ); // 406 Not Acceptable
			}

			$prices = $ticket->prices()->saveMany($prices);
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

	/*
		No longer necessary
	 */
	/*protected function checkRemovedTripBookings($ticket_id, $old_trips, $new_trips)
	{
		// Check, which tripIDs have been removed
		$removed_trips = array_diff($old_trips, $new_trips);

		if( count($removed_trips) === 0 )
			return false;

		// Now check if any of these removed trips have already been booked with this ticket
		$result = Departure::whereIn('trip_id', $removed_trips)
		    ->whereHas('bookingdetails', function($query) use ($ticket_id)
		    {
		    	$query->where('ticket_id', $ticket_id);
		    })->exists();

		return $result;
	}*/

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

		if($ticket->packages()->exists() || $ticket->courses()->exists())
			return Response::json( array('errors' => array('The ticket can not be removed currently because it is used in packages or courses.')), 409); // 409 Conflict

		try
		{
			$ticket->forceDelete();

			// If deletion worked, delete associated prices
			Price::where(Price::$owner_id_column_name, $ticket->id)->where(Price::$owner_type_column_name, 'Ticket')->delete();
		}
		catch(QueryException $e)
		{
			return Response::json( array('errors' => array('The ticket can not be removed currently because it has been booked at least once.')), 409); // 409 Conflict
		}

		return array('status' => 'OK. Ticket deleted');
	}

}
