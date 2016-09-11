<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;

class AccommodationController extends Controller {

	public function __construct(LogService $log_service)
	{
		$this->log_service = $log_service;
	}

    public function getIndex()
    {
        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }

            return Context::get()->accommodations()->withTrashed()->with('basePrices', 'prices')->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The accommodation could not be found.')), 404); // 404 Not Found
        }
    }

    public function getAll()
    {
        //throw new ModelNotFoundException();
        return Context::get()->accommodations()->with('basePrices', 'prices')->get();
    }

    public function getAllWithTrashed()
    {
        return Context::get()->accommodations()->withTrashed()->with('basePrices', 'prices')->get();
    }

    public function getFilter()
    {
        /*
         * Valid input parameter
         * accommodation_id
         * after
         * before
         */

        $data = Input::only('after', 'before', 'accommodation_id');

        // Transform parameter strings into DateTime objects
        $data['after'] = new DateTime($data['after'], new DateTimeZone(Context::get()->timezone)); // Defaults to NOW, when parameter is NULL
        if (empty($data['before'])) {
            if ($data['after'] > new DateTime('now', new DateTimeZone(Context::get()->timezone))) {
                // If the submitted `after` date lies in the future, move the `before` date to return 1 month of results
                $data['before'] = clone $data['after']; // Shallow copies without reference to cloned object
                $data['before']->add(new DateInterval('P1M')); // Extends the date 1 month into the future
            } else {
                // If 'after' date lies in the past or is NOW, return results up to 1 month into the future
                $data['before'] = new DateTime('+1 month', new DateTimeZone(Context::get()->timezone));
            }
        } else {
            // If a 'before' date is submitted, simply use it
            $data['before'] = new DateTime($data['before'], new DateTimeZone(Context::get()->timezone));
        }

        if ($data['after'] > $data['before']) {
            return Response::json(array('errors' => array('The supplied \'after\' date is later than the given \'before\' date.')), 400); // 400 Bad Request
        }

        // Check the integrity of the supplied parameters
        $validator = Validator::make($data, array(
            'after' => 'date|required_with:before',
            'before' => 'date',
            'accommodation_id' => 'integer|min:1',
        ));

        if ($validator->fails()) {
            return Response::json(array('errors' => $validator->messages()->all()), 400);
        } // 400 Bad Request

        if (!empty($data['accommodation_id'])) {
            try {
                $accommodation = Context::get()->accommodations()->findOrFail($data['accommodation_id']);
            } catch (ModelNotFoundException $e) {
                return Response::json(array('errors' => array('The accommodation could not be found.')), 404); // 404 Not Found
            }
        } else {
            $accommodation = false;
        }

        $current_date = clone $data['after'];
        $result = array();

        $accommodations = Context::get()->accommodations()->where(function ($query) use ($accommodation) {
            if ($accommodation) {
                $query->where('id', $accommodation->id);
            }
        })
        ->get();

        // Generate the utilisation for every day within the requested date range
        do {
            $key = $current_date->format('Y-m-d');

            $result[$key] = array();

            $accommodations->each(function ($el) use ($key, &$result, $current_date) {
                $result[$key][$el->id] = array(
                    $el->bookings()
                        ->wherePivot('start', '<=', $current_date)
                        ->wherePivot('end', '>', $current_date)
                        ->where(function ($query) {
                            $query->whereIn('status', Booking::$counted);
                        })
                        ->count(),
                    $el->capacity,
                );
            });

            $current_date->add(new DateInterval('P1D'));
        } while ($current_date < $data['before']);

        return $result;
    }

    public function postAdd()
    {
        $data = Input::only('name', 'description', 'capacity', 'parent_id'); // Please NEVER use parent_id in the front-end!

        // ####################### Prices #######################
        $base_prices = Input::get('base_prices');
        if (!is_array($base_prices)) {
            return Response::json(array('errors' => array('The "base_prices" value must be of type array!')), 400);
        } // 400 Bad Request

        // Filter out empty and existing prices
        $base_prices = Helper::cleanPriceArray($base_prices);

        // Check if 'prices' input array is now empty
        if (empty($base_prices)) {
            return Response::json(array('errors' => array('You must submit at least one base price!')), 400);
        } // 400 Bad Request

        if (Input::has('prices')) {
            $prices = Input::get('prices');
            if (!is_array($prices)) {
                return Response::json(array('errors' => array('The "prices" value must be of type array!')), 400);
            } // 400 Bad Request

            // Filter out empty and existing prices
            $prices = Helper::cleanPriceArray($prices);

            // Check if 'prices' input array is now empty
            if (empty($prices)) {
                $prices = false;
            }
        } else {
            $prices = false;
        }
        // ##################### End Prices #####################

        $accommodation = new Accommodation($data);

        if (!$accommodation->validate()) {
            return Response::json(array('errors' => $accommodation->errors()->all()), 406); // 406 Not Acceptable
        }

        $accommodation = Context::get()->accommodations()->save($accommodation);

        // Normalise base_prices array
        $base_prices = Helper::normaliseArray($base_prices);
        // Create new base_prices
        foreach ($base_prices as &$base_price) {
            $base_price = new Price($base_price);

            if (!$base_price->validate()) {
                return Response::json(array('errors' => $base_price->errors()->all()), 406);
            } // 406 Not Acceptable
        }

        $accommodation->basePrices()->saveMany($base_prices);

        if ($prices) {
            // Normalise prices array
            $prices = Helper::normaliseArray($prices);
            // Create new prices
            foreach ($prices as &$price) {
                $price = new Price($price);

                if (!$price->validate()) {
                    return Response::json(array('errors' => $price->errors()->all()), 406);
                } // 406 Not Acceptable
            }

            $accommodation->prices()->saveMany($prices);
        }

        return Response::json(array('status' => 'OK. Accommodation created', 'model' => $accommodation->load('basePrices', 'prices')), 201); // 201 Created
    }

    public function postEdit()
    {
        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }
            $accommodation = Context::get()->accommodations()->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The accommodation could not be found.')), 404); // 404 Not Found
        }

        $data = Input::only('name',    'description', 'capacity');

        // ####################### Prices #######################
        if (Input::has('base_prices')) {
            $base_prices = Input::get('base_prices');
            if (!is_array($base_prices)) {
                return Response::json(array('errors' => array('The "base_prices" value must be of type array!')), 400);
            } // 400 Bad Request

            // Filter out empty and existing prices
            $base_prices = Helper::cleanPriceArray($base_prices);

            // Check if 'base_prices' input array is now empty
            if (empty($base_prices)) {
                $base_prices = false;
            }
        } else {
            $base_prices = false;
        }

        if (Input::has('prices')) {
            $prices = Input::get('prices');
            if (!is_array($prices)) {
                return Response::json(array('errors' => array('The "prices" value must be of type array!')), 400);
            } // 400 Bad Request

            // Filter out empty and existing prices
            $prices = Helper::cleanPriceArray($prices);

            // Check if 'prices' input array is now empty
            if (empty($prices)) {
                $prices = false;
            }
        } else {
            $prices = false;
        }
        // ##################### End Prices #####################

        if (!$accommodation->update($data)) {
            return Response::json(array('errors' => $accommodation->errors()->all()), 406); // 406 Not Acceptable
        }

        if ($base_prices) {
            // Normalise base_prices array
            $base_prices = Helper::normaliseArray($base_prices);
            // Create new base_prices
            foreach ($base_prices as &$base_price) {
                $base_price = new Price($base_price);

                if (!$base_price->validate()) {
                    return Response::json(array('errors' => $base_price->errors()->all()), 406);
                } // 406 Not Acceptable
            }

            $base_prices = $accommodation->basePrices()->saveMany($base_prices);
        }

        if ($prices) {
            // Normalise prices array
            $prices = Helper::normaliseArray($prices);
            // Create new prices
            foreach ($prices as &$price) {
                $price = new Price($price);

                if (!$price->validate()) {
                    return Response::json(array('errors' => $price->errors()->all()), 406);
                } // 406 Not Acceptable
            }

            $prices = $accommodation->prices()->saveMany($prices);
        }

        return array('status' => 'OK. Accommodation updated', 'model' => $accommodation->load('basePrices', 'prices'));
    }

    public function postDelete()
    {
		/**
		 * 1. Get the accommodation model with any sessions it is booked in for
		 * 2. Filter through bookings that have not been cancelled
		 * 3. If there are valid (not cancelled) bookings, log all of their refrences and return a conflict
		 * 4. Check if the accommodation is used in any packages, if so, remove them
		 * 5. Delete the accommodation
		 *
		 * @todo
		 * - Soft delete the prices
		 */
		try 
		{
            if (!Input::get('id')) throw new ModelNotFoundException();
			$accommodation = Context::get()->accommodations()
										   ->with(['bookings' => function($q) {
											   $q->where('accommodation_booking.start', '>=', Helper::localtime());
										   }])
										   ->findOrFail(Input::get('id'));
		} 
		catch (ModelNotFoundException $e) 
		{
            return Response::json(array('errors' => array('The accommodation could not be found.')), 404); // 404 Not Found
        }

		$bookings = $accommodation->bookings
								  ->map(function($obj) {
								       if($obj->status != 'cancelled') return $obj;
								   })
								   ->toArray();

		$bookings = array_filter($bookings, function($obj){ return !is_null($obj); });

		//return $bookings;

		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the accommodation, ' 
												 . $accommodation->name);
			foreach($bookings as $obj) 
			{
				$logger->append('The accommodation is used in the booking ' . $obj['reference']);
			}
			return Response::json(
						array('errors' => 
							array('The accommodation could not be deleted as it is booked in the future, 
								   please visit the error logs tab to find how to delete it.')
						), 409); // Conflict
		}

        // Check if the user wants to delete accommodation even when in packages
		if(!$accommodation->getDeletableAttribute()) 
		{
			if ($accommodation->packages()->exists()) 
			{
                // Loop through each package and remove its pivot from packages
                $packages = $accommodation->packages();
				foreach($packages as $obj) 
				{
                    //$accommodation->packages()->detach($obj->id);
                    DB::table('packageables')
                        ->where('packageable_type', 'Accommodation')
                        ->where('packageable_id', $accommodation->id)
                        ->where('package_id', $obj->id)
                        ->update(array('deleted_at' => DB::raw('NOW()')));    
                }
                $accommodation->save();
            }
        }

        $accommodation->delete();

        // If deletion worked, delete associated prices
        //Price::where(Price::$owner_id_column_name, $accommodation->id)->where(Price::$owner_type_column_name, 'Accommodation')->delete();

        return array('status' => 'Ok. Accommodation deleted');
    }
}
