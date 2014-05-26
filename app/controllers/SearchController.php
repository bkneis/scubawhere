<?php
// use Illuminate\Database\Eloquent\ModelNotFoundException;
// use ScubaWhere\Helper;

class SearchController extends Controller {

	public function getSessions()
	{
		$data = Input::only('after', 'before', 'trip_id');

		// Check the integrity of the supplied parameters
		$validator = Validator::make( $data, array(
			'after'   => 'date',
			'before'  => 'date',
			'trip_id' => 'integer|min:1' // Here, we are not testing for 'exists:trips,id', because that would open the API for bruteforce tests of ALL existing trip_ids. trip_ids are private to the owning dive center and are not meant to be known by others.
		) );

		if( $validator->fails() )
			return Response::json( array('errors' => $validator->messages()->all()), 400 ); // 400 Bad Request

		// Tranform parameter strings into DateTime objects
		$data['after']  = new DateTime( $data['after'] );
		$data['before'] = new DateTime( $data['before'] );

		$options = array(
			'after'   => new DateTime(),
			'before'  => new DateTime('+ 1 month'),
			'trip_id' => null
		)

		// Join the default options and the submitted filter parameters
		$options = array_merge($options, $data);

		return Auth::user()->sessions()
			->where('start', '>=', $options['after'])
			->where('start', '<=', $options['before'])
			->where(function($query) use ($options)
				{
					if( !empty( $options['trip_id'] ) )
						$query->where('trip_id', $options['trip_id']);
				})
			->with('trip', 'trip.tickets')
			->take(25)->get();
	}

	public function getCustomers()
	{
		$options = Input::only('email');

		if( strlen( $options['email'] ) >= 3 )
			return Auth::user()->customers()->where('email', 'LIKE', '%'.$options['email'].'%')->take(10)->get();
		else
			return Response::json( array('errors' => array('The search term is too short. Must be at least 3 characters.')), 400 ); // 400 Bad Request
	}
}
