<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
// use ScubaWhere\Helper;

class CountryController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Country::with('regions')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The country could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Country::with('regions')->get();
	}
}
