<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;

class TripController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->trips()->with(
				array(
					'location',
					'locations',
					'triptypes',
					'tickets' => function($query)
					{
						$query->where('active', '=', 1);
					}
				)
			)->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->trips()->with(
			array(
				'location',
				'locations',
				'triptypes',
				'tickets' => function($query)
				{
					$query->where('active', '=', 1);
				}
			)
		)->get();
	}

	/*
	public function postAdd()
	{
		//
	}

	public function postEdit()
	{
		//
	}

	public function postDelete()
	{
		//
	}
	*/

}
