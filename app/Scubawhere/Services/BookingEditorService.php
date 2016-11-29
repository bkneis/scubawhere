<?php 

namespace Scubawhere\Services;

use Scubawhere\Repositories\BookingRepo;
use Scubawhere\Repositories\BookingDetailRepo;
use Scubawhere\Exceptions\InternalErrorException;

class BookingEditorService {

	private function duplicateBooking($booking)
	{
		//$new_booking = $booking->replicate();
		$new_booking = \DB::table('bookings')->find($booking->id);

		$new_booking->status     = 'temporary';
		$new_booking->parent_id  = $booking->id;
		$new_booking->reference .= '_';
		$new_booking->updated_at = date('Y-m-d H:i:s');
		unset($new_booking->id);

		return \DB::table('bookings')->insertGetId((array) $new_booking);
	}

	private function duplicateBookingDetails($details, $new_booking_id)
	{
        // Dublicate entries in booking_details, addon_bookingdetail, accommodation_booking and pick_ups
        $detail_dict = [];
        foreach ($details as $detail) {
            $temp = $detail->id;

            unset($detail->id);
            $detail->booking_id = $new_booking_id;

            $new_detail_id = \DB::table('booking_details')->insertGetId((array) $detail);

            $detail_dict[$temp] = $new_detail_id;
        }
		return $detail_dict;
	}

	private function duplicateAddons($detail_dict)
	{
        $addons = \DB::table('addon_bookingdetail')->whereIn('bookingdetail_id', array_keys($detail_dict))->get();
        foreach ($addons as &$addon) {
            $addon->bookingdetail_id = $detail_dict[$addon->bookingdetail_id];

            $addon = (array) $addon;
        }
        if (!empty($addons)) {
            \DB::table('addon_bookingdetail')->insert($addons);
        }
	}

	private function duplicateAccommodations($booking)
	{
        $accommodations = \DB::table('accommodation_booking')->where('booking_id', $booking->id)->get();
        foreach ($accommodations as &$accommodation) {
            $accommodation->booking_id = $new_booking_id;

            $accommodation = (array) $accommodation;
        }
        if (!empty($accommodations)) {
            \DB::table('accommodation_booking')->insert($accommodations);
        }
	}

	private function duplicatePickups($booking)
	{
        $pickups = \DB::table('pick_ups')->where('booking_id', $booking->id)->get();
        foreach ($pickups as &$pickup) {
            unset($pickup->id);
            $pickup->booking_id = $new_booking_id;

            $pickup = (array) $pickup;
        }
        if (!empty($pickups)) {
            \DB::table('pick_ups')->insert($pickups);
        }
	}

	public function startEditing($booking, $details)
	{
		if ($booking->status === 'temporary') {
			return $booking->id;
        }

        // Check if a dublicate is already in the DB
        if (\DB::table('bookings')->where('reference', $booking->reference.'_')->exists()) {
        	throw new BookingAlreadyEditedException(['This booking is already being edited. Cancel the edit and then try again.']);
            //return Response::json(['errors' => ['This booking is already being edited. Cancel the edit and then try again.']], 412);
        } // 412 Precondition Failed

		try 
		{
			\DB::beginTransaction();

			$new_booking_id = $this->duplicateBooking($booking);

			$detail_dict = $this->duplicateBookingDetails($details, $new_booking_id);

			$this->duplicateAddons($detail_dict);

			$this->duplicatePickups($booking);

			\DB::commit();
		}
		catch(\Exception $e)
		{
			\DB::rollback();
			throw $e;
			throw new InternalErrorException();
		}

        return $new_booking_id;
	}

	public function applyChanges($booking, $parent)
	{
		try
		{
			\DB::beginTransaction();
			// Move existing payments and refunds over to new booking's ID
			\DB::table('payments')->where('booking_id', $parent->id)->update(['booking_id' => $booking->id]);
			\DB::table('refunds')->where('booking_id', $parent->id)->update(['booking_id' => $booking->id]);

			// Update status to original status
			$booking->status = $parent->status;
			// Clear parent_id
			$booking->parent_id = null;
			// Save new booking to apply null for parent_id (otherwise the dublicate booking will get deleted when the parent gets deleted, because of the foreign key restraints)
			$booking->updateUniques();

			// Delete old booking record
			$parent->delete();

			// Remove appended underscore from reference and update new booking (reference can't be updated earlier, because of UNIQUE rule on reference column)
			$booking->reference = substr($booking->reference, 0, -1);
			$booking->updateUniques();

			\DB::commit();
		}
		catch(\Exception $e)
		{
			\DB::rollback();
			throw new InternalErrorException();
		}

		return array(
			'status'            => 'OK. Changes applied.',
            'booking_status'    => $booking->status,
            'booking_reference' => $booking->reference,
            'payments'          => $booking->payments,
            'refunds'           => $booking->refunds,
		);
	}

}
