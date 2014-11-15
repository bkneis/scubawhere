<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

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
			return Auth::user()->addons()->withTrashed()->findOrFail( Input::get('id') );
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

	public function getAllWithTrashed()
	{
		return Auth::user()->addons()->withTrashed()->get();
	}

	public function getCompulsory()
	{
		return Auth::user()->addons()->where('compulsory', true)->get();
	}

	public function postAdd()
	{
		$data = Input::only(
			'name',
			'description',
			'new_decimal_price',
			'compulsory'
		);

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
			'new_decimal_price',
			'compulsory'
		);

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

	public function postDeactivate()
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

		$addon->delete();

		return array('status' => 'OK. Addon deactivated');
	}

	public function postRestore()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$addon = Auth::user()->addons()->onlyTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The addon could not be found.')), 404 ); // 404 Not Found
		}

		$addon->restore();

		return array('status' => 'OK. Addon restored');
	}

	public function postDelete()
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

		try
		{
			$addon->forceDelete();
		}
		catch(QueryException $e)
		{
			return Response::json( array('errors' => array('The addon can not be removed because it has been booked at least once. Try deactivating it instead.')), 409); // 409 Conflict
		}

		return array('status' => 'Ok. Addon deleted');
	}
}
