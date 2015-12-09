<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Context;

class AddonController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->addons()->withTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The addon could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Context::get()->addons()->get();
	}

	public function getAllWithTrashed()
	{
		return Context::get()->addons()->withTrashed()->get();
	}

	public function getCompulsory()
	{
		return Context::get()->addons()->where('compulsory', true)->get();
	}

	public function postAdd()
	{
		$data = Input::only(
			'name',
			'description',
			'new_decimal_price',
			'compulsory',
			'parent_id' // Please NEVER use parent_id in the front-end!
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

		$addon = Context::get()->addons()->save($addon);

		return Response::json( ['status' => 'OK. Addon created', 'model' => $addon], 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$addon = Context::get()->addons()->findOrFail( Input::get('id') );
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

		// Check compulsory field
		if (empty($data['compulsory'])) {
			$data['compulsory'] = 0;
		}

		if($addon->has_bookings && $data['new_decimal_price'] && $data['new_decimal_price'] != $addon->decimal_price)
		{
			// Create new addon and deactivate the old one

			$data['parent_id'] = $addon->id;

			// Replace all unavailable input data with data from the old addon object
			if( empty($data['name']) )              $data['name']              = $addon->name;
			if( empty($data['description']) )       $data['description']       = $addon->description;
			if( empty($data['new_decimal_price']) ) $data['new_decimal_price'] = $addon->decimal_price;
			if( empty($data['compulsory']) )        $data['compulsory']        = $addon->compulsory;

			// SoftDelete the old addon
			$addon->delete();

			// Dispatch add-addon route with all data
			$originalInput = Request::input();
			$data['_token'] = Input::get('_token');
			$request = Request::create('api/addon/add', 'POST', $data);
			Request::replace($request->input());
			return Route::dispatch($request);
			Request::replace($originalInput);
		}
		else {
			// Just update the addon

			if( !$addon->update($data) )
			{
				return Response::json( array('errors' => $addon->errors()->all()), 406 ); // 406 Not Acceptable
			}

			return Response::json( ['status' => 'OK. Addon updated.', 'model' => $addon], 200 ); // 200 OK
		}
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$addon = Context::get()->addons()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The addon could not be found.')), 404 ); // 404 Not Found
		}

		if($addon->packages()->exists())
			return Response::json( array('errors' => array('The addon can not be removed currently because it is used in packages.')), 409); // 409 Conflict

		try
		{
			$addon->forceDelete();
		}
		catch(QueryException $e)
		{

			$addon = Context::get()->addons()->find( Input::get('id') );
			$addon->delete();
		}

		return array('status' => 'Ok. Addon deleted');
	}
}
