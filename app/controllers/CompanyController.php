<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;

/**
 * This controller lets you retrieve and save all data concerning companies. It will only return data for the logged in company. The methods in this controller require authentication.
 *
 * For documentation, please refer to http://scubawhere.com/docs
 */
class CompanyController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
        return Auth::user();
	}

	public function postUpdate()
	{
		$data = Input::only('username', 'contact', 'description', 'email', 'name', 'address_1', 'address_2', 'city', 'county', 'postcode',/* 'country_id', 'currency_id',*/ 'business_phone', 'business_email', 'vat_number', 'registration_number', 'phone', 'website');

		if( Input::has('address_1') || Input::has('address_2') || Input::has('postcode') || Input::has('city') || Input::has('county') )
		{
			$country = Auth::user()->country;

			$address = urlencode( implode( ',', array(
				$data['address_1'],
				$data['address_2'],
				$data['postcode'],
				$data['city'],
				$data['county'],
				$country->name,
			) ) );
			$googleAPIKey = 'AIzaSyDBX2LjGDdq2QlaGq0UJ9RcEHYdodJXCWk';

			$latLng = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key='.$googleAPIKey;
			$latLng = simplexml_load_file($latLng);

			if($latLng->status === "OK")
			{
				$data['latitude']  = $latLng->results[0]->geometry->location->lat;
				$data['longitude'] = $latLng->results[0]->geometry->location->lng;

				$timezone = 'https://maps.googleapis.com/maps/api/timezone/xml?location='.$data['latitude'].','.$data['longitude'].'&timestamp='.time().'&key='.$googleAPIKey;
				$timezone = simplexml_load_file($timezone);

				if($timezone->status === "OK")
					$data['timezone'] = $timezone->time_zone_id;
				else
					return Response::json( array('errors' => arary('Sorry, we could not determine your timezone.')), 406 ); // 406 Not Acceptable
			}
			else
			{
				return Response::json( array('errors' => arary('Sorry, we could not find the specified address.')), 406 ); // 406 Not Acceptable
			}
		}

		$company = Auth::user();

		// Mass assigned insert with automatic validation
		$company->fill($data);
		if($company->updateUniques())
		{
			if( Input::has('agencies') )
				$company->agencies()->sync( Input::get('agencies') );

			return array('status' => 'OK. Company data updated', 'company' => $company);
		}
		else
		{
			return Response::json( array('errors' => $company->errors()->all()), 406 ); // 406 Not Acceptable
		}
	}

	public function getTriptypes()
	{
		return Triptype::orderBy('name')->get();
	}

	public function getLocations()
	{
		$area = Input::get('area'); // Structure: [north, west, south, east]
		if( is_array($area) )
		{
			$north = $area[0];
			$west  = $area[1];
			$south = $area[2];
			$east  = $area[3];

			// Cater for the southern hemisphere
			if( $north > $south )
			{
				$north = $area[2];
				$south = $area[0];
			}

			$locations = Location::whereBetween('latitude',  array($north, $south))
			                     ->whereBetween('longitude', array($west, $east))
			                     ->get();
		}
		else
		{
			$data = Input::only('latitude', 'longitude', 'limit');

			$validator = Validator::make( $data, array(
				'latitude'  => 'required|numeric|between:-90,90',
				'longitude' => 'required|numeric|between:-180,180',
				'limit'     => 'integer|min:1'
			) );

			if( $validator->fails() )
				return Response::json( array('errors' => $validator->messages()->all()), 406 ); // 406 Not Acceptable

			$lat   = $data['latitude'];
			$lon   = $data['longitude'];
			$limit = empty($data['limit']) ? 5 : $data['limit'];

			$timer = microtime(true);
			$locations = DB::table('locations')
			                 ->select(DB::raw('*, ((ACOS(SIN('.$lat.' * PI() / 180) * SIN(latitude * PI() / 180) + COS('.$lat.' * PI() / 180) * COS(latitude * PI() / 180) * COS(('.$lon.' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance'))
			                 ->orderBy('distance', 'asc')
			                 ->take($limit)
			                 ->get();
			$timer = round( ( microtime(true) - $timer ) * 1000, 3 );

			Log::info('The locations query took '.$timer.' ms to execute.('.$lat.', '.$lon.', '.$limit.')');

		}

		return $locations;
	}

	public function postAddLocation()
	{
		$data = Input::only('name', 'description', 'latitude', 'longitude', 'tags');

		$location = new Location($data);

		if( !$location->save() )
		{
			return Response::json( array('errors' => $location->errors()->all()), 406 ); // 406 Not Acceptable
		}

		// Automatically attach location to the company
		Auth::user()->locations()->attach( $location->id );

		return Response::json( array('status' => 'OK. Location created', 'id' => $location->id), 201 ); // 201 Created
	}
}
