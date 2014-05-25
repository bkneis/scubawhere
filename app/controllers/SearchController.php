<?php
// use Illuminate\Database\Eloquent\ModelNotFoundException;
// use ScubaWhere\Helper;

class SearchController extends Controller {

	public function getSessions()
	{
		$data = Input::only('after', 'before', 'trip_id');

		$options = array(
			'after' => new DateTime(),
			'before' => new DateTime('+ 1 month'),
			'trip_id' => null
		)

		// Join the default options and the submitted filter parameters
		$options = array_merge($options, $data);

		$sessions = Auth::user()->sessions()
			->where('start', '>=', $options['after'])
			->where('start', '<=', $options['before'])
			->where(function($query) use ($options)
				{
					if( !empty( $options['trip_id'] ) )
						$query->where('trip_id', $options['trip_id']);
				})
			->with('trip', 'trip.tickets')
			->take(25)->get();

		return $sessions;
	}

	public function getCustomer()
	{
		$options = Input::only('email');

		if( strlen( $options['email'] ) >= 3 )
			return Auth::user()->customers()->where('email', 'LIKE', '%'.$options['email'].'%')->take(10)->get();
		else
			return Response::json( array('errors' => array('The search term is too short. Must be at least 3 characters.')), 400 ); // 400 Bad Request
	}
}
