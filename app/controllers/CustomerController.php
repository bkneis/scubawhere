<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
			'country_id',
			'phone',
			'last_dive'
		);

		$customer = new Customer($data);

		if( !$customer->validate() )
		{
			return Response::json( array('errors' => $customer->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// Validate that the customer does not already exist within the logged-in company
		if( Auth::user()->customers()->where('email', Input::get('email'))->count() > 0 )
			return Response::json( array('errors' => 'Cannot create new customer! The email already exists.'), 409 ); // 409 Conflict

		$customer = Auth::user()->customers()->save($customer);

		$certificates = Input::get('certificates');
		if( $certificates && !empty($certificates) && is_array($certificates) && is_numeric( $certificates[0] ) )
		{
			try
			{
				$trip->certificates()->sync($certificates);
			}
			catch(Exception $e)
			{
				return Response::json( array('errors' => array('Could not assign certificates to customer, \'certificates\' array is propably erroneous.')), 400 ); // 400 Bad Request
			}
		}

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
			// 'region_id',
			'country_id',
			'phone',
			'last_dive'
		);

		if( !$customer->update($data) )
		{
			return Response::json( array('errors' => $customer->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$certificates = Input::get('certificates');
		if( $certificates && !empty($certificates) && is_array($certificates) && is_numeric( $certificates[0] ) )
		{
			try
			{
				$trip->certificates()->sync($certificates);
			}
			catch(Exception $e)
			{
				return Response::json( array('errors' => array('Could not assign certificates to customer, \'certificates\' array is propably erroneous.')), 400 ); // 400 Bad Request
			}
		}

		return Response::json( array('status' => 'OK. Customer updated.'), 200 ); // 200 OK
	}
}
