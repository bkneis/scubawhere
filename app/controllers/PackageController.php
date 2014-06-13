<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;
use PhilipBrown\Money\Currency;

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
			return Auth::user()->packages()->with('tickets')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->packages()->with('tickets')->get();
	}

	public function postAdd()
	{
		$data = Input::only('name', 'description', 'price', 'currency', 'capacity');

		// Validate that tickets are supplied
		$tickets = Input::get('tickets');
		if( empty($tickets) )
			return Response::json( array('errors' => array('At least one ticket is required.')), 406 ); // 406 Not Acceptable

		// Convert price to subunit
		try
		{
			$currency = new Currency( $data['currency'] );
		}
		catch(InvalidCurrencyException $e)
		{
			return Response::json( array( 'errors' => array('The currency is not a valid currency code!')), 400 ); // 400 Bad Request
		}
		$data['price'] = (int) round( $data['price'] * $currency->getSubunitToUnit() );

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

		return Response::json( array('status' => 'Package created and connected OK', 'id' => $package->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		$data = Input::only('name', 'description', 'price', 'currency', 'capacity');

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$package = Auth::user()->packages()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The package could not be found.')), 404 ); // 404 Not Found
		}

		// Validate that tickets are supplied
		$tickets = Input::get('tickets');
		if( empty($tickets) )
			return Response::json( array('errors' => array('At least one ticket is required.')), 406 ); // 406 Not Acceptable

		// Convert price to subunit
		try
		{
			$currency = new Currency( $data['currency'] );
		}
		catch(InvalidCurrencyException $e)
		{
			return Response::json( array( 'errors' => array('The currency is not a valid currency code!')), 400 ); // 400 Bad Request
		}
		$data['price'] = (int) round( $data['price'] * $currency->getSubunitToUnit() );

		if( $data['capacity'] == 0)
			$data['capacity'] = null;

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
		$package->tickets()->sync( Input::get('tickets') );

		return Response::json( array('status' => 'Package created and connected OK', 'id' => $package->id), 201 ); // 201 Created
	}

	/*

	public function postDeactivate()
	{
		//
	}

	public function postDelete()
	{
		//
	}
	*/

}
