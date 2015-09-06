<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;

class CrmCampaignController extends Controller {

	public function getIndex() {

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->campaigns()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The campaign could not be found.')), 404 ); // 404 Not Found
		}

	}

	public function getAll()
	{
		return Auth::user()->campaigns()->get();
	}

	public function getAllWithTrashed()
	{
		return Auth::user()->campaigns()->withTrashed()->get();
	}

	public function postAdd()
	{
		$data = Input::only(
			'subject',
			'message'
		);

		$data['sent_at'] = Helper::localTime();

		$group_ids = Input::only('groups')['groups'];

		$customer_emails = [];

		$tmpRules = [];
		$rules = [];
		$rules['certs'] = [];
		$rules['classes'] = [];
		$rules['tickets'] = [];

		// FORMAT RULES INTO IDS TO FILTER THROUGH
		foreach ($group_ids as $group_id) {
			$group = Auth::user()->crmGroups()->where('id', '=', $group_id)->with('rules')->first();
			$tmpRules = $group->rules;
			foreach ($tmpRules as $rule) {
				// Translate agency to certification ids
				if($rule->agency_id !== null) {
					$agency = Agency::with('certificates')->findOrFail( $rule->agency_id ); // Auth::user()->agencies() etc gives eeror
					$certs = $agency->certificates;
					foreach ($certs as $cert) {
						array_push($rules['certs'], $cert->id); // ->id
					}
				}
				else if($rule->certificate_id !== null) {
					array_push($rules['certs'], $rule->certificate_id);
				}
				else if($rule->training_id !== null) {
					array_push($rules['classes'], $rule->training_id);
				}
				else if($rule->ticket_id !== null) {
					array_push($rules['tickets'], $rule->ticket_id);
				}
			}
		}

		// SEARCH CUSTOMER EMAILS BY CERTIFICATION
		/*$customers = Auth::user()->customers()->with('certificates')->get();
		foreach ($customers as $customer) {
			foreach ($customer->certificates as $certificate) {
				if(in_array($certificate->id, $rules['certs'])) {
					array_push($customer_emails, $customer->email);
					break;
				}
			}
		}*/

		$certificates_customers = Auth::user()->customers()->whereHas('certificates', function($query) use ($rules){
			$query->whereIn('id', $rules['certs']);
		})->lists('email');

		array_merge($customer_emails, $certificates_customers);



		// SEARCH BOOKINGS FOR TICKET AND TRAINING ID FOR CUSTOMER ID
		/*$bookings = Auth::user()->bookings()->with('bookingdetails')->get();
		foreach ($bookings as $booking) {
			foreach ($booking->bookingdetails as $detail) {
				if(in_array($detail->training_id, $rules['classes']) || in_array($detail->ticket_id, $rules['tickets'])) {
					$cust = Auth::user()->customers()->where('id', '=', $detail->customer_id)->first();
					if(!in_array($cust->email, $customer_emails)) array_push($customer_emails, $cust->email);
				}
			}
		}*/

		//$bookings = Auth::user()->bookings()->with('bookingdetails')->whereHas('ticket_id')->get();
		$booked_customers = Auth::user()->bookingdetails()->whereHas('booking', function($query) use($rules) {
			$query->where('status', 'confirmed'); // changed confirmed to completed when soren pushes it
		})->whereIn('ticket_id', $rules['tickets']) //->orWhereIn('training_id', $rules['classes']) ADD WHEN SOREN ADDS TRAINING IS TO DETAILS
		->leftJoin('customers', 'customers.id', '=', 'booking_details.customer_id')->lists('email');

		array_merge($customer_emails, $booked_customers);
		$customer_emails = array_unique($customer_emails);

		// SEND EMAILS TO CUSTOMER EMAILS ARRAY HERE

		$data['num_sent'] = sizeof($customer_emails);

		$campaign = new CrmCampaign($data);

		if( !$campaign->validate() )
		{
			return Response::json( array('errors' => $campaign->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('emails' => $customer_emails), 201 );

		// HOW TO SAVE CAMPAIGN AND ASIGN PIVOT VALUES TO ATTACH CAMPAIGNS TO GROUPS-------------
		$campaign = Auth::user()->campaigns()->save($campaign);

		$campaign->groups()->sync($group_ids);

		/*foreach ($group_ids as $group_id) {
			$campaign->groups()->attach($group_id);
		}*/
		// --------------------------------------------------------------------------------------

		return Response::json( array('status' => '<b>OK</b> Campaign created and emails sent', 'id' => $campaign->id, 'emails' => $customer_emails), 201 ); // 201 Created
	}

}