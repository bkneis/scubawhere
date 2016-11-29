<?php

namespace Scubawhere\Policies;

use Scubawhere\Helper;
use Scubawhere\Entities\Booking;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Exceptions\HTTPForbiddenException;

class AddSessionDetailPolicy implements BasePolicy
{
	protected $departure;

	protected $training_session;

	protected $customer;

	protected $temporary;

	public function __construct($departure, $training_session, $customer, $temporary)
	{
		$this->departure        = $departure;
		$this->training_session = $training_session;
		$this->customer		    = $customer;
		$this->temporary        = $temporary;
	}

	protected function containsAtleastOneSession($departure, $training_session, $temporary)
	{
        if (!$departure && !$training_session) {
            if (!($temporary && $temporary == 1)) {
				throw new InvalidInputException(['Either the session_id or training_session_id is required!']);
            }
        }
	}

	protected function sessionsHasNotPassed($departure, $training_session)
	{
        // Validate that the session start date has not already passed
        if ($departure && Helper::isPast($departure->start)) {
			throw new HTTPForbiddenException(['Cannot add details, because the trip has already departed!']);
        }

        // Validate that the training_session start date has not already passed
        if ($training_session && Helper::isPast($training_session->start)) {
			throw new HTTPForbiddenException(['Cannot add details, because the class has already started!']);
        }
	}

	protected function customerIsNotBusy($departure, $training_session, $customer)
	{
		if($departure) $model = $departure;
		else if($training_session) $model = $training_session;

        // Validate that the customer is not already booked for this session or training_session on another booking
		if ($departure || $training_session) {
			$start_date = new \DateTime($model->start);
			$end_date = clone $start_date;
			$duration_hours   = floor($model->duration);
			$duration_minutes = round( ($model->duration - $duration_hours) * 60 );
			$end_date->add(new \DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M'));

            $check = Booking::onlyOwners()
				->filterByCountedStatus()
				->filterContainsCustomer($customer->id)
				->filterOverlappingSessionsByDates($start_date, $end_date, $departure, $training_session)
				->exists();

			if ($check) {
                $model = $departure ? 'trip' : 'class';
				throw new HTTPForbiddenException(['The customer is already booked on another '.$model.' during this time!']);
            }
        }
	}

	public function allows()
	{
		$this->containsAtleastOneSession($this->departure, $this->training_session, $this->temporary);
		$this->sessionsHasNotPassed($this->departure, $this->training_session);
		$this->customerIsNotBusy($this->departure, $this->training_session, $this->customer);
	}

}

