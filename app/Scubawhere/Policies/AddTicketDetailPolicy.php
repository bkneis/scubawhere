<?php

namespace Scubawhere\Policies;

class AddTicketDetailPolicy implements BasePolicy
{
	protected $ticket;

	protected $trip;

	protected $course;

	protected $package;

	public function __construct($ticket, $trip, $course, $package)
	{
		$this->ticket  = $ticket;
		$this->trip    = $trip;
		$this->course  = $course;
		$this->package = $package;
	}

	protected function isTicketValidForTrip($trip, $ticket)
	{
		$exists = $trip
			->tickets()
			->where('id', $ticket->id)
			->exists();

		if (!$exists) {
			throw new HTTPForbidden(['This ticket can not be booked for this trip.']);
		}
	}

	protected function isTicketValidForCourse($course, $ticket)
	{
		if($course) {
			$exists = $course
				->tickets()
				->where('id', $ticket->id)
				->exists();

			if (!$exists) {
				throw new HTTPForbiddenException(['This ticket can not be booked as part of this course.']);
			}
		}
	}

	protected function isTicketValidForPackage($package, $ticket)
	{
		if($package) {
			$exists = $package
				->tickets()
				->where('id', $ticket->id)
				->exists();

			if (!$exists) {
				throw new HTTPForbidden(['This ticket can not be booked as part of this package.']);
			}
		}
	}

	public function allows()
	{
		$this->isTicketValidForTrip($this->trip, $this->ticket);
		$this->isTicketValidForCourse($this->course, $this->ticket);
		$this->isTicketValidForPackage($this->package, $this->ticket);
	}
	
}

