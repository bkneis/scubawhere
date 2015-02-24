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
		 */

		$data = Input::only('after', 'before', 'accommodation_id');

		// Transform parameter strings into DateTime objects
		$data['after']  = new DateTime( $data['after'], new DateTimeZone( Auth::user()->timezone ) ); // Defaults to NOW, when parameter is NULL
		if( empty( $data['before'] ) )
		{
			if( $data['after'] > new DateTime('now', new DateTimeZone( Auth::user()->timezone )) )
			{
				// If the submitted `after` date lies in the future, move the `before` date to return 1 month of results
				$data['before'] = clone $data['after']; // Shallow copies without reference to cloned object
				$data['before']->add( new DateInterval('P1M') ); // Extends the date 1 month into the future
			}
			else
			{
				// If 'after' date lies in the past or is NOW, return results up to 1 month into the future
				$data['before'] = new DateTime('+1 month', new DateTimeZone( Auth::user()->timezone ));
			}
		}
		else
		{
			// If a 'before' date is submitted, simply use it
			$data['before'] = new DateTime( $data['before'], new DateTimeZone( Auth::user()->timezone ) );
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


		// Generate the utilisation for every day within the requested date range
		do
		{
			$key = $current_date->format('Y-m-d');

			$result[$key] = array();

			$accommodations->each(function($el) use ($key, &$result, $current_date)
			{
				$result[$key][$el->id] = array(
					$el->bookings()
					    ->wherePivot('start', '<=', $current_date)
					    ->wherePivot('end', '>', $current_date)
					    ->where(function($query)
					    {
					    	$query->whereIn('status', Booking::$counted);
					    })
					    ->count(),
					$el->capacity
				);
			});

			$current_date->add( new DateInterval('P1D') );
		}
		while( $current_date < $data['before'] );

		return $result;
	}

	public function postAdd()
	{
		$data = Input::only('name', 'description', 'capacity', 'parent_id'); // Please NEVER use parent_id in the front-end!

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

		$accommodation = new Accommodation($data);

		if( !$accommodation->validate() )
		{
			return Response::json( array('errors' => $accommodation->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$accommodation = Auth::user()->accommodations()->save($accommodation);

		// Normalise base_prices array
		$base_prices = Helper::normaliseArray($base_prices);
		// Create new base_prices
		foreach($base_prices as &$base_price)
		{
			$base_price = new Price($base_price);

			if( !$base_price->validate() )
				return Response::json( array('errors' => $base_price->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$accommodation->basePrices()->saveMany($base_prices);

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

			$accommodation->prices()->saveMany($prices);
		}

		return Response::json( array('status' => 'OK. Accommodation created', 'id' => $accommodation->id), 201 ); // 201 Created
	}

	public function postEdit()
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

		$data = Input::only('name',	'description', 'capacity');

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

		if( !$accommodation->update($data) )
		{
			return Response::json( array('errors' => $accommodation->errors()->all()), 406 ); // 406 Not Acceptable
		}

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

			$base_prices = $accommodation->basePrices()->saveMany($base_prices);
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

			$prices = $accommodation->prices()->saveMany($prices);
		}

		return array('status' => 'OK. Accommodation updated', 'base_prices' => $base_prices, 'prices' => $prices);
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

			// If deletion worked, delete associated prices
			Price::where(Price::$owner_id_column_name, $accommodation->$id)->where(Price::$owner_type_column_name, 'Accommodation')->delete();
		}
		catch(QueryException $e)
		{
			// SoftDelete instead
			$accommodation = Auth::user()->accommodations()->findOrFail( Input::get('id') );
			$accommodation->delete();
		}

		return array('status' => 'Ok. Accommodation deleted');
	}

}
