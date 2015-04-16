<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;

class NotificationsController extends Controller {

	public function getAll($from = 0, $take = 20) {

		$NOTIFICATIONS = [];

		if(!Auth::user()->initialised) $NOTIFICATIONS['init'] = 'Please start the tour!';

		$bookings = Auth::user()->bookings()
			->orderBy('id', 'DESC')
			->skip($from)
			->take($take)
			->get();

		$overdue = [];
		$expiring = [];
		
		foreach($bookings as $booking) {

			$amountPaid = 0;
			foreach($booking->payments as $payment) {
				$amountPaid += $payment->amount;
			}
			if($booking->price > $amountPaid) $overdue[$booking->id] = $booking->price - $amountPaid;

			/* Get all booking that expire within 30 minutes */
			$reservedDate = new DateTime( $booking->reserved, new DateTimeZone( Auth::user()->timezone ) );
			if( $reservedDate < new DateTime('+30 minutes', new DateTimeZone( Auth::user()->timezone ))
					&&  $reservedDate > new DateTime('now', new DateTimeZone( Auth::user()->timezone ))) {
				$expiring[$booking->id] = $booking->reserved;
			}

		}

		$NOTIFICATIONS['overduePayments'] = $overdue;
		$NOTIFICATIONS['warning_time'] = new DateTime('+30 minutes', new DateTimeZone( Auth::user()->timezone ));
		$NOTIFICATIONS['expiringBookings'] = $expiring;

		return $NOTIFICATIONS;
	}

}
