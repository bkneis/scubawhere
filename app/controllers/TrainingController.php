<?php

use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Helper;

class TrainingController extends Controller
{

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

            return Context::get()->trainings()->withTrashed()->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The class could not be found.')), 404); // 404 Not Found
        }
    }

    public function getAll()
    {
        return Context::get()->trainings()->get();
    }

    public function getAllWithTrashed()
    {
        return Context::get()->trainings()->withTrashed()->get();
    }

    public function postAdd()
    {
        $data = Input::only('name', 'description', 'duration');

        $training = new Training($data);

        if (!$training->validate()) {
            // The validator failed
            return Response::json(array('errors' => $training->errors()->all()), 406); // 406 Not Acceptable
        }

        // Input has been validated, save the model
        $training = Context::get()->trainings()->save($training);

        // When no problems occur, we return a success response
        return Response::json(array('status' => 'OK. Class created', 'id' => $training->id), 201); // 201 Created
    }

    public function postEdit()
    {
        $data = Input::only('name', 'description', 'duration');

        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }
            $training = Context::get()->trainings()->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The class could not be found.')), 404); // 404 Not Found
        }

        if (!$training->update($data)) {
            // When validation fails
            return Response::json(array('errors' => $training->errors()->all()), 406); // 406 Not Acceptable
        }

        // When no problems occur, we return a success response
        return Response::json(array('status' => 'OK. Trip updated'), 200); // 200 OK
    }

    public function getTest()
    {
        return Context::get()->trainings()->with('test')->findOrfail(Input::get('id'));
    }

    public function postDelete()
    {
		/**
		 * 1. Get the training model with any sessions it is booked for in the future, since classes can't
		 * be booked. We need to retrieve it through its courses
		 * 2. Go through the courses and retrieve their booking id
		 * 3. Get the reference and status for all implicated bookings
		 * 4. Filter through bookings that have not been cancelled
		 * (5). If there are valid (not cancelled) bookings, log all of their refrences and return a conflict
		 * 6. Check if the tickets belongs to any courses
		 * 7. Check if any of the courses rely on the class (the class is the only session on the course)
		 * (8). If any are a depedancy for the courses, log the courses and return a conflict
		 * 9. Delete the class and return OK.
		 *
		 * @todo
		 * - Reduce the number of db transactions by joining the booking info with the booking details
		 * - Find a way to not retieve the appended attributes of the booking model
		 */

		try 
		{
			if (!Input::get('id')) throw new ModelNotFoundException();
			$training = Context::get()->trainings()
									  ->with([
									  'courses.bookingdetails.training_session' => function($q) {
									      $q->where('start', '>=', Helper::localtime());
								      }])
									  ->findOrFail(Input::get('id'));
		} 
		catch (ModelNotFoundException $e) 
		{
            return Response::json(array('errors' => array('The class could not be found.')), 404); // 404 Not Found
        }

		$booking_ids = array();

		foreach($training->courses as $obj) 
		{
			$ids = $obj->bookingdetails
					   ->map(function($obj) {
					       if($obj->training_session)
						   {
						       return $obj->booking_id;
						   }
					   })
					   ->toArray();	  
			$booking_ids = array_merge($booking_ids, $ids);
			$booking_ids = array_filter($booking_ids, function($obj) { return !is_null($obj); });
		}

		$bookings = Context::get()->bookings()
									  ->whereIn('id', $booking_ids)
								      ->get(['reference', 'status']);

		$bookings = $bookings->map(function($obj){
			if($obj->status != 'cancelled') return $obj;	
		})->toArray();

		$bookings = array_filter($bookings, function($obj){ return !is_null($obj); });

		if($bookings)
		{
			$logger = $this->log_service->create('Attempting to delete the class, '
												. $training->name);
			foreach($bookings as $obj) 
			{
				$logger->append('The class is used in the future in booking ' . $obj['reference']);
			}

			return Response::json(
						array('errors' => 
							array('The class could not be deleted as it is used in bookings in the future, '.
								'Please visit the error logs for more info on how to delete it.')
						), 409); // Conflict
		}

		if(!$training->getDeleteableAttribute()) 
		{
            $problem_courses = array();
			foreach($training->courses as $obj) 
			{
				if($obj->tickets()->exists()) 
				{
                    DB::table('course_training')
                        ->where('course_id', $obj->id)
                        ->where('training_id', $training->id)
                        ->update(array('deleted_at' => DB::raw('NOW()')));    
                }
				else 
				{
                    array_push($problem_courses, $obj);
                }
            }
			if(sizeof($problem_courses) > 0)
			{
                $logger = $this->log_service->create('Attempting to delete the class ' . $training->name);

				foreach($problem_courses as $prob) 
				{
					$logger->append('The class can not be deleted becuase it belongs to the course ' . $obj->name . ', please assign a diffrent class or ticket to it');
				}
				return Response::json(
					array('errors' => 
						array('The class could not be deleted as it is assigned to a course, '.
							'please visit the error logs to view how to correct it before deleting it')
					), 409);
			}
        }

		$training->delete();
		return Response::json(array('status' => 'Ok. Class deleted'), 200);
    }
}
