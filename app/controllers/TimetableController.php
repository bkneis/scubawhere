<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Context;

class TimetableController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->timetables()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The timetable could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Context::get()->timetables()->get();
	}

	private function isBoatAvailable($boat_id, $start_dates, $duration)
	{	
		// Check if the boat is already being used during the submitted time

		$duration_hours   = floor($duration);
		$duration_minutes = round( ($duration - $duration_hours) * 60 );
		$end_dates = array();
		foreach($start_dates as &$obj) 
		{
			$end_date = new DateTime($obj->format('Y-m-d H:i:s'));
			$end_date->add( new DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M') );
			$end_date->format('Y-m-d H:i:s');
			array_push($end_dates, $end_date);
		}

		usort($start_dates, function($a, $b) 
		{
			if($a == $b) return 0;
			return $a < $b ? -1 : 1;
		});

		usort($end_dates, function($a, $b) 
		{
			if($a == $b) return 0;
			return $a < $b ? -1 : 1;
		});

		$overlappingSessions = Context::get()->departures()
			->with('trip')
			->where('boat_id', '=', $boat_id)
			->where('start', '>=', $start_dates[0]->format('Y-m-d H:i:s'))
			->where('start', '<=', $end_dates[count($end_dates) - 1]->format('Y-m-d H:i:s'))
			->get();

		$available = true;
		$overlappingSessions->each(function($obj) use ($start_dates, $end_dates, &$available)
		{
			$duration_hours   = floor($obj->trip->duration);
			$duration_minutes = round( ($obj->trip->duration - $duration_hours) * 60 );
			$start = new DateTime($obj->start);
			$end = clone $start;
			$end->add(new DateInterval('PT' . $duration_hours . 'H' . $duration_minutes . 'M'));

			// perform checks to see if they clash	
			for($i = 0; $i < count($start_dates); $i++)
			{
				if($start <= $start_dates[$i] && $end >= $end_dates[$i]) 
				{
					$available = false;
					return false; // @todo remove false, no need
				}
			}

			for($i = 0; $i < count($start_dates); $i++)
			{
				if($start >= $start_dates[$i] && $start <= $end_dates[$i])
				{
					$available = false;
					return false;
				}
			}

			for($i = 0; $i < count($start_dates); $i++)
			{
				if($end >= $start_dates[$i] && $end <= $end_dates[$i])
				{
					$available = false;
					return false;
				}
			}
		});

		return $available;	
	}

	public function postAdd()
	{
		$data = Input::only('schedule');

		try
		{
			if( !Input::get('session_id') ) throw new ModelNotFoundException();
			$departure = Context::get()->departures()->with('trip')->where('sessions.id', Input::get('session_id') )->firstOrFail(array('sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		$schedule = $data['schedule'];
		$length   = count($schedule);

		$data['schedule'] = json_encode( $schedule );
		$data['weeks']    = $length;

		$timetable = new Timetable($data);

		if( !$timetable->validate() )
		{
			return Response::json( array('errors' => $timetable->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$timetable = Context::get()->timetables()->save($timetable);

		// Update the referenced session object's timetable ID
		$departure->timetable()->associate( $timetable );
		$departure->save();

		////////////////////////////////////////////
		// CREATE THE SESSIONS FROM THE TIMETABLE //
		////////////////////////////////////////////
		$start = new DateTime( $departure->start);
		$start_DayOfTheWeek = $start->format('N'); // Day of the week, 1 through 7. 1 for Monday, 7 for Sunday
		// $startTime = $start->format('H:I');
		$days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');

		////////////////////////////////////////////////////////////////////////////////////////////////////////
		// STEP 1: Convert schedule day names to actual dates, starting in the week of the original departure //
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		$scheduleDates = array();
		for($i = 0; $i < $length; $i++)
		{
			$date_MondayOfThisWeek = clone $start;
			$date_MondayOfThisWeek
				->sub( new DateInterval('P'.($start_DayOfTheWeek - 1).'D') ) // Set date to previous Monday
				->add( new DateInterval('P'.(7 * $i).'D') );                 // Fast forward to whatever week we are in right now

			$scheduleDates[$i] = array();
			// Loop through the days of the week
			for($d = 0; $d < 7; $d++)
			{
				if( in_array($days[$d], $schedule[$i + 1]) )
				{
					$date_DayOfTheWeek   = clone $date_MondayOfThisWeek;
					$scheduleDates[$i][] = clone $date_DayOfTheWeek->add( new DateInterval('P'.$d.'D') );
				}
			}
		}

		/////////////////////////////////////////////////////////////////////////
		// STEP 2: Create 1D array of *all* session dates that will be created //
		/////////////////////////////////////////////////////////////////////////
		$until = Input::get('until');

		if( !Input::get('until') || empty($until) )
		{
			$until = date_create('+18 months'); // Default
		}
		else
		{
			$until = date_create($until);

			if( $until === false )
			{
				return Response::json( array('errors' => array('The "until" value is not a valid date.')), 400 ); // 400 Bad Request
			}
		}

		$until->setTime(23, 59, 59); // Set time to last second of the day to make `until` date inclusive

		$sessionDates = array();
		$break = false;

		do
		{
			for($j = 0; $j < $length; $j++) // For number of weeks in the timetable
			{
				foreach( $scheduleDates[$j] as &$day ) // For each checkbox in each week
				{
					if( $day > $start ) // Only create sessions after the original sessions' startDate
					{
						if( $day > $until) // Skip, if day of session is after the 'until' date
						{
							$break = true;
							continue;
						}

						$sessionDates[] = clone $day;
					}

					$day->add( new DateInterval('P'.(7 * $length).'D') ); // Add needed weeks for the next iteration
				}
			}
		} while($break === false);

		///////////////////////////////////////////////////////////////////////////
		// STEP 3: Create required full detail array for insertion into database //
		///////////////////////////////////////////////////////////////////////////

		$now = new DateTime;
		$start_dates = array();
		foreach($sessionDates as &$date)
		{
			array_push($start_dates, $date);
			$date = array(
				'trip_id'      => $departure->trip_id,
				'start'        => $date,
				'boat_id'      => $departure->boat_id,
				'timetable_id' => $timetable->id,
				'created_at'   => $now,
				'updated_at'   => $now
			);
		}

		if(!$this->isBoatAvailable($departure->boat_id, $start_dates, $departure->trip->duration))
		{
			$departure->timetable()->dissociate($timetable);
			$timetable->delete();
			return Response::json(
				array('errors' => array('The timetable could not be created as their are future departures that are using the boat.')
			), 409);
		}

		try
		{
			DB::table('sessions')->insert( $sessionDates );
		}
		catch(Illuminate\Database\QueryException $e)
		{
			return Response::json( array('errors' => array('departure->trip_id: '.$departure->trip_id, $e->getSql(), $e->getBindings())), 500 ); // 500 Internal Server Error
		}

		//$timetable = Context::get()->timetables()->save($timetable);

		// Update the referenced session object's timetable ID
		//$departure->timetable()->associate( $timetable );
		//$departure->save();

		return Response::json( array(
			'status'   => 'OK. Timetable and trips created',
			'id'       => $timetable->id,
			/*'sessions' => $timetable->departures()
				->where('start', '>', strtotime('first day of this month'))
				->where('start', '<', strtotime('last day of next month'))
				->get()*/
		), 201 ); // 201 Created
	}

	/*
	public function postEdit()
	{
		//
	}

	public function postDeactivate()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$departure = Context::get()->departures()->where('sessions.id', Input::get('id'))->firstOrFail(array('sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
		}

		$departure->delete();

		return Response::json( array('status' => 'OK. Session deactivated'), 200 ); // 200 OK
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$departure = Context::get()->departures()->where('sessions.id', Input::get('id'))->firstOrFail(array('sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The session could not be found.')), 404 ); // 404 Not Found
		}

		if( !$departure->forceDelete() )
		{
			return Response::json( array('errors' => array('Cannot delete session. It has already been booked!')), 409 ); // 409 Conflict
		}

		return Response::json( array('status' => 'OK. Trip deleted'), 200 ); // 200 OK
	}
	*/

}
