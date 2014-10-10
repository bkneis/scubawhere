<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;
use PhilipBrown\Money\Currency;

class AddonController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->addons()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The addon could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->addons()->get();
	}

	public function postAdd()
	{
		$data = Input::only(
			'name',
			'description',
			'price',
			'currency',
			'compulsory'
		);

		//Get currency code
		$data['currency'] = Helper::currency( Input::get('currency') );

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

		//Check compulsory field.....
		if (empty($data['compulsory'])) {
			$data['compulsory'] = 0;
		}

		$addon = new Addon($data);

		if( !$addon->validate() )
		{
			return Response::json( array('errors' => $addon->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$addon = Auth::user()->addons()->save($addon);

		return Response::json( array('status' => 'OK. Addon created', 'id' => $addon->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$addon = Auth::user()->addons()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The addon could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only(
			'name',
			'description',
			'currency',
			'price',
			'compulsory'
		);

		//Get currency code
		$data['currency'] = Helper::currency( Input::get('currency') );

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

		//Check compulsory field.....
		if (empty($data['compulsory'])) {
			$data['compulsory'] = 0;
		}

		if( !$addon->update($data) )
		{
			return Response::json( array('errors' => $addon->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('status' => 'OK. Addon updated.'), 200 ); // 200 OK
	}
}
