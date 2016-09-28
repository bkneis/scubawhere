<?php

use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AddonController extends Controller {

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

            return Context::get()->addons()->withTrashed()->with('basePrices')->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The addon could not be found.')), 404); // 404 Not Found
        }
    }

    public function getAll()
    {
        return Context::get()->addons()->with('basePrices')->get();
    }

    public function getAllWithTrashed()
    {
        return Context::get()->addons()->withTrashed()->with('basePrices')->get();
    }

    public function getCompulsory()
    {
        return Context::get()->addons()->where('compulsory', true)->with('basePrices')->get();
    }

    public function postAdd()
    {
        $data = Input::only(
            'name',
            'description',
            'compulsory',
            'parent_id' // Please NEVER use parent_id in the front-end!
        );

        //Check compulsory field.....
        if (empty($data['compulsory'])) {
            $data['compulsory'] = 0;
        }

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
        // ##################### End Prices #####################

        $addon = new Addon($data);

        if (!$addon->validate()) {
            return Response::json(array('errors' => $addon->errors()->all()), 406); // 406 Not Acceptable
        }

        $addon = Context::get()->addons()->save($addon);

        // ####################### Prices #######################
        // Normalise base_prices array
        $base_prices = Helper::normaliseArray($base_prices);
        // Create new base_prices
        foreach ($base_prices as &$base_price) {
            $base_price = new Price($base_price);

            if (!$base_price->validate()) {
                return Response::json(array('errors' => $base_price->errors()->all()), 406);
            } // 406 Not Acceptable
        }

        $addon->basePrices()->saveMany($base_prices);
        // ##################### End Prices #####################

        $addon->load('basePrices');

        return Response::json(['status' => 'OK. Addon created', 'model' => $addon], 201); // 201 Created
    }

    public function postEdit()
    {
        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }
            $addon = Context::get()->addons()->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The addon could not be found.')), 404); // 404 Not Found
        }

        $data = Input::only(
            'name',
            'description',
            'compulsory'
        );

        // Check compulsory field
        if (empty($data['compulsory'])) {
            $data['compulsory'] = 0;
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
        // ####################### End Prices #######################

        if (!$addon->update($data)) {
            return Response::json(array('errors' => $addon->errors()->all()), 406); // 406 Not Acceptable
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

            $base_prices = $addon->basePrices()->saveMany($base_prices);
        }

        $addon->load('basePrices');

        return Response::json(['status' => 'OK. Addon updated.', 'model' => $addon], 200); // 200 OK
    }

    public function postDelete()
    {
		/**
		 * 1. Get the addon model with any sessions it is booked in
		 * 2. Filter through bookings that have not been cancelled
		 * 3. If there are valid (not cancelled) bookings, log all of their refrences and return a conflict
		 * 4. Check if the addon is used in any packages, if so, remove them
		 * 5. Delete the addon and its associated prices
		 */
		try 
		{
            if (!Input::get('id')) throw new ModelNotFoundException();
			$addon = Context::get()->addons()
								   ->with([
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
            return Response::json(array('errors' => array('The addon could not be found.')), 404); // 404 Not Found
        }

		$booking_ids = $addon->bookingdetails
							 ->map(function($obj) {
							     if($obj->session != null || $obj->training_session != null)
							     {
								     return $obj->booking_id;
								 }
							 })
							 ->toArray();

		$bookings = Context::get()->bookings()
								  ->whereIn('id', $booking_ids)
								  ->get(['reference', 'status']);

		$quotes = $bookings->map(function($obj) {
			if($obj->status == 'saved') return $obj->id;
		})
		->toArray();

		Context::get()->bookings()->whereIn('id', $quotes)->delete();

		$bookings = $bookings->map(function($obj){
			if($obj->status != 'cancelled' && $obj->status != 'saved') return $obj;	
		})->toArray();

		$bookings = array_filter($bookings, function($obj){ return !is_null($obj); });

		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the addon, '
												. $addon->name);
			foreach($bookings as $obj) 
			{
				$logger->append('The addon is used in the future in booking ' . '['.$obj['reference'].']');
			}

			return Response::json(
				array('errors' => 
					array('The addon could not be deleted as it is used in bookings in the future, '.
						'Please visit the error logs for more info on how to delete it.')
				), 409); // Conflict
		}


        // Check if the addon is used in any packages
		if(!$addon->getDeletableAttribute()) 
		{
			if($addon->packages()->exists()) 
			{
                $packages = $addon->packages();
                /* Loop through each package and soft delete the pivot betweeen
                 * addon and package, so that packages used in the future
                 * can use a previous state
                 * http://stackoverflow.com/questions/17350072/soft-delete-on-a-intermediate-table-for-many-to-many-relationship
                */
				foreach($packages as $obj) 
				{
                    DB::table('packageables')
                        ->where('packageable_type', 'Addon')
                        ->where('packageable_id', $addon->id)
                        ->where('package_id', $obj->id)
                        ->update(array('deleted_at' => DB::raw('NOW()')));    
                }
                $addon->save();
            }
        }

        $addon->delete();
		Price::where(Price::$owner_id_column_name, $addon->id)
			 ->where(Price::$owner_type_column_name, 'Addon')
			 ->delete();

        return array('status' => 'Ok. Addon deleted');
    }
}
