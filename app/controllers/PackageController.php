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
			return Auth::user()->packages()->withTrashed()->with('tickets', 'basePrices', 'prices')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->packages()->with('tickets', 'basePrices', 'prices')->get();
	}

	public function getAllWithTrashed()
	{
		return Auth::user()->packages()->withTrashed()->with('tickets', 'basePrices', 'prices')->get();
	}

	public function postAdd()
	{
		$data = Input::only('name', 'description', 'capacity');

		// Validate that tickets are supplied
		$tickets = Input::get('tickets');
		if( empty($tickets) )
			return Response::json( array('errors' => array('At least one ticket is required.')), 406 ); // 406 Not Acceptable

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

		// Save prices
		$package->basePrices()->saveMany($base_prices);
		if($prices)
		{
			$package->prices()->saveMany($prices);
		}

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

		if( $data['capacity'] == 0)
			$data['capacity'] = null;

		// Check if a booking exists for the package and whether a critical value is updated
		if( $package->bookingdetails()->count() > 0 && (
			   (!empty($tickets) && $this->checkTicketsChanged($package->tickets, $tickets))
			|| ($base_prices     && $this->checkPricesChanged($package->base_prices, $base_prices, true))
			|| ($prices          && $this->checkPricesChanged($package->prices, $prices))
		) )
		{
			// If yes, create a new package with the input data

			$data['base_prices'] = $base_prices;

			// Only submit $prices, when input has been submitted: Otherwise, all seasonal prices are removed.
			if( $prices )
				$data['prices'] = $prices;

			// Replace all unavailable input data with data from the old package object
			if( empty($data['name']) )        $data['name']        = $package->name;
			if( empty($data['description']) ) $data['description'] = $package->description;
			if( empty($data['capacity']) )    $data['capacity']    = $package->capacity;
			if( empty($data['base_prices']) ) $data['base_prices'] = $package->base_prices;

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

			// Dispatch add-package route with all data and return result
			$originalInput = Request::input();
			$data['_token'] = Input::get('_token');
			$request = Request::create('api/package/add', 'POST', $data);
			Request::replace($request->input());
			return Route::dispatch($request);
			Request::replace($originalInput);
		}
		else
		{
			$base_prices_changed = $base_prices && $this->checkPricesChanged($package->base_prices, $base_prices, true);
			$prices_changed      = $prices && $this->checkPricesChanged($package->prices, $prices);

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
					$price = new Price($price);

					if( !$price->validate() )
						return Response::json( array('errors' => $price->errors()->all()), 406 ); // 406 Not Acceptable
				}
			}

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

			if( $base_prices_changed )
			{
				// Delete old base_prices
				$package->basePrices()->delete();
				$package->basePrices()->saveMany($base_prices);

				$base_prices = true; // Signal the front-end to reload the form to show the new base_price IDs
			}

			if( $prices_changed )
			{
				// Delete old prices
				$package->prices()->delete();
				$package->prices()->saveMany($prices);

				$prices = true; // Signal the front-end to reload the form to show the new price IDs
			}
			elseif( !$prices )
			{
				$package->prices()->delete();
			}

			return array('status' => 'OK. Package updated', 'base_prices' => $base_prices, 'prices' => $prices);
		}
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
		if( count( array_merge( array_diff_key($old_tickets, $tickets), array_diff_key($tickets, $old_tickets) ) ) > 0 )
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
