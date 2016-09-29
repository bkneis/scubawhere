<?php

use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CourseController extends Controller {

	protected $log_service;

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

            return Context::get()->courses()->withTrashed()->with('trainings', 'tickets', 'basePrices', 'prices')->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The course could not be found.')), 404); // 404 Not Found
        }
    }

    public function getAll()
    {
        return Context::get()->courses()->with('trainings', 'tickets', 'basePrices', 'prices')->get();
    }

    public function getAllWithTrashed()
    {
        return Context::get()->courses()->withTrashed()->with('trainings', 'tickets', 'basePrices', 'prices')->get();
    }

    public function postAdd()
    {
        $data = Input::only('name', 'description', 'capacity', 'certificate_id');

        if (empty($data['certificate_id'])) {
            $data['certificate_id'] = null;
        }

        // Check if tickets are supplied
        $tickets = Input::get('tickets', []);
        $trainings = Input::get('trainings', []);

        if (empty($trainings) && empty($tickets)) {
            return Response::json(array('errors' => array('Either a class or a ticket is required.')), 406);
        } // 406 Not Acceptable */

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

        $course = new Course($data);

        if (!$course->validate()) {
            return Response::json(array('errors' => $course->errors()->all()), 406); // 406 Not Acceptable
        }

        $course = Context::get()->courses()->save($course);

        // Course has been created, let's connect it with its tickets
        // TODO Validate input
        /*
         * ticket_id => 'required|exists:tickets,id', // validate ownership
         * quantity  => 'required|integer|min:1'
         */
        // Input must be of type <input name="tickets[1][quantity]" value="2">
        //                                ticket_id --^   quantity value --^
        $course->tickets()->sync($tickets);

        $course->trainings()->sync($trainings);

        // Normalise base_prices array
        $base_prices = Helper::normaliseArray($base_prices);
        // Create new base_prices
        foreach ($base_prices as &$base_price) {
            $base_price = new Price($base_price);

            if (!$base_price->validate()) {
                return Response::json(array('errors' => $base_price->errors()->all()), 406);
            } // 406 Not Acceptable
        }

        $course->basePrices()->saveMany($base_prices);

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

            $course->prices()->saveMany($prices);
        }

        return Response::json(array('status' => 'Course created and connected OK', 'id' => $course->id), 201); // 201 Created
    }

    public function postEdit()
    {
        $data = Input::only('name', 'description', 'capacity', 'certificate_id');

        if (empty($data['certificate_id'])) {
            $data['certificate_id'] = null;
        }

        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }
            $course = Context::get()->courses()->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The course could not be found.')), 404); // 404 Not Found
        }

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

        if (!$course->update($data)) {
            return Response::json(array('errors' => $course->errors()->all()), 406); // 406 Not Acceptable
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

            $base_prices = $course->basePrices()->saveMany($base_prices);
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

            $prices = $course->prices()->saveMany($prices);
        }

        return array('status' => 'OK. Course updated', 'base_prices' => $base_prices, 'prices' => $prices);
    }

    public function postDelete()
    {
        try 
        {
            if (!Input::get('id')) throw new ModelNotFoundException();
            $course = Context::get()->courses()
									->with(['packages',
									'bookingdetails.session' => function($q) {
										$q->where('start', '>=', Helper::localtime());
									},
									'bookingdetails.training_session' => function($q) {
										$q->where('start', '>=', Helper::localtime());
									}])
                                    ->findOrFail(Input::get('id'));
        } 
        catch (ModelNotFoundException $e) 
        {
            return Response::json(
                            array('errors' => 
                                array('The course could not be found.')
                            ), 404); // 404 Not Found
        }

		$booking_ids = $course->bookingdetails
							  ->map(function($obj) {
							      if($obj->session != null || $obj->training_session != null)
								  {
								      return $obj->booking_id;
								  }
							  })
							  ->toArray();

		$bookings = Context::get()->bookings()
								  ->whereIn('id', $booking_ids)
								  ->get(['id', 'reference', 'status']);

		$quotes = $bookings->map(function($obj) {
			if($obj->status == 'saved') return $obj->id;
		})
		->toArray();

		Context::get()->bookings()->whereIn('id', $quotes)->delete();

		$bookings = $bookings->filter(function($obj){
			if($obj->status != 'cancelled' && $obj->status != 'saved') return $obj;	
		})->toArray();

		//$bookings = array_filter($bookings, function($obj){ return !is_null($obj); });

		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the course, '
												. $course->name);
			foreach($bookings as $obj) 
			{
				$logger->append('The course is used in the future in booking ' . '['.$obj['reference'].']');
			}

			return Response::json(
				array('errors' => 
					array('The course could not be deleted as it is used in bookings in the future, '.
						'Please visit the error logs for more info on how to delete it.')
				), 409); // Conflict
		}

        if(!$course->getDeleteableAttribute())
        {
            foreach($course->packages as $obj) 
            {
                DB::table('packageables')
                    ->where('packageable_type', 'Course')
                    ->where('packageable_id', $course->id)
                    ->where('package_id', $obj->id)
                    ->update(array('deleted_at' => DB::raw('NOW()')));    
            } 
        }

        $course->delete();

		Price::where(Price::$owner_id_column_name, $course->id)
			->where(Price::$owner_type_column_name, 'Course')
			->delete();

        return array('status' => 'Ok. Course deleted');
    }

    /*
     *
    public function postDelete()
    {
        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }
            $course = Context::get()->courses()->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The course could not be found.')), 404); // 404 Not Found
        }

        if ($course->packages()->exists()) {
            return Response::json(array('errors' => array('The course can not be removed currently because it is used in packages.')), 409);
        } // 409 Conflict

        try {
            $course->forceDelete();

            // If deletion worked, delete associated prices
            Price::where(Price::$owner_id_column_name, $course->id)->where(Price::$owner_type_column_name, 'Course')->delete();
        } catch (QueryException $e) {
            // SoftDelete instead
            $course = Context::get()->courses()->findOrFail(Input::get('id'));
            $course->delete();
        }

        return array('status' => 'Ok. Course deleted');
    }
     * 
     */
}
