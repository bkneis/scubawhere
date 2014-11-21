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

	public function getFilter()
	{
		/**
		 * Valid input parameter
		 * accommodation_id
		 * after
		 * before
		 * with_full
		 */

		$data = Input::only('after', 'before', 'accommodation_id');
		$data['with_full'] = Input::get('with_full', false);

		// Transform parameter strings into DateTime objects
		$data['after']  = new DateTime( $data['after'] ); // Defaults to NOW, when parameter is NULL
		if( empty( $data['before'] ) )
		{
			if( $data['after'] > new DateTime('now') )
			{
				// If the submitted `after` date lies in the future, move the `before` date to return 1 month of results
				$data['before'] = clone $data['after']; // Shallow copies without reference to cloned object
				$data['before']->add( new DateInterval('P1M') ); // Extends the date 1 month into the future
			}
			else
			{
				// If 'after' date lies in the past or is NOW, return results up to 1 month into the future
				$data['before'] = new DateTime('+1 month');
			}
		}
		else
		{
			// If a 'before' date is submitted, simply use it
			$data['before'] = new DateTime( $data['before'] );
		}

		if( $data['after'] > $data['before'] )
		{
			return Response::json( array('errors' => array('The supplied \'after\' date is later than the given \'before\' date.')), 400 ); // 400 Bad Request
		}

		// Check the integrity of the supplied parameters
		$validator = Validator::make( $data, array(
			'after'            => 'date|required_with:before',
			'before'           => 'date',
			'accommodation_id' => 'integer|min:1',
			'with_full'        => 'boolean'
		) );

		if( $validator->fails() )
			return Response::json( array('errors' => $validator->messages()->all()), 400 ); // 400 Bad Request

		if( !empty( $data['accommodation_id'] ) )
		{
			try
			{
				$accommodation = Auth::user()->accommodations()->findOrFail( $data['accommodation_id'] );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The accommodation could not be found.')), 404 ); // 404 Not Found
			}
		}
		else
			$accommodation = false;

		$current_date = clone $data['after'];
		$result = array();

		$accommodations = Auth::user()->accommodations()->where(function($query) use ($accommodation)
		{
			if( $accommodation )
				$query->where('id', $accommodation->id);
		})
		->get();

		do
		{
			$key = $current_date->format('Y-m-d');

			$result[$key] = array();

			$accommodations->each(function($el) use ($key, &$result, $current_date)
			{
				$result[$key][$el->id] = array(
					$el->customers()->wherePivot('start', '<=', $current_date)->wherePivot('end', '>', $current_date)->count(),
					$el->capacity
				);
			});

			$current_date->add( new DateInterval('P1D') );
		}
		while( $current_date <= $data['before'] );

		return $result;
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

		$accommodation = new Accommodation($data);

		if( !$accommodation->validate() )
		{
			return Response::json( array('errors' => $accommodation->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$accommodation = Auth::user()->accommodations()->save($accommodation);

		// Save prices
		$accommodation->basePrices()->saveMany($base_prices);
		if($prices)
		{
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
			   ($base_prices     && Helper::checkPricesChanged($accommodation->base_prices, $base_prices, true))
			|| ($prices          && Helper::checkPricesChanged($accommodation->prices, $prices))
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

			// Dispatch add-accommodation route with all data and return result
			$originalInput = Request::input();
			$data['_token'] = Input::get('_token');
			$request = Request::create('api/accommodation/add', 'POST', $data);
			Request::replace($request->input());
			return Route::dispatch($request);
			Request::replace($originalInput);
		}
		else
		{
			$base_prices_changed = $base_prices && Helper::checkPricesChanged($accommodation->base_prices, $base_prices, true);
			$prices_changed      = $prices && Helper::checkPricesChanged($accommodation->prices, $prices);

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

			if( !$accommodation->update($data) )
			{
				return Response::json( array('errors' => $accommodation->errors()->all()), 406 ); // 406 Not Acceptable
			}

			if( $base_prices_changed )
			{
				// Delete old base_prices
				$accommodation->basePrices()->delete();
				$accommodation->basePrices()->saveMany($base_prices);

				$base_prices = true; // Signal the front-end to reload the form to show the new base_price IDs
			}

			if( $prices_changed )
			{
				// Delete old prices
				$accommodation->prices()->delete();
				$accommodation->prices()->saveMany($prices);

				$prices = true; // Signal the front-end to reload the form to show the new price IDs
			}
			elseif( !$prices )
			{
				$accommodation->prices()->delete();
			}

			return array('status' => 'OK. Accommodation updated', 'base_prices' => $base_prices, 'prices' => $prices);
		}
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

		// If deletion worked, delete associated prices
		Price::where(Price::$owner_id_column_name, $id)->where(Price::$owner_type_column_name, 'Accommodation')->delete();

		return array('status' => 'Ok. Accommodation deleted');
	}

}
