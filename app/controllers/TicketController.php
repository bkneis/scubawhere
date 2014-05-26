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
			return Auth::user()->tickets()->where('active', '=', 1)->with('boats')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->tickets()->where('active', '=', 1)->with('boats')->get();
	}

	public function postAdd()
	{
		$data = Input::only('trip_id', 'name', 'description', 'price', 'currency');
		$data['currency'] = Helper::currency( Input::get('currency') );

		try
		{
			if( empty( $data['trip_id'] ) ) throw new ModelNotFoundException();
			$trip = Auth::user()->trips()->findOrFail( $data['trip_id'] );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		$ticket = new Ticket($data);

		if( !$ticket->validate() )
		{
			return Response::json( array('errors' => $ticket->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// Input has been validated, save the model
		$ticket = $trip->tickets()->save($ticket);

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
							'boat_id' => $boat_id,
							'accommodation_id' => $accommodation_id
						),
						array(
							'boat_id' => 'integer|exists:boats,id',
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
		return Response::json( array('status' => 'Created and connected OK', 'id' => $ticket->id), 201); // 201 Created
	}

	public function postEdit()
	{
		// Check if the ticket exists
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$ticket = Auth::user()->tickets()->where('active', '=', 1)->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}

		// Check if a booking exists for the ticket and whether a critical value is updated
		if( $ticket->bookings()->count() > 0 && (
			   (Input::get('trip_id')  && Input::get('trip_id') != $ticket->trip_id)
			|| (Input::get('price')    && Input::get('price') != $ticket->price)
			|| (Input::get('currency') && Helper::currency( Input::get('currency') ) != $ticket->currency)
		) )
		{
			// If yes, create a new ticket with the input data
			$data = Input::only('trip_id', 'name', 'description', 'price', 'currency', 'boats');

			// Replace all unavailable input data with data from the old ticket object
			if( empty($data['trip_id']) )     $data['trip_id']     = $ticket->trip_id;
			if( empty($data['name']) )        $data['name']        = $ticket->name;
			if( empty($data['description']) ) $data['description'] = $ticket->description;
			if( empty($data['price']) )       $data['price']       = $ticket->price;
			if( empty($data['currency']) )    $data['currency']    = $ticket->currency;
			if( empty($data['boats']) )
			{
				$data['boats'] = array();
				foreach($ticket->boats() as $boat)
				{
					$data['boats'][$boat->id] = $boat->pivot->accommodation_id;
				}
			}

			// "Delete" the old ticket
			$ticket->update( array('active' => 0) );

			// MAYBE: Unconnect the original ticket from boats

			// Dispatch add-ticket route with all data and return result
			$request = Request::create('api/ticket/add', 'POST', $data);
			return Route::dispatch($request);

		}
		else
		{
			// If not, simply update it
			$data = Input::only('trip_id', 'name', 'description', 'price', 'currency');
			if( !$ticket->update($data) )
			{
				return Response::json( array('errors' => $ticket->errors()->all()), 406 ); // 406 Not Acceptable
			}
			else
			{
				// Ticket has been updated, let's reconnect it

				// Connect boats
				$boats = Input::get('boats');
				if( $boats && !empty($boats) )
				{
					$sync = array();
					foreach( $boats as $boat_id => $accommodation_id )
					{
						$validator = Validator::make(
							array(
								'boat_id' => $boat_id,
								'accommodation_id' => $accommodation_id
							),
							array(
								'boat_id' => 'integer|exists:boats,id',
								'accommodation_id' => 'required|integer|exists:accommodations,id'
							)
						);

						if( $validator->fails() )
						{
							return Response::json( array('errors' => $validator->messages()->all()), 406 ); // 406 Not Acceptable
						}

						$sync[$id] = array('accommodation_id' => $accommodation_id);
					}
					$ticket->boats()->sync( $sync );
				}

				// When no problems occur, we return a success response
				return Response::json( array('status' => 'OK. Ticket updated'), 200 ); // 200 OK
			}
		}
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$ticket = Auth::user()->tickets()->where('active', '=', 1)->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The ticket could not be found.')), 404 ); // 404 Not Found
		}

		$ticket->update( array('active' => 0) );

		return array('status' => 'Ticket deleted OK');
	}

}
