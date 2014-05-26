<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
// use ScubaWhere\Helper;

class CustomerController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->customers()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The customer could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->customers()->get();
	}

	public function postAdd()
	{
		$data = Input::only(
			'email',
			'firstname',
			'lastname',
			'birthday',
			'gender',
			'address_1',
			'address_2',
			'city',
			'county',
			'postcode',
			'region_id',
			'country_id',
			'phone',
			'certificate_id',
			'last_dive'
		);

		$customer = new Customer($data);

		if( !$customer->validate() )
		{
			return Response::json( array('errors' => $customer->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$customer = Auth::user()->customers()->save($customer);

		return Response::json( array('status' => 'OK. Customer created', 'id' => $customer->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$customer = Auth::user()->customers()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The customer could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only(
			'email',
			'firstname',
			'lastname',
			'birthday',
			'gender',
			'address_1',
			'address_2',
			'city',
			'county',
			'postcode',
			'region_id',
			'country_id',
			'phone',
			'certificate_id',
			'last_dive'
		);

		if( !$customer->update($data) )
		{
			return Response::json( array('errors' => $customer->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('status' => 'OK. Customer updated.'), 200 ); // 200 OK
	}
}
