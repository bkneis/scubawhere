<?php

namespace Scubawhere\Strategies;

use Scubawhere\Context;
use Scubawhere\Entities\Accommodation;
use Scubawhere\Entities\Bookingdetail;

class RevenueReportGenerator implements ReportGeneratorInterface {

	private function getBookingData($afterUTC, $beforeUTC)
	{
		return Bookingdetail::with(
		    	'ticket',
		    	'departure',
		    	'packagefacade',
		    		'packagefacade.package',
		    	'course',
		    	'training_session',
		    	'addons'
		    )
		    ->whereHas('booking', function($query) use ($afterUTC, $beforeUTC)
		    {
		    	$query
		    	    ->where('company_id', Context::get()->id)
		    	    ->whereIn('status', ['confirmed'])
		    	    ->whereBetween('created_at', [$afterUTC, $beforeUTC]);
		    })->get();

		$bookingdetails->load('booking.agent');
	}

	public function createReport($before, $after) 
	{
		$RESULT = array();

		$afterUTC = $after;
		$beforeUTC = $before;

		$counted_packagefacades = $counted_courses = [];

		  $RESULT['tickets']
		= $RESULT['packages']
		= $RESULT['courses']
		= $RESULT['addons']
		= $RESULT['fees']
		= $RESULT['accommodations'] = [];

		  $RESULT['tickets_total']
		= $RESULT['packages_total']
		= $RESULT['courses_total']
		= $RESULT['addons_total']
		= $RESULT['fees_total']
		= $RESULT['accommodations_total'] = ['quantity' => 0, 'revenue' => 0];

		$bookingdetails = $this->getBookingData($after, $before);

		foreach($bookingdetails as $detail)
		{
			if(!empty($detail->ticket_id)/* && !empty($detail->session_id)*/ && empty($detail->packagefacade_id) && empty($detail->course_id))
			{
				### -------------------------------- ###
				### This is a directly booked ticket ###
				### -------------------------------- ###

				if($detail->departure)
					$detail->ticket->calculatePrice($detail->departure->start, $detail->created_at);
				else
					$detail->ticket->calculatePrice($detail->created_at, $detail->created_at);

				$revenue = $detail->ticket->decimal_price;
				$model   = 'tickets';
				$name    = $detail->ticket->name;
				$id      = $detail->ticket->id;
			}
			elseif(empty($detail->packagefacade_id) && !empty($detail->course_id))
			{
				### -------------------------------- ###
				### This is a directly booked course ###
				### -------------------------------- ###

				$identifier = $detail->booking_id . '-' . $detail->customer_id . '-' . $detail->course_id;

				// Only continue, if the course has not been counted yet
				if(!in_array($identifier, $counted_courses))
				{
					$counted_courses[] = $identifier;

					// Find the first departure or training datetime that is booked in this course
					$bookingdetails = $detail->course->bookingdetails()
					    ->where('booking_id', $detail->booking_id)
					    ->where('customer_id', $detail->customer_id)
					    ->with('departure', 'training_session')
					    ->get();

					$firstDetail = $bookingdetails->sortBy(function($d)
					{
						if(!empty($d->departure))
							return $d->departure->start;
						elseif(!empty($d->training_session))
							return $d->training_session->start;
						else
							return $d->created_at;
					})->first();

					$start = $firstDetail->created_at;

					if(!empty($firstDetail->departure))
						$start = $firstDetail->departure->start;
					elseif(!empty($firstDetail->training_session))
						$start = $firstDetail->training_session->start;

					// Calculate the course price at this first departure/training_session datetime
					$detail->course->calculatePrice($start, $detail->created_at);

					$revenue = $detail->course->decimal_price;
					$model   = 'courses';
					$name    = $detail->course->name;
					$id      = $detail->course->id;
				}
				else
					$model = null;
			}
			elseif(!empty($detail->packagefacade_id))
			{
				### ----------------- ###
				### This is a package ###
				### ----------------- ###

				// Only continue, if the package has not been counted yet
				if(!in_array($detail->packagefacade_id, $counted_packagefacades))
				{
					$counted_packagefacades[] = $detail->packagefacade_id;

					// Find the first departure datetime that is booked in this package
					$details = $detail->packagefacade->bookingdetails()->with('departure', 'training_session')->get();
					$firstDetail = $details->sortBy(function($detail)
					{
						if($detail->departure)
							return $detail->departure->start;
						elseif(!empty($detail->training_session))
							return $detail->training_session->start;
						else
							return $detail->created_at;
					})->first();

					if($firstDetail->departure)
						$start = $firstDetail->departure->start;
					elseif(!empty($firstDetail->training_session))
						$start = $firstDetail->training_session->start;
					else
						$start = $firstDetail->created_at;

					$accommodations = $detail->booking->accommodations()->wherePivot('packagefacade_id', $detail->packagefacade_id)->get();
					$firstAccommodation = $accommodations->sortBy(function($accommodation)
					{
						return $accommodation->pivot->start;
					})->first();

					if(!empty($firstAccommodation))
					{
						$detailStart = new DateTime($start);
						$accommStart = new DateTime($firstAccommodation->pivot->start);

						$start = ($detailStart < $accommStart) ? $detailStart : $accommStart;

						$start = $start->format('Y-m-d H:i:s');
					}

					// Calculate the package price at this first departure datetime and sum it up
					$detail->packagefacade->package->calculatePrice($start, $detail->created_at);

					$revenue = $detail->packagefacade->package->decimal_price;
					$model   = 'packages';
					$name    = $detail->packagefacade->package->name;
					$id      = $detail->packagefacade->package->id;
				}
				else
					$model = null;
			}
			else
			{
				### ---------------------------------------- ###
				### The detail does not fall into a category ###
				### ---------------------------------------- ###

				Log::write('ERROR: Unable to parse bookingdetail: ' . json_encode($detail));
				return Response::json(['errors' => ['A bookingdetail cannot be handled, as it doesn\'t fit the rules! Please check the log file to see what happened.']], 500); // 500 Internal Server Error
			}

			$realPricePercentage = ($detail->booking->real_decimal_price === null)
				? 1
				: $detail->booking->real_decimal_price / ($detail->booking->real_decimal_price + $detail->booking->discount);

			if(!empty($model))
			{
				### ---------------------------------- ###
				### Apply all special cases to revenue ###
				### ---------------------------------- ###

				// Apply percentage discount to price and sum up
				$revenue *= $realPricePercentage;

				// If booked through agent, subtract agent's commission
				if(!empty($detail->booking->agent))
				{
					$revenue *= (1 - $detail->booking->agent->commission / 100);
				}

				// Sum revenue and increase counter
				if(empty($RESULT[$model][$id])) $RESULT[$model][$id] = ['name' => $name, 'quantity' => 0, 'revenue' => 0];

				$RESULT[$model][$id]['quantity']++;
				$RESULT[$model][$id]['revenue'] += round($revenue, 2);

				$RESULT[$model . '_total']['quantity']++;
				$RESULT[$model . '_total']['revenue'] += round($revenue, 2);
			}

			### -------------------------------------------- ###
			### Sum up addons that are not part of a package ###
			### -------------------------------------------- ###

			// (packages would have been caught above, because packaged addons are only allowed on tickets of the same package)
			foreach($detail->addons as $addon)
			{
				if(!empty($addon->pivot->packagefacade_id)) continue;

				if($addon->compulsory)
				{
					// Handle as a fee

					// Sum revenue and increase counter
					if(empty($RESULT['fees'][$addon->id])) $RESULT['fees'][$addon->id] = ['name' => $addon->name, 'quantity' => 0, 'revenue' => 0];

					$RESULT['fees'][$addon->id]['quantity']++;
					$RESULT['fees'][$addon->id]['revenue'] += round($addon->decimal_price, 2);

					$RESULT['fees_total']['quantity']++;
					$RESULT['fees_total']['revenue'] += round($addon->decimal_price, 2);
				}
				else
				{
					// Handle as regular addon

					// Apply percentage discount to price and sum up
					$revenue = $addon->decimal_price * $addon->pivot->quantity * $realPricePercentage;

					// If booked through agent, subtract agent's commission
					if(!empty($detail->booking->agent))
					{
						$revenue = $revenue * (1 - $detail->booking->agent->commission / 100);
					}

					// Sum revenue and increase counter
					if(empty($RESULT['addons'][$addon->id])) $RESULT['addons'][$addon->id] = ['name' => $addon->name, 'quantity' => 0, 'revenue' => 0];

					$RESULT['addons'][$addon->id]['quantity'] += $addon->pivot->quantity;
					$RESULT['addons'][$addon->id]['revenue'] += round($revenue, 2);

					$RESULT['addons_total']['quantity'] += $addon->pivot->quantity;
					$RESULT['addons_total']['revenue'] += round($revenue, 2);
				}
			}
		}

		### --------------------- ###
		### Sum up accommodations ###
		### --------------------- ###

		$accommodations = Accommodation::whereHas('bookings', function($query) use ($afterUTC, $beforeUTC)
		{
			$query
			    ->where('company_id', Context::get()->id)
			    ->whereIn('status', ['confirmed'])
			    ->whereBetween('bookings.created_at', [$afterUTC, $beforeUTC]);
		})->get();

		$accommodations->load('bookings.agent');

		foreach($accommodations as $accommodation)
		{
			foreach($accommodation->bookings as $booking)
			{
				// Only continue if the accommodation is not part of a package
				if(empty($booking->pivot->packagefacade_id))
				{
					$accommodation->calculatePrice(
						$booking->pivot->start,
						$booking->pivot->end,
						$booking->pivot->created_at
					);

					$realPricePercentage = ($booking->real_decimal_price === null)
						? 1
						: $booking->real_decimal_price / ($booking->real_decimal_price + $booking->discount);

					// Apply percentage discount to price and sum up
					$revenue = $accommodation->decimal_price * $realPricePercentage;

					// If booked through agent, subtract agent's commission
					if(!empty($booking->agent))
					{
						$revenue = $revenue * (1 - $booking->agent->commission / 100);
					}

					// Sum revenue and increase counter
					if(empty($RESULT['accommodations'][$accommodation->id])) $RESULT['accommodations'][$accommodation->id] = ['name' => $accommodation->name, 'quantity' => 0, 'revenue' => 0];

					$RESULT['accommodations'][$accommodation->id]['quantity']++;
					$RESULT['accommodations'][$accommodation->id]['revenue'] += round($revenue, 2);

					$RESULT['accommodations_total']['quantity']++;
					$RESULT['accommodations_total']['revenue'] += round($revenue, 2);
				}
			}
		}

		return $RESULT;	
	}

}