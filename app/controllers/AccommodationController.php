<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;

class AccommodationController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->accommodations()->withTrashed()->with('basePrices', 'prices')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The accommodation could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->accommodations()->with('basePrices', 'prices')->get();
	}

	public function getAllWithTrashed()
	{
		return Auth::user()->accommodations()->withTrashed()->with('basePrices', 'prices')->get();
	}

	public function postAdd()
	{
		$data = Input::only('name',	'description', 'capacity');

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

		$accommodation = new Accommodation($data);

		if( !$accommodation->validate() )
		{
			return Response::json( array('errors' => $accommodation->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$accommodation = Auth::user()->accommodations()->save($accommodation);

		// Normalise base_prices array
		$base_prices = Helper::normaliseArray($base_prices);
		// Create base_prices
		foreach($base_prices as &$base_price)
		{
			$base_price = new Price($base_price);
		}
		$accommodation->basePrices()->saveMany($base_prices);

		if($prices)
		{
			// Normalise prices array
			$prices = Helper::normaliseArray($prices);
			// Create prices
			foreach($prices as &$price)
			{
				$price = new Price($price);
			}
			$accommodation->prices()->saveMany($prices);
		}

		return Response::json( array('status' => 'OK. Accommodation created', 'id' => $accommodation->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$accommodation = Auth::user()->accommodations()->withTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The accommodation could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only('name',	'description', 'capacity');

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

		// Check if a booking exists for the accommodation and whether a critical value is updated
		if( $accommodation->bookings()->count() > 0 && (
			   ($base_prices     && $this->checkPricesChanged($accommodation->base_prices, $base_prices, true))
			|| ($prices          && $this->checkPricesChanged($accommodation->prices, $prices))
		) )
		{
			// If yes, create a new accommodation with the input data

			$data['base_prices'] = $base_prices;

			// Only submit $prices, when input has been submitted: Otherwise, all seasonal prices are removed.
			if( $prices )
				$data['prices'] = $prices;

			// Replace all unavailable input data with data from the old accommodation object
			if( empty($data['name']) )        $data['name']        = $accommodation->name;
			if( empty($data['description']) ) $data['description'] = $accommodation->description;
			if( empty($data['capacity']) )    $data['capacity']    = $accommodation->capacity;
			if( empty($data['base_prices']) ) $data['base_prices'] = $accommodation->base_prices;

			// SoftDelete the old ticket
			$accommodation->delete();

			// TODO MAYBE: Unconnect the original ticket from boats

			// Dispatch add-ticket route with all data and return result
			$request = Request::create('api/accommodation/add', 'POST', $data);
			return Route::dispatch($request);
		}
		else
		{
			if( !$accommodation->update($data) )
			{
				return Response::json( array('errors' => $accommodation->errors()->all()), 406 ); // 406 Not Acceptable
			}

			if( $base_prices && $this->checkPricesChanged($accommodation->base_prices, $base_prices, true) )
			{
				// Delete old base_prices
				$accommodation->basePrices()->delete();

				// Normalise base_prices array
				$base_prices = Helper::normaliseArray($base_prices);

				// Create new base_prices
				foreach($base_prices as &$base_price)
				{
					$base_price = new Price($base_price);
				}
				$accommodation->basePrices()->saveMany($base_prices);

				$base_prices = true; // Signal the front-end to reload the form to show the new base_price IDs
			}
			else
				$base_prices = false; // Signal the front-end to NOT reload the form, because the base_price IDs didn't change

			if( $prices && $this->checkPricesChanged($accommodation->prices, $prices) )
			{
				// Delete old prices
				$accommodation->prices()->delete();

				// Normalise prices array
				$prices = Helper::normaliseArray($prices);

				// Create new prices
				foreach($prices as &$price)
				{
					$price = new Price($price);
				}
				$accommodation->prices()->saveMany($prices);

				$prices = true; // Signal the front-end to reload the form to show the new price IDs
			}
			elseif( !$prices )
			{
				$accommodation->prices()->delete();
				$prices = false; // Signal the front-end to NOT reload the form, because the price IDs didn't change
			}
			else
				$prices = false; // Signal the front-end to NOT reload the form, because the price IDs didn't change

			return array('status' => 'OK. Accommodation updated', 'base_prices' => $base_prices, 'prices' => $prices);
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

	public function postDeactivate()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$accommodation = Auth::user()->accommodations()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The accommodation could not be found.')), 404 ); // 404 Not Found
		}

		$accommodation->delete();

		return array('status' => 'OK. Accommodation deactivated');
	}

	public function postRestore()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$accommodation = Auth::user()->accommodations()->onlyTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The accommodation could not be found.')), 404 ); // 404 Not Found
		}

		$accommodation->restore();

		return array('status' => 'OK. Accommodation restored');
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$accommodation = Auth::user()->accommodations()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The accommodation could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			$accommodation->forceDelete();
		}
		catch(QueryException $e)
		{
			return Response::json( array('errors' => array('The accommodation can not be removed because it has been booked at least once. Try deactivating it instead.')), 409); // 409 Conflict
		}

		return array('status' => 'Ok. Accommodation deleted');
	}

}
