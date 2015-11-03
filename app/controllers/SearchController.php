<?php
// use Illuminate\Database\Eloquent\ModelNotFoundException;
// use ScubaWhere\Helper;
use ScubaWhere\Context;

class SearchController extends Controller {

	public function getCustomers()
	{
		$options = Input::only('email');

		if( strlen( $options['email'] ) < 3 )
			return Response::json( array('errors' => array('The search term is too short. Must be at least 3 characters.')), 400 ); // 400 Bad Request

		return Context::get()->customers()->where('email', 'LIKE', '%'.$options['email'].'%')->take(10)->get();
	}
}
