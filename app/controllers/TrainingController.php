<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class TrainingController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->trainings()->withTrashed()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->trainings()->get();
	}

	public function getAllWithTrashed()
	{
		return Auth::user()->trainings()->withTrashed()->get();
	}

	public function postAdd()
	{
		$data = Input::only('name', 'description', 'duration');

		$training = new Training($data);

		if( !$training->validate() )
		{
			// The validator failed
			return Response::json( array('errors' => $training->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// Input has been validated, save the model
		$training = Auth::user()->trainings()->save($training);

		// When no problems occur, we return a success response
		return Response::json( array('status' => 'OK. Class created', 'id' => $training->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		$data = Input::only('name', 'description', 'duration');

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$training = Auth::user()->trainings()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
		}

		if( !$training->update($data) )
		{
			// When validation fails
			return Response::json( array('errors' => $training->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// When no problems occur, we return a success response
		return Response::json( array('status' => 'OK. Trip updated'), 200 ); // 200 OK
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$training = Auth::user()->trainings()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			$training->forceDelete();
		}
		catch(QueryException $e)
		{
			return Response::json( array('errors' => array('The class can not be removed because it is used in courses or is scheduled.'/*.' Try deactivating it instead.'*/)), 409); // 409 Conflict
		}

		return array('status' => 'Ok. Class deleted');
	}

}
