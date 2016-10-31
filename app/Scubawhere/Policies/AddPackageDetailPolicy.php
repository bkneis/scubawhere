<?php

namespace Scubawhere\Policies;

use Scubawhere\Exceptions\HTTPForbiddenException;

class AddPackageDetailPolicy implements BasePolicy
{
	protected $course;

	protected $package;

	protected $packagefacade;

	protected $ticket;

	protected $customer_id;

	public function __construct($course, $package, $packagefacade, $ticket, $customer_id)
	{
		$this->course = $course;
		$this->package = $package;
		$this->packagefacade = $packagefacade;
		$this->ticket = $ticket;
		$this->customer_id = $customer_id;
	}

	/**
	 * Check the course is included within the package
	 *
	 * @throws \Scubawhere\Exceptions\HTTPForbiddenException
	 */
	protected function isCourseValidForPackage()
	{
        if ($this->course && $this->package) {
			$exists = $this->package
				->courses()
				->where('id', $this->course->id)
				->exists();

            if (!$exists) {
				throw new HTTPForbiddenException(['This course can not be booked as part of this package.']);
            }
        }
	}

    /**
	 * Validate that the ticket still fits into the package
	 *
	 * @throws \Scubawhere\Exceptions\HTTPForbiddenException
	 */
	protected function doesTicketFitpackage()
	{
		dd($this->packagefacade);
        if ($this->ticket && $this->packagefacade && !$this->course) {
            // Check if the package still has space for the wanted ticket
			$bookedTicketsQuantity = $this->packagefacade
				->bookingdetails()
				->where('ticket_id', $this->ticket->id)
				->whereNull('course_id')
				->count();

            if ($bookedTicketsQuantity >= $this->package->tickets()->where('id', $this->ticket->id)->first()->pivot->quantity) {
				throw new HTTPForbiddenException(['The ticket cannot be assigned because the package\'s limit for the ticket is reached.']);
            }
        }
	}

	/**
	 * Validate that the course still fits into the package (failsafe for when client validation fails)
	 *
	 * @throws \Scubawhere\Exceptions\HTTPForbiddenException
	 */
	protected function doesCourseFitPackage()
	{
        if ($this->course && $this->packagefacade) {
            // Check if the package still has space for the wanted course
			$bookedCustomers = $this->packagefacade
				->bookingdetails()
				->where('course_id', $this->course->id)
				->lists('customer_id');

            $bookedCoursesQuantity = count($bookedCustomers);

            if ($bookedCoursesQuantity >= $this->package->courses()->where('id', $this->course->id)->first()->pivot->quantity) {
                // Before we throw the error, we need to check if the new detail belongs to one of the existing courses
                if (!in_array($this->customer_id, $bookedCustomers)) {
					throw new HTTPForbiddenException(['The course cannot be assigned because the package\'s limit for the course is reached.']);
                }
            }
        }
	}

	public function allows()
	{
		$this->isCourseValidForPackage();
		$this->doesTicketFitPackage();
		$this->doesCourseFitPackage();
	}
}

