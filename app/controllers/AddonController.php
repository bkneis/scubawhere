<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
// use ScubaWhere\Helper;

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
			'compulsory'
		);

		$addon = new Addons($data);

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
			'price',
			'compulsory'
		);

		if( !$addon->update($data) )
		{
			return Response::json( array('errors' => $addon->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('status' => 'OK. Addon updated.'), 200 ); // 200 OK
	}
}
