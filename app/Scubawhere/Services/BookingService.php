<?php

namespace Scubawhere\Services;

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Repositories;
use Scubawhere\Services\LogService;
use Scubawhere\Exceptions\ConflictException;
use Scubawhere\Exceptions\BadRequestException;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Repositories\BookingRepoInterface;
use Scubawhere\Repositories\CustomerRepoInterface;
use Scubawhere\Repositories\BookingDetailRepoInterface;

class BookingService {

	/** 
	 *	Repository to access the booking models
	 *
	 *	@var \Scubawhere\Repositories\BookingRepo
	 */
	protected $booking_repo;

	/** 
	 *	Repository to access the booking detail models
	 *
	 *	@var \Scubawhere\Repositories\BookingRepo
	 */
	protected $booking_detail_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 *
	 * @var \Scubawhere\Services\LogService
	 */
	protected $log_service;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 *
	 * @var \Scubawhere\Services\BookingEditorService
	 */
	protected $booking_editor;

	/** @var \Scubawhere\Repositories\CustomerRepo */
	protected $customer_repo;

	/**
	 * @param BookingRepoInterface     Injected using \Scubawhere\Repositories\BookingRepoServiceProvider
	 * @param LogService               Injected using laravel's IOC container
	 */
	public function __construct(BookingRepoInterface       $booking_repo,
								LogService                 $log_service,
								BookingEditorService       $booking_editor,
								BookingDetailRepoInterface $booking_detail_repo,
								CustomerRepoInterface      $customer_repo)
	{
		$this->booking_repo        = $booking_repo;
		$this->log_service         = $log_service;
		$this->booking_editor      = $booking_editor;
		$this->booking_detail_repo = $booking_detail_repo;
		$this->customer_repo       = $customer_repo;
	}

	/**
     * Get an booking for a company from its id
	 *
     * @param int ID of the booking
	 *
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
	 *
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an booking for a company
     */
	public function get($id, $ref) {
		$booking = $this->booking_repo->getWithTrimmings($id, $ref);
        $pricedPackagefacades = [];
        $pricedCourses = [];

        $booking->bookingdetails->each(function ($detail) use ($booking, &$pricedPackagefacades, &$pricedCourses) {
            $limitBefore = in_array($booking->status, ['reserved', 'expired', 'confirmed']) ? $detail->created_at : false;

            if ($detail->packagefacade_id !== null) {
                if (!array_key_exists($detail->packagefacade_id, $pricedPackagefacades)) {
                    // Find the first departure datetime that is booked in this package
                    // $bookingdetails = $detail->packagefacade->bookingdetails()->with('departure', 'training_session')->get();
                    $firstDetail = $booking->bookingdetails->filter(function ($d) use ($detail) {
                        return $d->packagefacade_id === $detail->packagefacade_id;
                    })
                    ->sortBy(function ($detail) {
                        if ($detail->session) {
                            return $detail->session->start;
                        } elseif ($detail->training_session) {
                            return $detail->training_session->start;
                        } else {
                            return '9999-12-31';
                        }
                    })->first();

                    if ($firstDetail->session) {
                        $start = $firstDetail->session->start;
                    } elseif ($firstDetail->training_session) {
                        $start = $firstDetail->training_session->start;
                    } else {
                        $start = null;
                    }

                    $firstAccommodation = $booking->accommodations->filter(function ($a) use ($detail) {
                        return $a->pivot->packagefacade_id === $detail->packagefacade_id;
                    })
                    ->sortBy(function ($accommodation) {
                        return $accommodation->pivot->start;
                    })->first();

                    if (!empty($firstAccommodation)) {
                        if ($start !== null) {
                            $detailStart = new DateTime($start);
                            $accommStart = new DateTime($firstAccommodation->pivot->start);

                            $start = ($detailStart < $accommStart) ? $detailStart : $accommStart;

                            $start = $start->format('Y-m-d H:i:s');
                        } else {
                            $start = $firstAccommodation->pivot->start;
                        }
                    }

                    // Calculate the package pricregister/e at this first datetime and sum it up
                    if ($start === null) {
                        $start = $firstDetail->created_at;
                    }
                    $detail->packagefacade->package->calculatePrice($start, $limitBefore);

                    $pricedPackagefacades[$detail->packagefacade_id] = $detail->packagefacade->package->decimal_price;
                } else {
                    $detail->packagefacade->package->decimal_price = $pricedPackagefacades[$detail->packagefacade_id];
                }
            } elseif ($detail->course_id !== null) {
                $identifier = $detail->booking_id.'-'.$detail->customer_id.'-'.$detail->course_id;

                if (!array_key_exists($identifier, $pricedCourses)) {
                    // Find the first departure or class datetime that is booked in this course
                    // $bookingdetails = $detail->course->bookingdetails()->with('departure', 'training_session')->get();
                    $firstDetail = $booking->bookingdetails->filter(function ($d) use ($detail) {
                        return $d->course_id === $detail->course_id;
                    })
                    ->sortBy(function ($detail) {
                        if ($detail->session) {
                            return $detail->session->start;
                        } elseif ($detail->training_session) {
                            return $detail->training_session->start;
                        } else {
                            return '9999-12-31';
                        }
                    })->first();

                    if ($firstDetail->session) {
                        $start = $firstDetail->session->start;
                    } elseif ($firstDetail->training_session) {
                        $start = $firstDetail->training_session->start;
                    } else {
                        $start = $firstDetail->created_at;
                    }

                    // Calculate the package price at this first departure datetime and sum it up
                    $detail->course->calculatePrice($start, $limitBefore);

                    $pricedCourses[$identifier] = $detail->course->decimal_price;
                } else {
                    $detail->course->decimal_price = $pricedCourses[$identifier];
                }
            } else {
                // Sum up the ticket
                if ($detail->departure) {
                    $start = $detail->departure->start;
                } else {
                    $start = $detail->created_at;
                }

                $detail->ticket->calculatePrice($start, $limitBefore);
            }

            // Calculate add-ons
            $detail->addons->each(function ($addon) use ($detail) {
                if (!empty($addon->pivot->packagefacade_id)) {
                    return;
                }

                if ($detail->departure) {
                    $start = $detail->departure->start;
                } else {
                    $start = $detail->created_at;
                }

                $addon->calculatePrice($start);
            });
        });

        $booking->accommodations->each(function ($accommodation) use ($booking, &$pricedPackagefacades) {
            if (empty($accommodation->pivot->packagefacade_id)) {
                $limitBefore = in_array($booking->status, ['reserved', 'expired', 'confirmed']) ? $accommodation->pivot->created_at : false;

                $accommodation->calculatePrice($accommodation->pivot->start, $accommodation->pivot->end, $limitBefore);
            } else {
                $accommodation->package = Packagefacade::find($accommodation->pivot->packagefacade_id)->package;
            }

            $accommodation->customer = Customer::find($accommodation->pivot->customer_id);
        });

        return $booking;
	}

	/**
     * Get all bookings for a company
	 *
     * @param int ID of the booking
	 *
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all bookings for a company
     */
	public function getAll($from, $take) {
		return $this->booking_repo->all($from, $take);
	}

	/**
     * Get all bookings for a company including soft deleted models
	 *
     * @param int ID of the booking
	 *
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all bookings for a company including soft deleted models
     */
	public function getAllWithTrashed() {
		return $this->booking_repo->allWithTrashed();
	}

	public function getFilter($data, $from, $take)
	{
        if (empty($data['reference']) && empty($data['date']) && empty($data['lastname'])) {
            return $this->getAll();
        }

        if (!empty($data['date'])) {
            $data['date'] = new \DateTime($data['date'], new \DateTimeZone($this->company_model->timezone));
        }

		return $this->booking_repo->filter($data, $from, $take);
	}

	public function getCustomerBookings($id)
	{
		if (empty($id)) {
			throw new InvalidInputException(['The customer ID is not found']);
        }

		return $this->booking_repo->customerBookings($id);
	}

	// @note should this be in the booking repo? It is responsible for retrieving data but I do not like
	// the idea of having alot of functions in the repo then a corresponding function in the service just because
	public function getPayments($id)
	{
		if(is_null($id)) {
			throw new InvalidInputException(['Please provide a booking id to get the payments for']);
		}

		$booking = $this->booking_repo->getOrFail($id, null);
		return $booking->payments()->with('paymentgateway')->get();
	}

	public function getRefunds()
	{
		if(is_null($id)) {
			throw new InvalidInputException(['Please provide a booking id to get the payments for']);
		}

		$booking = $this->booking_repo->getOfFail($id, null);
		return $booking->refunds()->with('paymentgateway')->get();
	}

	public function sendConfirmationEmail($id)
	{
		if(is_null($id)) {
			throw new InvalidInputException(['Please provide a booking id']);
		}

		CrmMailer::sendBookingConf($data['booking_id']);
	}

	public function setLead($data)
	{
		if(is_null($data['customer_id'])) {
			throw new InvalidInputException(['Please provide a customer id to set as lead']);
		}

		$booking = $this->booking_repo->getOrFail($data['booking_id'], null);	

        // Validate that the booking is not cancelled or on hold
        if ($booking->status === 'cancelled') {
			throw new HTTPForbiddenException(['Cannot change lead customer, because the booking is '.$booking->status.'.']);
        }

		$customer = $this->customer_repo->getOrFail($data['customer_id']);

        if (!$booking->update(array('lead_customer_id' => $customer->id))) {
			throw new InvalidInputException(['errors' => $booking->errors()->all()]);
		}

		return $booking;
	}

	public function editInfo($data)
	{
		$booking = $this->booking_repo->getOrFail($data['booking_id'], null);

        // Validate that the booking is not cancelled or on hold
        if ($booking->status === 'cancelled') {
			throw new HTTPForbiddenException(['Cannot edit details, because the booking is '.$booking->status.'.']);
        }

        if (empty($data['discount']) && $data['discount'] !== 0 && $data['discount'] !== '0') {
            $data['discount'] = null;
        }

        $oldDiscount = $booking->discount;

        if (!$booking->update($data)) {
			throw new InvalidInputException(['errors' => $booking->errors()->all()]);
        }

        if (!is_null($data['discount'])) {
            $booking->updatePrice(true, $oldDiscount);
		}

		return $booking;
	}

	/**
	 * Validate, create and save the booking and prices to the database
	 *
	 * @param  array Data to autofill booking model
	 *
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model for the booking
	 */
	public function create($data) 
	{
		return $this->booking_repo->create($data);
	}

	/**
	 * Validate, update and save the booking and prices to the database
	 *
	 * @param  int   $id           ID of the booking
	 * @param  array $data         Information about booking
	 *
	 * @return \Illuminate\Database\Eloquent\Model Eloquent model of the booking
	 */
	public function update($id, $data) 
	{
    	return $this->booking_repo->update($id, $data);
	}

	/**
	 * Remove the booking from the database.
	 *
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the booking, and the booking ids are then logged
	 *
	 * @throws \Scubawhere\Exceptions\ConflictException
	 * @throws Exception
	 *
	 * @param  int $id ID of the booking
	 */
	public function delete($id)
	{
		
	}

	/**
	 * Duplicate a booking with all of its details and ammend its refrence to mark that it is being edited.
	 *
	 * @param int $id ID of the booking
	 *
	 * @return The edited booking with all of its details
	 */
	public function startEditor($id)
	{
		if(!$id) {
			throw new InvalidInputException(['Please provide a booking ID to start editing']);
		}

		$booking = $this->booking_repo->get($id, null);
		//$details = $this->booking_detail_repo->getWhere(['booking_id' => $booking->id]);
		$details = \DB::table('booking_details')->where('booking_id', $booking->id)->get();

		$edit_booking_id = $this->booking_editor->startEditing($booking, $details);

		return $this->get($edit_booking_id, null);
	}

	/**
	 * Apply the changes to an edited booking and remove the temporary booking.
	 *
	 * @param int $id ID of the booking
	 *
	 * @return array anon Information of the updated booking 
	 */
	public function applyEdits($id)
	{
		if(is_null($id)) {
			throw new InvalidInputException(['Please provide a booking ID to save your changes.']);
		}

		$booking = $this->booking_repo->get($id, null);
		$parent = $this->booking_repo->get($booking->parent_id, null);

		return $this->booking_editor->applyChanges($booking, $parent);
	}

}
