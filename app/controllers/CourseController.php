<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;

class CourseController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->courses()->withTrashed()->with('training', 'tickets', 'basePrices', 'prices')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The course could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->courses()->with('training', 'tickets', 'basePrices', 'prices')->get();
	}

	public function getAllWithTrashed()
	{
		return Auth::user()->courses()->withTrashed()->with('training', 'tickets', 'basePrices', 'prices')->get();
	}

	public function postAdd()
	{
		$data = Input::only('name', 'description', 'capacity', 'training_id', 'training_quantity');

		try
		{
			if( !Input::get('training_id') ) throw new ModelNotFoundException();
			Auth::user()->trainings()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
		}

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

		$course = new Course($data);

		if( !$course->validate() )
		{
			return Response::json( array('errors' => $course->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$course = Auth::user()->courses()->save($course);

		// Check if tickets are supplied
		$tickets = Input::get('tickets');
		if( empty($tickets) )
			return Response::json( array('errors' => array('At least one ticket is required.')), 406 ); // 406 Not Acceptable

		// Course has been created, let's connect it with its tickets
		// TODO Validate input
		/**
		 * ticket_id => 'required|exists:tickets,id', // validate ownership
		 * quantity  => 'required|integer|min:1'
		 */
		// Input must be of type <input name="tickets[1][quantity]" value="2">
		//                                ticket_id --^   quantity value --^
		$course->tickets()->sync( $tickets );

		// Normalise base_prices array
		$base_prices = Helper::normaliseArray($base_prices);
		// Create new base_prices
		foreach($base_prices as &$base_price)
		{
			$base_price = new Price($base_price);

			if( !$base_price->validate() )
				return Response::json( array('errors' => $base_price->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$course->basePrices()->saveMany($base_prices);

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

			$course->prices()->saveMany($prices);
		}

		return Response::json( array('status' => 'Course created and connected OK', 'id' => $course->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		$data = Input::only('name', 'description', 'capacity');

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$course = Auth::user()->courses()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The course could not be found.')), 404 ); // 404 Not Found
		}

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

		if( !$course->update($data) )
		{
			return Response::json( array('errors' => $course->errors()->all()), 406 ); // 406 Not Acceptable
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

			$base_prices = $course->basePrices()->saveMany($base_prices);
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

			$prices = $course->prices()->saveMany($prices);
		}

		return array('status' => 'OK. Course updated', 'base_prices' => $base_prices, 'prices' => $prices);
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$course = Auth::user()->courses()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The course could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			$course->forceDelete();

			// If deletion worked, delete associated prices
			Price::where(Price::$owner_id_column_name, $course->id)->where(Price::$owner_type_column_name, 'Course')->delete();
		}
		catch(QueryException $e)
		{
			// SoftDelete instead
			$course = Auth::user()->courses()->findOrFail( Input::get('id') );
			$course->delete();
		}

		return array('status' => 'Ok. Course deleted');
	}

}
