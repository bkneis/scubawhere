<?php

namespace Scubawhere\Services;

use Scubawhere\Helper;
use Scubawhere\Policies\AddCourseDetailPolicy;
use Scubawhere\Policies\AddTicketDetailPolicy;
use Scubawhere\Policies\AddPackageDetailPolicy;
use Scubawhere\Policies\AddSessionDetailPolicy;
use Scubawhere\Policies\AssignBoatDetailPolicy;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Repositories\TicketRepoInterface;
use Scubawhere\Repositories\CourseRepoInterface;
use Scubawhere\Repositories\BookingRepoInterface;
use Scubawhere\Repositories\PackageRepoInterface;
use Scubawhere\Repositories\CustomerRepoInterface;
use Scubawhere\Repositories\DepartureRepoInterface;
use Scubawhere\Repositories\BookingdetailRepoInterface;
use Scubawhere\Repositories\TrainingSessionRepoInterface;

class BookingdetailService
{
	protected $booking_repo;

	protected $customer_repo;

	protected $ticket_repo;

	protected $departure_repo;

	protected $training_session_repo;

	protected $package_repo;

	protected $package_service;

	public function __construct(BookingRepoInterface         $booking_repo,
								BookingdetailRepoInterface   $bookingdetail_repo,
								CustomerRepoInterface        $customer_repo,
								TicketRepoInterface          $ticket_repo,
								DepartureRepoInterface       $departure_repo,
								TrainingSessionRepoInterface $training_session_repo,
								PackageRepoInterface         $package_repo,
								CourseRepoInterface          $course_repo,
								PackageService               $package_service)
	{
		$this->booking_repo          = $booking_repo;
		$this->bookingdetail_repo    = $bookingdetail_repo;
		$this->customer_repo         = $customer_repo;
		$this->ticket_repo           = $ticket_repo;
		$this->departure_repo        = $departure_repo;
		$this->training_session_repo = $training_session_repo;
		$this->package_repo          = $package_repo;
		$this->course_repo           = $course_repo;
		$this->package_service       = $package_service;
	}

	private function validateAddDetailInput($data)
	{
		$rules = array(
			'booking_id'          => 'required',
			'customer_id'         => 'required',
			'session_id'          => 'required_without:training_session_id',
			'training_session_id' => 'required_without:session_id'
		);

		$validator = \Validator::make($data, $rules);

		if($validator->fails()) {
			throw new InvalidInputException($validator->errors()->all());
		}
	}

	private function validateRemoveDetailInput($data)
	{
		$rules = array(
			'booking_id'       => 'required',
			'bookingdetail_id' => 'required'
		);

		$validator = \Validator::make($data, $rules);

		if($validator->fails()) {
			throw new InvalidInputException($validator->errors()->all());
		}
	}

	protected function getCourseAndTraining($course_id, $ticket, $training_session)
	{
		if(!is_null($course_id)) {
			$course = $this->course_repo->get($course_id);

            if (!$ticket) {
                $training_id = null;
                if ($training_session) {
                    $training_id = $training_session->training_id;
                } elseif (!is_null($training_id)) {
                    $training_id = $training_id;
                } else {
					throw new BadRequestException(['training_id is required when adding a class without date.']);
                }

                try {
                    $training = $course->trainings()->where('id', $training_id)->firstOrFail();
                } catch (ModelNotFoundException $e) {
					throw new NotFoundException(['This class can not be booked in this course.']);
                }
            } else {
                $training = false;
            }
		} else {
			$course  = false;
			$training = false;
		}

		return array(
			'course'   => $course,
			'training' => $training
		);
	}

	protected function calculateDateOfFirstTrip($bookingdetails, $firstAccommodation)
	{
		$firstDetail = $bookingdetails->sortBy(function ($detail) {
			if ($detail->departure) {
				return $detail->departure->start;
			} elseif ($detail->training_session) {
				return $detail->training_session->start;
			} else {
				return '9999-12-31';
			}
		})->first();

		if ($firstDetail->departure) {
			$start = $firstDetail->departure->start;
		} elseif ($firstDetail->training_session) {
			$start = $firstDetail->training_session->start;
		} else {
			$start = null;
		}

		if (!is_null($firstAccommodation)) {
			if ($start !== null) {
				$detailStart = new \DateTime($start);
				$accommStart = new \DateTime($firstAccommodation->pivot->start);

				$start = ($detailStart < $accommStart) ? $detailStart : $accommStart;
				$start = $start->format('Y-m-d H:i:s');
			} else {
				$start = $firstAccommodation->pivot->start;
			}
		}

		if ($start === null) {
			$start = $firstDetail->created_at;
		}
		return $start;	
	}

	protected function calculatePackagePrice($packagefacade)
	{ 
		// Find the first departure datetime that is booked in this package
		$bookingdetails = $packagefacade
			->bookingdetails()
			->with('departure', 'training_session')
			->get();

		$firstAccommodation = $booking
			->accommodations()
			->wherePivot('packagefacade_id', $packagefacade->id)
			->get()
			->sortBy(function ($accommodation) {
				return $accommodation->pivot->start;
			})->first();

		if(empty($firstAccommodation)) {
			$firstAccommodation = null;
		}

		$start = $this->calculateDateOfFirstTrip($bookingdetails, $firstAccommodation);

		$package->calculatePrice($start);
	}

	protected function calculateCoursePrice($course, $booking_id, $customer_id)
	{
		$bookingdetails = $course->bookingdetails()
			->where('booking_id', $booking_id)
			->where('customer_id', $customer_id)
			->with('departure', 'training_session')
			->get();

		$start = $this->calculateDateOfFirstTrip($bookingdetails, null);

		$course->calculatePrice($start);
	}

	protected function updateBookingPrice($package, $course, $departure, $ticket, $bookingdetail, $booking, $customer)
	{
		if($package) {
			$this->calculatePackagePrice($bookingdetail);
		} elseif ($course) {
			$this->calculateCoursePrice($course, $booking->id, $customer->id);
		} else {
			if($departure) {
				$start = $departure->start;
			} else {
				$start = $bookingdetail->created_at;
			}
			$ticket->calculatePrice($start);
		}

		$booking->updatePrice();
	}

	public function create($data)
	{
		$this->validateAddDetailInput($data);
		
		// Get the booking
		$booking = $this->booking_repo->get($data['booking_id'], null);		
		// Get the customer
		$customer = $this->customer_repo->get($data['customer_id']);

		// Get the ticket
		if (!is_null($data['ticket_id'])) {
			$ticket = $this->ticket_repo->get($data['ticket_id']);
		} else {
			$ticket = false;
		}
		// Get the session
		if (!is_null($data['session_id'])) {
			$departure = $this->departure_repo->get($data['session_id']);
			$trip      = $departure->trip;
		} else {
			$departure = false;
			$trip      = false;
		}
		// Get the training session
		if (!is_null($data['training_session_id'])) {
			$training_session = $this->training_session_repo->get($data['training_session_id']);
		} else {
			$training_session = false;
		}

		// Get the package
		$package_data  = $this->package_service->getPackageAndFacade($data, $booking, false);
		$package       = $package_data['package'];
		$packagefacade = $package_data['packagefacade'];
		unset($package_data);

		// Get the course
		$course_data = $this->getCourseAndTraining($data['course_id'], $ticket, $training_session);
		$course      = $course_data['course'];
		$training    = $course_data['training'];
		unset($course_data);

		// Validate that the booking is not cancelled or on hold
        if (!$booking->isEditable()) {
			throw new HTTPForbiddenException(['Cannot add details, because the booking is '.$booking->status.'.']);
        }

		if($data['temporary'] === 1 && (!(is_null('session_id') && is_null('training_session_id')))) {
			throw new InvalidInputException(['temporary is not allowed together with session_id, training_session_id']);
		}
		
		// BEGIN VALIDATION USING *DetailPolicy classes
		(new AddSessionDetailPolicy($departure, $training_session, $customer, $data['temporary']))->allows();
		(new AddCourseDetailPolicy($training_session, $departure, $course, $ticket, $training, $customer, $booking->id))->allows();
		(new AddPackageDetailPolicy($course, $package, $packagefacade, $ticket, $customer->id))->allows();

        if ($departure) {
			(new AddTicketDetailPolicy($ticket, $trip, $course, $package))->allows();
        }
        if ($departure && $trip->boat_required) {
			(new AssignBoatDetailPolicy($ticket, $trip, $departure, $data['boatroom_id']))->allows();
        }

        // Check if we have to create a new packagefacade
        if ($package && !$packagefacade) {
            $packagefacade = new Packagefacade(array('package_id' => $package->id));
            $packagefacade->save();
        }

		// Fill an array with neccessary data for a booking detail
		$bookingdetail_data = array(
			'customer_id' => $customer->id,
            'ticket_id' => $ticket ? $ticket->id : null,
            'session_id' => $departure ? $departure->id : null,
            'boatroom_id' => $departure && $trip->boat_required ? $data['boatroom_id'] : null,
            'packagefacade_id' => $package ? $packagefacade->id : null,
            'course_id' => $course ? $course->id : null,
            'training_session_id' => $training_session ? $training_session->id : null
		);
		
		// Create the booking detail
		$bookingdetail = $this->bookingdetail_repo->create($bookingdetail_data, $data['temporary'], $training);
		$bookingdetail = $booking->bookingdetails()->save($bookingdetail);

        // If this is the booking's first added details and there is no lead customer yet, set lead_customer_id
        if (empty($booking->lead_customer_id) && $booking->bookingdetails()->count() === 1) {
            $booking->update(array('lead_customer_id' => $customer->id));
        }

		// Update the total price of the booking concidering seasonal prices
		$this->updateBookingPrice($package, $course, $departure, $ticket, $bookingdetail, $booking, $customer);

		// @todo Remove addons from array and front end expectation as compulsory addons were removed
        return array(
            'status' => 'OK. Booking details added.',
            'id' => $bookingdetail->id,
            'addons' => false,
            'decimal_price' => $booking->decimal_price,
            'boatroom_id' => $departure && $trip->boat_required ? $data['boatroom_id'] : false,
            'package_decimal_price' => $package ? $package->decimal_price : false,
            'course_decimal_price' => !$package && $course ? $course->decimal_price : false,
            'ticket_decimal_price' => !$package && !$course && $ticket ? $ticket->decimal_price : false,

            'packagefacade_id' => $package ? $packagefacade->id : false,
        );
	}

	public function remove($data)
	{
		$this->validateRemoveDetailInput($data);

		$booking       = $this->booking_repo->get($data['booking_id'], null);	
        $bookingdetail = $booking->bookingdetails()->with('departure', 'training_session')->findOrFail($data['bookingdetail_id']);

        // Validate that the booking is not cancelled or on hold
        if ($booking->status === 'cancelled' || $booking->status === 'on hold') {
			throw new HTTPForbiddenException('Cannot remove details, because the booking is '.$booking->status.'.');
        }

        // Validate that the session start date has not already passed
        if (!$bookingdetail->temporary) {
            $start = !empty($bookingdetail->departure) ? $bookingdetail->departure->start : $bookingdetail->training_session->start;

            if (Helper::isPast($start)) {
				throw new HTTPForbiddenException('Cannot remove details, because the trip/class has already departed/started!');
            }
        }

        // Execute delete
        $bookingdetail->delete();

        // Update booking price
        $booking->updatePrice();

		return $booking;
	}

}

