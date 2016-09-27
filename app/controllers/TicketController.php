<?php
use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends Controller {

    protected $log_service;

    public function __construct(LogService $log_service)
    {
        $this->log_service = $log_service;
    }

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->tickets()->withTrashed()->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Context::get()->tickets()->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')->get();
	}

	public function getAllWithTrashed()
	{
		return Context::get()->tickets()->withTrashed()->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')->get();
	}

	public function getOnlyAvailable() {
		$now = Helper::localTime();
		$now = $now->format('Y-m-d');

		return Context::get()->tickets()
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

		$data['only_packaged'] = Input::get('only_packaged', false);

		$ticket = new Ticket($data);

		if( !$ticket->validate() )
		{
			return Response::json( array('errors' => $ticket->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// Check if 'trips' input array is given and not empty
		$trips = Input::get('trips', []);
		if( !is_array($trips) || empty($trips) )
			return Response::json( array( 'errors' => array('Please specify at least one eligable trip.')), 400 ); // 400 Bad Request

		if(!$ticket->only_packaged)
		{
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
		}

		// Required input has been validated, save the model
		$ticket = Context::get()->tickets()->save($ticket);

		// Ticket has been created, let's connect it to trips
		// TODO Validate existence and ownership of trip IDs
		$ticket->trips()->sync( $trips );

		if(!$ticket->only_packaged)
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
			$ticket = Context::get()->tickets()->withTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only('name', 'description', 'available_from', 'available_until', 'available_for_from', 'available_for_until');

		$data['only_packaged'] = Input::get('only_packaged', false); // When the checkbox is not checked, not even the key is submitted. So when no key is submitted, the checkbox is not set and thus FALSE.

		// Check if only_packaged changed from TRUE to FALSE and if so, check that at least one price is submitted
		if($ticket->only_packaged == true && $data['only_packaged'] == false && $ticket->basePrices->count() === 0 && !Input::has('base_price'))
			return Response::json( array( 'errors' => array('At least one base price must be given.')), 406 ); // 406 Not Acceptable

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
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
            $ticket = Context::get()->tickets()
									->with(['packages', 'courses',
									'bookingdetails.session' => function($q) {
										$q->where('start', '>=', Helper::localtime());
									}])
                                    ->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}

		$booking_ids = $ticket->bookingdetails
							  ->map(function($obj) {
							      if($obj->session != null || $obj->training_session != null)
							      {
								      return $obj->booking_id;
								  }
							  })
							  ->toArray();

		$bookings = Context::get()->bookings()
								  ->whereIn('id', $booking_ids)
								  ->get(['reference', 'status']);

		$quotes = $bookings->map(function($obj) {
			if($obj->status == 'saved') return $obj->id;
		});

		Context::get()->bookings()->whereIn('id', $quotes)->delete();

		$bookings = $bookings->map(function($obj){
			if($obj->status != 'cancelled' && $obj->status != 'saved') return $obj;	
		})->toArray();

		$bookings = array_filter($bookings, function($obj){ return !is_null($obj); });

		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the ticket, '
												. $ticket->name);
			foreach($bookings as $obj) 
			{
				$logger->append('The ticket is used in the future in booking ' . '['.$obj['reference'].']');
			}

			return Response::json(
				array('errors' => 
					array('The ticket could not be deleted as it is used in bookings in the future, '.
						'Please visit the error logs for more info on how to delete it.')
				), 409); // Conflict
		}


        if(!$ticket->deleteable)
        {
            // Remove the ticket from all packages, as packages don't require a ticket, there is no checks
            foreach($ticket->packages as $obj) 
            {
                DB::table('packageables')
                    ->where('package_id', $obj->id)
                    ->where('packageable_type', 'Ticket')
                    ->where('packageable_id', $ticket->id)
                    ->update(array('deleted_at' => DB::raw('NOW()')));    
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
                    $logger->append('The ticket can not be deleted as the course ' . $obj->name . ' requires atleast one ticket or class, please delete or edit the course so that the ticket can be deleted');
                }
                return Response::json(
                            array('errors' => 
                                array('The ticket cannot be deleted as it has courses that depend on it, please visit the error logs tab to see more information on how to fix this.')
                            ), 409);
            }
            // Otherwise soft delete the pivots to the courses
            else
            {
                foreach($ticket->courses as $obj) 
                {
                    DB::table('packageables')
                        ->where('package_id', $obj->id)
                        ->where('packageable_type', 'Course')
                        ->where('packageable_id', $ticket->id)
                        ->update(array('deleted_at' => DB::raw('NOW()')));    
                }
            }
        }

        $ticket->delete();

		Price::where(Price::$owner_id_column_name, $ticket->id)
			->where(Price::$owner_type_column_name, 'Ticket')
			->delete();

		return array('status' => 'OK. Ticket deleted');
	}

    /*
     *
	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$ticket = Context::get()->tickets()->findOrFail( Input::get('id') );
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
     */
}
