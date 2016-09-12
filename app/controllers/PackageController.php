<?php

use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PackageController extends Controller {

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
			return Context::get()->packages()->withTrashed()->with(
				'tickets',
				'courses',
					'courses.trainings',
					'courses.tickets',
				'accommodations',
				'addons',
				'basePrices',
				'prices'
			)->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Context::get()->packages()->with(
			'tickets',
			'courses',
				'courses.trainings',
				'courses.tickets',
			'accommodations',
			'addons',
			'basePrices',
			'prices'
		)->get();
	}

	public function getAllWithTrashed()
	{
		return Context::get()->packages()->withTrashed()->with(
			'tickets',
			'courses',
				'courses.trainings',
				'courses.tickets',
			'accommodations',
			'addons',
			'basePrices',
			'prices'
		)->get();
	}

	public function getOnlyAvailable() {
		$now = Helper::localTime();
		$now = $now->format('Y-m-d');

		return Context::get()->packages()
			->where(function($query) use ($now)
			{
				$query->whereNull('available_from')->orWhere('available_from', '<=', $now);
			})
			->where(function($query) use ($now)
			{
				$query->whereNull('available_until')->orWhere('available_until', '>=', $now);
			})
			->with(
			'tickets',
			'courses',
				'courses.trainings',
				'courses.tickets',
			'accommodations',
			'addons',
			'basePrices',
			'prices'
		)->get();
	}

	public function postAdd()
	{
		$data = Input::only('name', 'description', 'parent_id', 'available_from', 'available_until', 'available_for_from', 'available_for_until'); // Please NEVER use parent_id in the front-end!

		// Validate that tickets are supplied
		$tickets = Input::get('tickets', []);
		/*if( empty($tickets) )
			return Response::json( array('errors' => array('At least one ticket is required.')), 406 ); // 406 Not Acceptable*/
		$courses = Input::get('courses', []);
		$accommodations = Input::get('accommodations', []);
		$addons = Input::get('addons', []);

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

		$package = new Package($data);

		if( !$package->validate() )
		{
			return Response::json( array('errors' => $package->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$package = Context::get()->packages()->save($package);

		// Package has been created, let's connect it with its tickets
		// TODO Validate input
		/**
		 * ticket_id => 'required|exists:tickets,id', // validate ownership
		 * quantity  => 'required|integer|min:1'
		 */
		// Input must be of type <input name="tickets[1][quantity]" value="2">
		//                                ticket_id --^   quantity value --^
		$package->tickets()->sync( $tickets );
		$package->courses()->sync( $courses );
		$package->accommodations()->sync( $accommodations );
		$package->addons()->sync( $addons );

		// Normalise base_prices array
		$base_prices = Helper::normaliseArray($base_prices);
		// Create new base_prices
		foreach($base_prices as &$base_price)
		{
			$base_price = new Price($base_price);

			if( !$base_price->validate() )
				return Response::json( array('errors' => $base_price->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$package->basePrices()->saveMany($base_prices);

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

			$package->prices()->saveMany($prices);
		}

		return Response::json( array('status' => 'Package created and connected OK', 'id' => $package->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		$data = Input::only('name', 'description', 'available_from', 'available_until', 'available_for_from', 'available_for_until');

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$package = Context::get()->packages()->withTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
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

		if( !$package->update($data) )
		{
			return Response::json( array('errors' => $package->errors()->all()), 406 ); // 406 Not Acceptable
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

			$base_prices = $package->basePrices()->saveMany($base_prices);
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

			$prices = $package->prices()->saveMany($prices);
		}

		return array('status' => 'OK. Package updated', 'base_prices' => $base_prices, 'prices' => $prices);
	}

	public function postDelete()
	{
		/**
		 * 1. Get the package model with any sessions its tickets or classes is booked in for
		 * 2. Filter through bookings that have not been cancelled
		 * 3. If there are valid (not cancelled) bookings, log all of their refrences and return a conflict
		 * 4. Delete the package and its associated prices
		 *
		 * @todo
		 */
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$package = Context::get()->packages()
									 ->with([
									 'bookingdetails.session' => function($q) {
									     $q->where('start', '>=', Helper::localtime());
									 },
							         'bookingdetails.training_session' => function($q) {
									     $q->where('start', '>=', Helper::localtime());
									 }
									 ])
									 ->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The package could not be found.')), 404 ); // Not Found
		}

		$booking_ids = $package->bookingdetails
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

		$bookings = $bookings->map(function($obj){
			if($obj->status != 'cancelled') return $obj;	
		})->toArray();

		$bookings = array_filter($bookings, function($obj){ return !is_null($obj); });

		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the package, '
												. $package->name);
			foreach($bookings as $obj) 
			{
				$logger->append('The package is used in the future in booking ' . $obj['reference']);
			}

			return Response::json(
				array('errors' => 
					array('The package could not be deleted as it is used in bookings in the future, '.
						'Please visit the error logs for more info on how to delete it.')
				), 409); // Conflict
		}

		$package->delete();
		Price::where(Price::$owner_id_column_name, $package->id)
			 ->where(Price::$owner_type_column_name, 'Package')
			 ->delete();

		return Response::json(array('status' => 'Ok. Package deleted.'), 200);
	}

}
