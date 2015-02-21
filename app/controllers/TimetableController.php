<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TimetableController extends Controller {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->timetables()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The timetable could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->timetables()->get();
	}

	public function postAdd()
	{
		$data = Input::only('weeks', 'schedule');

		try
		{
			if( !Input::get('session_id') ) throw new ModelNotFoundException();
			$departure = Auth::user()->departures()->where('sessions.id', Input::get('session_id') )->firstOrFail(array('sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
		}

		$schedule = $data['schedule'];
		$length = count($schedule);

		$data['schedule'] = json_encode( $schedule );
		$data['weeks'] = $length;

		$timetable = new Timetable($data);

		if( !$timetable->validate() )
		{
			return Response::json( array('errors' => $timetable->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$timetable = Auth::user()->timetables()->save($timetable);

		// Update the referenced session object's timetable ID
		$departure->timetable()->associate( $timetable );
		$departure->save();
		// $departure->update( array('timetable_id' => $timetable->id) );

		////////////////////////////////////////////
		// CREATE THE SESSIONS FROM THE TIMETABLE //
		////////////////////////////////////////////
		$start = new DateTime( $departure->start, new DateTimeZone( Auth::user()->timezone ) );
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
		foreach( $sessionDates as &$date)
		{
			$date = array(
				'trip_id'      => $departure->trip_id,
				'start'        => $date,
				'boat_id'      => $departure->boat_id,
				'timetable_id' => $timetable->id,
				'created_at'   => $now,
				'updated_at'   => $now
			);
		}

		try
		{
			DB::table('sessions')->insert( $sessionDates );
		}
		catch(Illuminate\Database\QueryException $e)
		{
			return Response::json( array('errors' => array('departure->trip_id: '.$departure->trip_id, $e->getSql(), $e->getBindings())), 500 ); // 500 Internal Server Error
		}

		return Response::json( array(
			'status'   => 'OK. Timetable and sessions created',
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
			$departure = Auth::user()->departures()->where('sessions.id', Input::get('id'))->firstOrFail(array('sessions.*'));
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
			$departure = Auth::user()->departures()->where('sessions.id', Input::get('id'))->firstOrFail(array('sessions.*'));
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
