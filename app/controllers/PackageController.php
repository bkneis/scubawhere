<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;

class PackageController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->packages()->withTrashed()->with('tickets')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->packages()->with('tickets', 'tickets.prices', 'prices')->get();
	}

	public function getAllWithTrashed()
	{
		return Auth::user()->packages()->withTrashed()->with('tickets', 'tickets.prices', 'prices')->get();
	}

	public function postAdd()
	{
		$data = Input::only('name', 'description', 'capacity');

		// Validate that tickets are supplied
		$tickets = Input::get('tickets');
		if( empty($tickets) )
			return Response::json( array('errors' => array('At least one ticket is required.')), 406 ); // 406 Not Acceptable

		$prices = Input::get('prices');
		// Filter out empty price inputs
		$prices = array_filter($prices, function($element)
		{
			return $element['new_decimal_price'] !== '';
		});
		// Check if 'prices' input array is given and not empty
		if( !is_array($prices) || empty($prices) )
			return Response::json( array( 'errors' => array('The "prices" value must be an array and cannot be empty!')), 400 ); // 400 Bad Request

		if( $data['capacity'] == 0)
			$data['capacity'] = null;

		$package = new Package($data);

		if( !$package->validate() )
		{
			return Response::json( array('errors' => $package->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$package = Auth::user()->packages()->save($package);

		// Package has been created, let's connect it with its tickets
		// TODO Validate input
		/**
		 * ticket_id => 'required|exists:tickets,id', // validate ownership
		 * quantity  => 'required|integer|min:1'
		 */
		// Input must be of type <input name="tickets[1][quantity]" value="2">
		//                                ticket_id --^   quantity value --^
		$package->tickets()->sync( $tickets );

		// Normalise prices array
		$prices = Helper::normaliseArray($prices);
		// Create prices
		foreach($prices as &$price)
		{
			$price = new Price($price);
		}
		$package->prices()->saveMany($prices);

		return Response::json( array('status' => 'Package created and connected OK', 'id' => $package->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		$data = Input::only('name', 'description', 'capacity');

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$package = Auth::user()->packages()->withTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
		}

		// Validate that tickets are supplied
		$tickets = Input::get('tickets');
		if( empty($tickets) )
			return Response::json( array('errors' => array('At least one ticket is required.')), 406 ); // 406 Not Acceptable

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

		if( $data['capacity'] == 0)
			$data['capacity'] = null;

		// Check if a booking exists for the package and whether a critical value is updated
		if( $package->bookingdetails()->count() > 0 && (
			   (!empty($tickets) && $this->checkTicketsChanged($package->tickets, $tickets))
			|| ($prices          && $this->checkPricesChanged($package->prices, $prices))
		) )
		{
			// If yes, create a new package with the input data
			$data['prices'] = $prices;

			// Replace all unavailable input data with data from the old package object
			if( empty($data['name']) )        $data['name']        = $package->name;
			if( empty($data['description']) ) $data['description'] = $package->description;
			if( empty($data['capacity']) )    $data['capacity']    = $package->capacity;
			if( empty($data['prices']) )      $data['prices']      = $package->prices;

			if( !Input::has('tickets') )
			{
				// Convert $package->tickets into input format
				$data['tickets'] = array();
				foreach($package->tickets as $ticket) // Includes pivot data by default
				{
					$data['tickets'][$ticket->id] = $ticket->pivot->quantity;
				}
			}
			else {
				$data['tickets'] = Input::get('tickets');
			}

			// SoftDelete the old ticket
			$package->delete();

			// TODO MAYBE: Unconnect the original ticket from boats

			// Dispatch add-ticket route with all data and return result
			$request = Request::create('api/package/add', 'POST', $data);
			return Route::dispatch($request);
		}
		else
		{
			if( !$package->update($data) )
			{
				return Response::json( array('errors' => $package->errors()->all()), 406 ); // 406 Not Acceptable
			}

			// Package has been created, let's connect it with its tickets
			// TODO Validate input
			/**
			 * ticket_id => 'required|exists:tickets,id', // validate ownership
			 * quantity  => 'required|integer|min:1'
			 */
			// Input must be of type <input name="tickets[1][quantity]" value="2">
			//                                ticket_id --^   quantity value --^
			if( Input::has('tickets') )
				$package->tickets()->sync( Input::get('tickets') );

			if( Input::has('prices') )
			{
				// Delete old prices
				$package->prices()->delete();

				// Create new prices
				foreach($prices as &$price)
				{
					$price = new Price($price);
				}
				$package->prices()->saveMany($prices);
			}

			return array('status' => 'Package edited OK', 'prices' => $package->prices()->get());
		}
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

	protected function checkTicketsChanged($old_tickets, $tickets)
	{
		$old_tickets = $old_tickets->toArray();

		if(count($tickets) !== count($old_tickets)) return true;

		// Convert $old_tickets into same format as $tickets
		$array = [];
		foreach($old_tickets as $old_ticket)
		{
			$array[$old_ticket['id']]['quantity'] = $old_ticket['pivot']['quantity'];
		}
		$old_tickets = $array;

		// Compare keys (both ways)
		if( count( array_diff_key($old_tickets, $tickets) ) > 0 )
			return true;
		if( count( array_diff_key($tickets, $old_tickets) ) > 0 )
			return true;

		// Compare each quantity
		foreach($old_tickets as $key => $old_ticket)
		{
			if( $old_ticket['quantity'] != $tickets[$key]['quantity'] ) // Needs to compare int with numerical string
				return true;
		}

		return false;
	}

	public function postDeactivate()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$package = Auth::user()->packages()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
		}

		$package->delete();

		return array('status' => 'OK. Package deactivated');
	}

	public function postRestore()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$package = Auth::user()->packages()->onlyTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
		}

		$package->restore();

		return array('status' => 'OK. Package restored');
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$package = Auth::user()->packages()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
		}

		$id = $package->id;

		try
		{
			$package->forceDelete();
		}
		catch(QueryException $e)
		{
			return Response::json( array('errors' => array('The package can not be removed because it has been booked at least once. Try deactivating it instead.')), 409); // 409 Conflict
		}

		// If deletion worked, delete associated prices
		Price::where(Price::$owner_id_column_name, $id)->where(Price::$owner_type_column_name, 'Package')->delete();

		return array('status' => 'Ok. Package deleted');
	}

}
