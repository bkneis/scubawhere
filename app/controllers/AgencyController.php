<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
// use ScubaWhere\Helper;

class AgencyController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Agency::with('certificates')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The agency could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Agency::with('certificates')->get();
	}
}
