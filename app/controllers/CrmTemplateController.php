<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;
use ScubaWhere\Context;

class CrmTemplateController extends Controller {
    
    public function postAdd()
	{
		$data = Input::only(
			'html_string',
            'name'
		);

		$template = new CrmTemplate($data);

		if( !$template->validate() )
		{
			return Response::json( array('errors' => $template->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$template = Context::get()->templates()->save($template);

		return Response::json( array('status' => '<b>OK</b> Email template has been saved'), 201 ); // 201 Created

	}
    
    public function getAll()
	{
		return Context::get()->templates()->get();
	}
    
    public function postSendTemplate()
	{
		$data = Input::only(
			'subject',
            'groups',
            'campaign_id'
		);

		$data['sent_at'] = Helper::localTime();

		$group_ids = $data['groups']['groups'];

		$customers = [];

		$tmpRules = [];
		$rules = [];
		$rules['certs'] = [];
		$rules['classes'] = [];
		$rules['tickets'] = [];

		// FORMAT RULES INTO IDS TO FILTER THROUGH
		foreach ($group_ids as $group_id) {
			$group = Context::get()->crmGroups()->where('id', '=', $group_id)->with('rules')->first();
			$tmpRules = $group->rules;
			foreach ($tmpRules as $rule) {
				// Translate agency to certification ids
				if($rule->agency_id !== null) {
					$agency = Agency::with('certificates')->findOrFail( $rule->agency_id ); // Context::get()->agencies() etc gives eeror
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

		$certificates_customers = Context::get()->customers()->whereHas('certificates', function($query) use ($rules){
			$query->whereIn('certificates.id', $rules['certs']);
		})->lists('email', 'firstname', 'lastname');

		$customers = array_merge($customers, $certificates_customers);

		$booked_customers = Context::get()->bookingdetails()->whereHas('booking', function($query) use($rules) {
			$query->where('status', 'confirmed'); // changed confirmed to completed when soren pushes it
		})->whereIn('ticket_id', $rules['tickets']) //->orWhereIn('training_id', $rules['classes']) ADD WHEN SOREN ADDS TRAINING IS TO DETAILS
		->leftJoin('customers', 'customers.id', '=', 'booking_details.customer_id')->lists('email', 'firstname', 'lastname');

		$customers = array_merge($customers, $booked_customers);
		$customers = array_unique($customers);

		return Response::json( array('customers' => $customers), 200 ); // GET RID OF AFTER DEBUGGING

        if($customers['email']) 
        {
            return Response::json( array('errors' => 'Sorry, you cannot send an email with no customers in the groups' )); // 406 Not Acceptable
        }
        
        $data['num_sent'] = sizeof($customers['email']);

		$campaign = new CrmCampaign($data);

		if( !$campaign->validate() )
		{
			return Response::json( array('errors' => $campaign->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$campaign = Context::get()->campaigns()->save($campaign);

		$campaign->groups()->sync($group_ids);

		// LOOP THROUGH CUSTOMER EMAILS AND SEND THEM EMAIL
        
        $html_string = Context::get()->campagins()->findOrFail($data['campaign_id'])->html_string;

		foreach($customers as $customer)
		{
			$cust_name = $customer['firstname'] + ' ' + $customer['lastname'];
			$html_string = str_replace('{{customer_name}}', $cust_name, $data['html_string']);
			Mail::send('emails.customerEmail', array('company' => Context::get(), 'data' => $data), function($message) use ($data) {
				$message->to($customer_email)
				->subject($data['subject'])
				->from(Context::get()->email)
				->setBody($html_string, 'text/html');
			});
		}

		return Response::json( array('status' => '<b>OK</b> Campaign created and emails sent', 'id' => $campaign->id, 'emails' => $customers['email']), 201 ); // 201 Created

	}
}