<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;

class TrainingSessionController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->training_sessions()->withTrashed()->with('training')->where('training_sessions.id', Input::get('id'))->firstOrFail(array('training_sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getManifest()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->training_sessions()->with('training', 'customers')->where('training_sessions.id', Input::get('id'))->firstOrFail(array('training_sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Auth::user()->training_sessions()->withTrashed()->get();
	}

	public function getToday()
	{
		$data = array(
			'with_full' => true,
			'after'     => Helper::localTime()->setTime(0, 0)->format('Y-m-d H:i:s'),
			'before'    => Helper::localTime()->setTime(23, 59)->format('Y-m-d H:i:s'),
		);

		Request::replace($data);

		return $this->getFilter();
	}

	public function getTomorrow()
	{
		$data = array(
			'with_full' => true,
			'after'     => Helper::localTime()->add(new DateInterval('P1D'))->setTime(0, 0)->format('Y-m-d H:i:s'),
			'before'    => Helper::localTime()->add(new DateInterval('P1D'))->setTime(23, 59)->format('Y-m-d H:i:s'),
		);

		Request::replace($data);

		return $this->getFilter();
	}

	public function getFilter()
	{
		/**
		 * Valid input parameter
		 * training_id
		 * course_id
		 * after
		 * before
		 * with_full
		 */

		$data = Input::only('after', 'before', 'training_id', 'course_id');

		$data['with_full'] = Input::get('with_full', false);

		// Transform parameter strings into DateTime objects
		$data['after'] = new DateTime( $data['after'], new DateTimeZone( Auth::user()->timezone ) ); // Defaults to NOW, when parameter is NULL
		if( empty( $data['before'] ) )
		{
			if( $data['after'] > new DateTime('now', new DateTimeZone( Auth::user()->timezone )) )
			{
				// If the submitted `after` date lies in the future, move the `before` date to return 1 month of results
				$data['before'] = clone $data['after']; // Shallow copies without reference to cloned object
				$data['before']->add( new DateInterval('P1M') ); // Extends the date 1 month into the future
			}
			else
			{
				// If 'after' date lies in the past or is NOW, return results up to 1 month into the future
				$data['before'] = new DateTime('+1 month', new DateTimeZone( Auth::user()->timezone ));
			}
		}
		else
		{
			// If a 'before' date is submitted, simply use it
			$data['before'] = new DateTime( $data['before'], new DateTimeZone( Auth::user()->timezone ) );
		}

		if( $data['after'] > $data['before'] )
		{
			return Response::json( array('errors' => array('The supplied \'after\' date is later than the given \'before\' date.')), 400 ); // 400 Bad Request
		}

		// Check the integrity of the supplied parameters
		$validator = Validator::make( $data, array(
			'after'       => 'date|required_with:before',
			'before'      => 'date',
			'training_id' => 'integer|min:1',
			'course_id'   => 'integer|min:1', // Here, we are not testing for 'exists:course,id', because that would open the API for bruteforce tests of ALL existing course_ids. course_ids are private to the owning dive center and are not meant to be known by others.
			'with_full'   => 'boolean'
		) );

		if( $validator->fails() )
			return Response::json( array('errors' => $validator->messages()->all()), 400 ); // 400 Bad Request

		$options = $data;

		if( !empty( $options['training_id'] ) )
		{
			try
			{
				$training = Auth::user()->trainings()->findOrFail( $options['training_id'] );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
			}
		}
		else
			$training = false;

		if( !empty( $options['course_id'] ) )
		{
			try
			{
				$course = Auth::user()->courses()->findOrFail( $options['course_id'] );
			}
			catch(ModelNotFoundException $e)
			{
				return Response::json( array('errors' => array('The course could not be found.')), 404 ); // 404 Not Found
			}
		}
		else
			$course = false;

		/*
		  We need to navigate the relationship-tree from departure/session via training to
		  ticket and then (conditionally) to course.
		*/
		// Someone will kill me for this someday. I'm afraid it will be me. But here it goes anyway:
		$training_sessions = Auth::user()->training_sessions()->withTrashed()->with(/*'bookings', */'training')
		->whereHas('training', function($query) use ($training, $course)
		{
			$query
			->where(function($query) use ($training, $course)
			{
				// Filter by training_id
				if($training)
				{
					$query->where('id', $training->id);
				}
			})
			->where(function($query) use ($course)
			{
				// Conditional where clause (only when course_id is provided)
				if( $course )
				{
					$query->whereHas('courses', function($query) use ($course)
					{
						$query->where('id', $course->id);
					});
				}
			});
		})
		// Filter by dates
		->whereBetween('start', array(
			$options['after']->format('Y-m-d H:i:s'),
			$options['before']->format('Y-m-d H:i:s')
		))
		// ->with('training', 'training.tickets')
		->orderBy('start', 'ASC')
		// ->take(25)
		->get();

		// Filter by capacity/availability
		/*if( !$options['with_full'] )
		{
			$departures = $departures->filter(function($departure) use ($course, $options)
			{
				$capacity = $departure->getCapacityAttribute();
				if( $capacity[0] >= $capacity[1] )
				{
					// Session/Boat full/overbooked
					return false;
				}

				if( $course && !empty($course->capacity) )
				{
					$usedUp = $departure->bookingdetails()->whereHas('coursefacade', function($query) use ($course)
					{
						$query->where('course_id', $course->id);
					})->count();
					if( $usedUp >= $course->capacity )
					{
						return false;
					}
				}

				return true;
			});
		}*/

		return $training_sessions;
	}

	public function postAdd()
	{
		$data = Input::only('start');

		$isPast = Helper::isPast( $data['start'] );
		if( $isPast )
			return Response::json( array('errors' => array('Classes cannot be scheduled in the past.')), 403 ); // 403 Forbidden

		try
		{
			if( !Input::has('training_id') ) throw new ModelNotFoundException();
			$training = Auth::user()->trainings()->findOrFail( Input::get('training_id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
		}

		$training_session = new TrainingSession($data);


		if( !$training_session->validate() )
		{
			return Response::json( array('errors' => $training_session->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$training_session = $training->training_sessions()->save($training_session);

		return Response::json( array('status' => 'OK. Class scheduled', 'id' => $training_session->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$training_session = Auth::user()->training_sessions()->where('training_sessions.id', Input::get('id'))->firstOrFail(array('training_sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
		}

		$isPast = Helper::isPast( $training_session->start );
		if( !empty($training_session->deleted_at) || $isPast )
			return Response::json( array('errors' => array('Past or deactivated classes cannot be updated.')), 412 ); // 412 Precondition Failed

		if( empty($training_session->schedule_id) )
		{
			if( Input::has('start') )
				$training_session->start = Input::get('start');

			/*$capacity = $training_session->capacity;
			if($capacity[0] > 0 && Input::has('start') && Input::get('start') != $training_session->start)
			{
				return Response::json( array('errors' => array('The class cannot be moved. It has already been booked.')), 409 ); // 409 Conflict
			}*/
		}
		// If the session is part of a schedule and has been changed, check if request sent instructions on what to do
		elseif( Input::get('start') && Input::get('start') !== $training_session->start)
		{
			switch( Input::get('handle_timetable') )
			{
				case 'only_this':
					// Remove this event from the schedule and set new time
					$capacity = $training_session->getCapacityAttribute();
					if($capacity[0] > 0)
						return Response::json( array('errors' => array('The class cannot be moved. It has already been booked.')), 409 ); // 409 Conflict

					$training_session->schedule_id = null;
					$training_session->start       = Input::get('start');
				break;
				case 'following':

					// TODO Differenciate between "Yes, move everything anyway and notify customers" and "Clone booked sessions and deactivate old ones"

					// First, generate new schedule_id
					$schedule_id = TrainingSession::orderBy('schedule_id', 'DESC')->take(1)->lists('schedule_id');
					if(count($schedule_id) === 0) $schedule_id = 1;
					else $schedule_id = $schedule_id[0]++;

					$start = new DateTime( Input::get('start') );

					// Update all following session with new time and schedule_id
					// First, calculate offset between old_time and new_time
					$offset    = new DateTime($training_session->start);
					$offset    = $offset->diff($start);
					$offsetSQL = $offset->format('%h:%i'); // hours:minutes

					// Single-Query MagicTM
					DB::update(
						"UPDATE `training_sessions` SET `schedule_id`=?, `start`=DATE_ADD(`start`, INTERVAL ? HOUR_MINUTE) WHERE `start`>=? AND `schedule_id`=?",
						array( $schedule->id, $offsetSQL, $training_session->start, $training_session->schedule_id )
					);

					return array('status' => 'OK. All classes updated.');
				break;
				default:
					return Response::json( array('errors' => array('`handle_timetable` parameter is required.')), 400 ); // 400 Bad Request
				break;
			}
		}
		else
		{
			// Do nothing
			return array('status' => 'Nothing updated.');
		}

		if( !$training_session->save() )
		{
			return Response::json( array('errors' => $training_session->errors()->all()), 400 ); // 400 Bad Request
		}

		return array('status' => 'OK. Class updated.');
	}

	public function postDeactivate()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$training_session = Auth::user()->training_sessions()->where('training_sessions.id', Input::get('id'))->firstOrFail(array('training_sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
		}

		$isPast = Helper::isPast( $training_session->start );
		if( $isPast )
			return Response::json( array('errors' => array('Past classes cannot be deactivated.')), 412 ); // 412 Precondition Failed


		$training_session->delete(); // SoftDelete

		return array('status' => 'OK. Class deactivated');
	}

	public function postRestore()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$training_session = Auth::user()->training_sessions()->onlyTrashed()->where('training_sessions.id', Input::get('id'))->firstOrFail(array('training_sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
		}

		$isPast = Helper::isPast( $training_session->start );
		if( $isPast )
			return Response::json( array('errors' => array('Past classes cannot be restored.')), 412 ); // 412 Precondition Failed

		$training_session->restore();

		return array('status' => 'OK. Class restored');
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$training_session = Auth::user()->training_sessions()->withTrashed()->where('training_sessions.id', Input::get('id'))->firstOrFail(array('training_sessions.*'));
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The class could not be found.')), 404 ); // 404 Not Found
		}

		$isPast = Helper::isPast( $training_session->start );
		if( $isPast )
			return Response::json( array('errors' => array('Past classes cannot be deleted.')), 412 ); // 412 Precondition Failed

		if( $training_session->schedule_id )
		{
			switch( Input::get('handle_timetable') )
			{
				case 'only_this': break;
				case 'following':

					// Get all affected training_sessions
					$training_sessions = Auth::user()->training_sessions()
						->where('start', '>=', $training_session->start)
						->where('schedule_id', $training_session->schedule_id)
						->with('bookingdetails')
						->get();

					$training_sessions->each( function($session)
					{
						if( $session->bookingdetails()->count() === 0 )
							$session->forceDelete();
						else
							$session->delete(); // SoftDelete
					});

					return array('status' => 'OK. All classes either deleted or deactivated.');
				break;
				default:
					return Response::json( array('errors' => array('`handle_timetable` parameter is required.')), 400 ); // 400 Bad Request
				break;
			}
		}

		try
		{
			$training_session->forceDelete();
		}
		catch(QueryException $e)
		{
			return Response::json( array('errors' => array('Cannot delete class. It has already been booked!')), 409 ); // 409 Conflict
		}

		return array('status' => 'OK. Class deleted');
	}

}