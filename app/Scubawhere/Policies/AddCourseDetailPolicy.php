<?php

namespace Scubawhere\Policies;

use Scubawhere\Entities\Departure;
use Scubawhere\Exceptions\HTTPForbiddenException;

/**
 * Class used to perform validation when adding a course to a Bookingdetail
 *
 * @see \Scubawhere\Policies\BasePolicy
 * @package Bookings
 * @author Bryan Kneis
 */
class AddCourseDetailPolicy implements BasePolicy
{
	/** @var \TrainingSession */
	protected $training_session;

	/** @var Departure */
	protected $departure;

	/** @var \Course */
	protected $course;

	/** @var \Ticket */
	protected $ticket;

	/** @var \Training */
	protected $training;

	/** @var int ID of the booking that the Bookingdetail belongs to */
	protected $booking_id;

	/** @var \Customer */
	protected $customer;

	public function __construct($training_session, $departure, $course, $ticket, $training, $customer, $booking_id)
	{
		$this->training_session = $training_session;
		$this->departure        = $departure;
		$this->course           = $course;
	    $this->ticket           = $ticket;
		$this->training         = $training;
		$this->booking_id       = $booking_id;
		$this->customer         = $customer;
	}

    /**
	 * Validate remaining class capacity on session
	 *
	 * @throws Scubawhere\Exceptions\HTTPForbiddenException
	 */
	protected function isClassFull()
	{
		if($this->training_session)
		{
			$training_capacity = $this->training_session->getCapacityAttribute();
			if($training_capacity[0] >= $training_capacity[1]) {
				throw new HTTPForbiddenException(['The class is already fully booked!']);
			}
		}
	}

    /**
	 * Validate remaining course capacity on session
	 * 
	 * @throws Scubawhere\Exceptions\HTTPForbiddenException
	 */
	protected function isCourseFull()
	{
        if ($this->departure && $this->course && !empty($this->course->capacity)) {
            // Course's capacity is *not* infinite and must be checked
			$usedUp = $this->departure
				->bookingdetails()
				->where('course_id', $course->id)
				->count();

            if ($usedUp >= $this->course->capacity) {
                // @todo Check for extra one-time courses for this session and their capacity
				throw new HTTPForbiddenException(['The course\'s capacity on this trip is already reached!']);
            }
        }
	}

    /**
	 * Validate that the ticket still fits into the course
	 *
	 * @throws Scubawhere\Exceptions\HTTPForbiddenException
	 */
	protected function doesTicketFitCourse()
	{
        if ($this->ticket && $this->course) {
            // Check if the course still has space for the wanted ticket
            $bookedTicketsQuantity = $this->course->bookingdetails()
                ->where('ticket_id', $this->ticket->id)
                ->where('customer_id', $this->customer->id)
                ->where('booking_id', $this->booking_id)
                ->count();

            if ($bookedTicketsQuantity >= $this->course->tickets()->where('id', $this->ticket->id)->first()->pivot->quantity) {
				throw new HTTPForbiddenException(['The ticket cannot be assigned because the course\'s limit for the ticket is reached.']);
            }
        }
	}

    /**
	 * Validate that the class still fits into the course
	 *
	 * @throws Scubawhere\Exceptions\HTTPForbiddenException
	 */
	protected function doesClassFitCourse()
	{
        if ($this->training_session && $this->course) {
			$training = $this->training;
            // Check if the course still has space for the wanted class
            $bookedTrainingsQuantity = $this->course->bookingdetails()
                ->where('customer_id', $this->customer->id)
                ->where('booking_id', $this->booking_id)
                ->whereHas('training_session', function ($query) use ($training) {
                    $query->where('training_id', $this->training->id);
                })
                ->count();

            if ($bookedTrainingsQuantity >= $this->training->pivot->quantity) {
				throw new HTTPForbiddenException(['The class cannot be assigned because the course\'s limit for the class is reached.']);
            }
        }
	}

	/** Method that is called to perform the validation rules */
	public function allows()
	{
		$this->isClassFull();
		$this->isCourseFull();
		$this->doesTicketFitCourse();
		$this->doesClassFitCourse();
	}
}

