<?php

namespace Scubawhere\Services;

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Repositories\AddonRepoInterface;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Repositories\PickupRepoInterface;
use Scubawhere\Repositories\BookingRepoInterface;
use Scubawhere\Exceptions\HTTPForbiddenException;
use Scubawhere\Repositories\CustomerRepoInterface;
use Scubawhere\Repositories\BookingdetailRepoInterface;
use Scubawhere\Repositories\AccommodationRepoInterface;

class BookingExtrasService
{
	protected $booking_repo;

	protected $addon_repo;

	protected $bookingdetail_repo;

	protected $package_service;

	public function __construct(BookingRepoInterface       $booking_repo,
								AddonRepoInterface         $addon_repo,
								BookingdetailRepoInterface $bookingdetail_repo,
								AccommodationRepoInterface $accommodation_repo,
								CustomerRepoInterface      $customer_repo,
								PackageService             $package_service,
								PickupRepoInterface        $pickup_repo)
	{
		$this->booking_repo       = $booking_repo;
		$this->addon_repo         = $addon_repo;
		$this->bookingdetail_repo = $bookingdetail_repo;
		$this->accommodation_repo = $accommodation_repo;
		$this->customer_repo      = $customer_repo;
		$this->package_service    = $package_service;
		$this->pickup_repo        = $pickup_repo;
	}
	
	// @todo move this to helper
	private function moreThan5DaysAgo($date)
    {
        $local_time = Helper::localTime();
        $test_date = new \DateTime($date, new \DateTimeZone(Context::get()->timezone));

        if ($local_time->diff($test_date)->format('%R%a') < -5) {
            return true;
        }

        return false;
    }

	protected function attachAddonToBooking($pivotData, $bookingdetail, $packagefacade, $addon, $quantity)
	{
        // Check if the addon already exists on the pivot table
		$existingAddon = $bookingdetail->addons()
			->wherePivot('packagefacade_id', $packagefacade ? $packagefacade->id : null)
			->where('id', $addon->id)
			->first();

        if ($existingAddon) {
            // The addon is already assigned to the bookingdetail
            $pivotData['quantity'] = $existingAddon->pivot->quantity + $quantity;
            $bookingdetail->addons()
                ->wherePivot('packagefacade_id', $packagefacade ? $packagefacade->id : null)
                ->updateExistingPivot($addon->id, $pivotData);
        } else {
            $pivotData['quantity'] = $quantity;
            $bookingdetail->addons()->attach($addon->id, $pivotData);
        }
	}

	protected function updateBookingPrice($package, $bookingdetail, $addon, $booking)
	{
        // Update booking price
        if (!$package) {
            if ($bookingdetail->departure) {
                $start = $bookingdetail->departure->start;
            } else {
                $start = $bookingdetail->created_at;
            }

            $addon->calculatePrice($start);
            $booking->updatePrice(); // Only need to update if not a package, because otherwise the price doesn't change
        }
		return $booking;
	}

	public function addAddon($data)
	{
		$booking = $this->booking_repo->get($data['booking_id'], null);
		
        // Validate that the booking is not cancelled or on hold
        if ($booking->status === 'cancelled') {
            return Response::json(array('errors' => array('Cannot add addon, because the booking is '.$booking->status.'.')), 403); // 403 Forbidden
        }

        $bookingdetail = $booking->bookingdetails()->with('departure', 'ticket')->findOrFail($data['bookingdetail_id']);

        // Validate that the bookingdetail is for a trip and not a class
        if (empty($bookingdetail->ticket)) {
			throw new HTTPForbiddenException(['Addons can only be added to trips, not classes.']);
        }

        // Check if trip departed more than 5 days ago
        if ($bookingdetail->departure && $this->moreThan5DaysAgo($bookingdetail->departure->start)) {
			throw new HTTPForbiddenException(['The addon cannot be added because the trip departed more than 5 days ago.']);
        }

		$addon = $this->addon_repo->get($data['addon_id']);

        $pivotData = [];

		// If the addon is part of a package, assign the packagefacade id to the pivot
		// and validate the addon is apart of the package
		if(!is_null($data['packagefacade_id'])) {
			$packagefacade = $booking->getPackagefacade($data['packagefacade_id']);
			$package       = $packagefacade->package()->with('addons')->first();

			(new AddPackagedAddonPolicy($package, $packagefacade, $addon, $booking))->allows();
            $pivotData['packagefacade_id'] = $packagefacade->id;
		} else {
			$package       = false;
			$packagefacade = false;
		}

		$this->attachAddonToBooking($pivotData, $bookingdetail, $packagefacade, $addon, $data['quantity']);

		$this->updateBookingPrice($package, $bookingdetail, $addon, $booking);

		return array('booking' => $booking, 'addon' => $addon);
	}

	public function validateRemoveAddon($data)
	{
		$rules = array(
			'booking_id'       => 'required',
			'bookingdetail_id' => 'required',
			'packagefacade_id' => '',
			'addon_id'         => 'required'
		);

		$validator = \Validator::make($data, $rules);

		if($validator->fails()) {
			throw new InvalidInputException($validator->errors()->all());
		}
	}

	public function removeAddon($data)
	{
		$this->validateRemoveAddon($data);

		$booking = $this->booking_repo->get($data['booking_id'], null);

		if($booking->status === 'cancelled') {
			throw new NotFoundException(['Cannot remove addon, because the booking is '.$booking->status.'.']);
		}

		try {
			$bookingdetail = $booking->bookingdetails()
				->with('departure')
				->findOrFail($data['bookingdetail_id']);
		} catch(ModelNotFoundException $e) {
			throw new NotFoundException('The booking detail could not be found, please check that the bookingdetail_id is valid');
		}

        // Check if trip departed more than 5 days ago
        if ($bookingdetail->departure && $this->moreThan5DaysAgo($bookingdetail->departure->start)) {
			throw new HTTPForbiddenException(['The addon cannot be removed because the trip departed more than 5 days ago.']);
        }
		
		try {
			$addon = $bookingdetail->addons()
				->wherePivot('packagefacade_id', $data['packagefacade_id'])
				->where('id', $data['addon_id'])
				->firstOrFail();
		} catch(ModelNotFoundException $e) {
			throw new NotFoundException(['The addon could not be found, please ensure the addon_id is valid']);
		}

        $pivotData = ['packagefacade_id' => $data['packagefacade_id']];

        // Check the quantity the addon
        if ($addon->pivot->quantity > 1) {
            // Just substract one from the quantity
            $pivotData['quantity'] = --$addon->pivot->quantity;
            $bookingdetail->addons()
                ->wherePivot('packagefacade_id', $data['packagefacade_id'])
                ->updateExistingPivot($addon->id, $pivotData);
        } else {
            // Don't need to check if addon belongs to company because detaching wouldn't throw an error if it's not there in the first place.
			$bookingdetail->addons()
				->wherePivot('packagefacade_id', $data['packagefacade_id'])
				->detach($addon->id);
        }

        // Update booking price, only need to update if not a package, because otherwise the price doesn't change
        if (empty($addon->pivot->packagefacade_id)) {
            $booking->updatePrice();
        } 

		return $booking;
	}

	protected function validateAddAccommodation($data)
	{
		$rules = array(
			'booking_id'       => 'required',
			'accommodation_id' => 'required',
			'customer_id'      => 'required',
			'start'            => 'required|date',
			'end'              => 'required|date',
			'package_id'       => '',
			'packagefacade_id' => ''
		);

		$validator = \Validator::make($data, $rules);

		if($validator->fails()) {
			throw new InvalidInputException($validator->errors()->all());
		}
	}

	public function addAccommodation($data)
	{
		$this->validateAddAccommodation($data);	
		
		// Check if the booking belongs to the company
        try {
            $booking = $this->booking_repo->get($data['booking_id'], null); 
        } catch (ModelNotFoundException $e) {
			throw new NotFoundException(['The booking could not be found.']);
        }

        // Validate that the booking is not cancelled or on hold
        if ($booking->status === 'cancelled') {
			throw new HTTPForbiddenException(['Cannot add accommodation, because the booking is '.$booking->status.'.']); 
        }

        // Check if the accommodation belongs to the company
        try {
            $accommodation = $this->accommodation_repo->get($data['accommodation_id']); 
        } catch (ModelNotFoundException $e) {
			throw new NotFoundException('The accommodation could not be found.');
        }
		
		// Check if the customer belongs to the company
        try {
            $customer = $this->customer_repo->get($data['customer_id']);
        } catch (ModelNotFoundException $e) {
			throw new NotFoundException(['The customer could not be found.']);
		}

		$start = new \DateTime($data['start'], new \DateTimeZone(Context::get()->timezone));
        $end = new \DateTime($data['end'],   new \DateTimeZone(Context::get()->timezone));

        if ($start->diff($end)->format('%R%a') < 1) {
			throw new BadRequestException(['The end date must be after the start date.']);
        }

        // Validate that the start and end dates are maximum 1 days ago
        $now = Helper::localTime();
        if ($start->diff($now)->format('%R%a') > 1 || $end->diff($now)->format('%R%a') > 1) {
			throw new BadRequestException(['The start date can only be a maximum of 1 day ago.']);
        }
		
		// Validate that the accommodation has not already been booked by the customer for the same day within the booking
        $alreadyBooked = $booking->accommodations()
            ->wherePivot('customer_id', $customer->id)
            ->wherePivot('start', $data['start'])
            ->where('id', $accommodation->id)
            ->exists();

        if ($alreadyBooked) {
			throw new HTTPForbiddenException('Cannot add accommodation, because the customer is already booked on it for this start day.');
        }

		$package_data = $this->package_service->getPackageAndFacade($data, $booking, true);
		$package       = $package_data['package'];
		$packagefacade = $package_data['packagefacade'];
		unset($package_data);
		
		// Validate that the accommodation can be booked as part of the package
        if ($package) {
            $exists = $package->accommodations()->where('id', $accommodation->id)->exists();
            if (!$exists) {
                return Response::json(['errors' => ['This accommodation can not be booked as part of this package.']], 403);
            }

            // Check if the package still has space for the number of nights selected
            $numberOfNights = $start->diff($end)->format('%a');

            $alreadyBookedNights = 0;
            $alreadyBookedNights = $booking->accommodations()
                ->wherePivot('packagefacade_id', $packagefacade->id)
                ->where('id', $accommodation->id)
                ->sum(DB::raw('DATEDIFF(end, start)'));

			$package_nights = $package->accommodations()
				->where('id', $accommodation->id)
				->first()
				->pivot->quantity;

            if (($alreadyBookedNights + $numberOfNights) > $package_nights) {
				throw new HTTPForbiddenException(['The accommodation cannot be booked because the package\'s limit for the accommodation would be exceeded.']);
            }
        }

		// Check if accommodation is available for each of the selected days
        $current_date = clone $start;
        $end_date = $end;
        do {
			$accommodation_booked_rooms = $accommodation->bookings()
                ->wherePivot('start', '<=', $current_date)
                ->wherePivot('end', '>', $current_date)
				->filterByCountedStatus()
                ->count(); 

            if ($accommodation_booked_rooms >= $accommodation->capacity) {
				throw new HTTPForbiddenException('The accommodation is not available for '.$current_date->format('D, j M Y').'!');
            }

            $current_date->add(new \DateInterval('P1D'));

        } while ($current_date < $end_date);

        $pivotData = array('customer_id' => $customer->id, 'start' => $start, 'end' => $end);
        if ($packagefacade) {
            $pivotData['packagefacade_id'] = $packagefacade->id;
        }

        $booking->accommodations()->attach($accommodation->id, $pivotData);

        // Update booking price
        if (!$package) {
            $accommodation->calculatePrice($start, $end);
        }

        $booking->updatePrice();

		return array(
			'booking'       => $booking,
			'package'       => $package,
			'accommodation' => $accommodation,
			'packagefacade' => $packagefacade
		);
	}

	protected function validateRemoveAccommodation($data)
	{
		$rules = array(
			'booking_id'       => 'required|integer',
			'accommodation_id' => 'required|integer',
			'customer_id'      => 'required|integer',
			'start'            => 'required|date'
		);

		$validator = \Validator::make($data, $rules);

		if($validator->fails()) {
			throw new InvalidInputException($validator->errors()->all());
		}
	}

	public function removeAccommodation($data)
	{
		$this->validateRemoveAccommodation($data);	
		
        try {
            $booking = $this->booking_repo->get($data['booking_id'], null); 
        } catch (ModelNotFoundException $e) {
			throw new NotFoundException(['The booking could not be found.']);
        }

        // Validate that the booking is not cancelled or on hold
        if ($booking->status === 'cancelled') {
			throw new HTTPForbidden(['Cannot remove accommodation, because the booking is '.$booking->status.'.']);
        }

        // Don't need to check if accommodation belongs to company because detaching wouldn't throw an error if it's not there in the first place.
        $affectedRows = $booking->accommodations()
            ->wherePivot('customer_id', $data['customer_id'])
            ->wherePivot('start', $data['start'])
            ->detach($data['accommodation_id']);

        if ($affectedRows === 0) {
			throw NotFoundException(['The accommodation pivot model could not be found.']);
        }

        // Update booking price
		$booking->updatePrice();

		return $booking;
	}

	protected function validateAddPickUp($booking_id, $data)
	{
		$rules = array(
			'booking_id' => 'required|integer',
            'location'   => 'required',
            'date'       => 'required',
            'time'       => 'required',
            'quantity'   => 'required'
		);

		$data['booking_id'] = $booking_id;
		$validator = \Validator::make($data, $rules);
		unset($data['booking_id']);

		if ($validator->fails()) {
			throw new InvalidInputException($validator->errors()->all());
		}
	}

	public function addPickUp($booking_id, $data)
	{
		$this->validateAddPickUp($booking_id, $data);

		$booking = $this->booking_repo->getOrFail($booking_id, null);

        // Validate that the booking is not cancelled or on hold
        if ($booking->status === 'cancelled') {
			throw new HTTPForbiddenException(['Cannot add pick-up, because the booking is '.$booking->status.'.']);
		}

		if (empty($data['date']) || empty($data['time'])) {
			throw new InvalidInputException(['Please submit both a date and a time for the pick-up.']);
        }

        $datetime = new \DateTime($data['date'].' '.$data['time']);
        $data['date'] = $datetime->format('Y-m-d');
        $data['time'] = $datetime->format('H:i:s');

		$pick_up = $this->pickup_repo->create($data);
		$pick_up = $booking->pick_ups()->save($pick_up);

		return $pick_up;
	}

	public function removePickUp($data)
	{
		$booking = $this->booking_repo->getOrFail($data['booking_id'], null);	
		$booking->pick_ups()->find($data['id'])->delete();
	}

}

