<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;
use ScubaWhere\Context;

class CrmCampaignController extends Controller {

	public function getIndex() {

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->campaigns()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The campaign could not be found.')), 404 ); // 404 Not Found
		}

	}

	public function getAll()
	{
		return Context::get()->campaigns()->get();
	}

	public function getAllWithTrashed()
	{
		return Context::get()->campaigns()->withTrashed()->get();
	}

	public function postUploadImage()
	{
		$image = Input::file('uploaded-image');

		if(! Input::file('uploaded-image') )
		{
			return Response::json( array('errors' => array('Please upload a file.')), 406 ); // 406 bad request
		}

		if(! Input::file('uploaded-image')->isValid() )
		{
			return Response::json( array('errors' => array('Uploaded file is not valid.')), 406 ); // 406 bad request
		}

		$savepath = public_path() . '/uploads/images/' . Context::get()->username . '/';

		if(!file_exists($savepath))
		{
			File::makeDirectory($savepath);
		}

	    $filename = $image->getClientOriginalName() . str_random(20);

	    Input::file('uploaded-image')->move($savepath, $filename);

	    $filepath = '/uploads/images/' . Context::get()->username . '/' . $filename;

	    return Response::json( array('sucess' => array('File Uploaded.'), 'filepath' => $filepath), 200 ); // 200 Success
	}

	public function postAdd()
	{
		$data = Input::only(
			'subject',
			'html_string'
		);

		$data['sent_at'] = Helper::localTime();

		$group_ids = Input::only('groups')['groups'];

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

		return Response::json( array('customers' => $customers), 200 );

		$data['num_sent'] = sizeof($customers['email']);

		$campaign = new CrmCampaign($data);

		if( !$campaign->validate() )
		{
			return Response::json( array('errors' => $campaign->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$campaign = Context::get()->campaigns()->save($campaign);

		$campaign->groups()->sync($group_ids);

		// LOOP THROUGH CUSTOMER EMAILS AND SEND THEM EMAIL

		foreach($customers as $customer)
		{
			$cust_name = $customer['firstname'] + ' ' + $customer['lastname'];
			$html_string = str_replace('{{customer_name}}', $cust_name, $data['html_string']);
			Mail::send('emails.customerEmail', array('company' => Context::get(), 'data' => $data), function($message) use ($data) {
				$message->to($customer_email)
				->subject($data['subject'])
				->from(Context::get()->email)
				->setBody($data['html_string'], 'text/html');
			});
		}

		return Response::json( array('status' => '<b>OK</b> Campaign created and emails sent', 'id' => $campaign->id, 'emails' => $customers['email']), 201 ); // 201 Created

	}

	/*
	public function postAdd()
	{
		$data = Input::only(
			'subject',
			'message',
			'html_string'
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
		})->lists('email');

		$customer_emails = array_merge($customer_emails, $certificates_customers);

		$booked_customers = Context::get()->bookingdetails()->whereHas('booking', function($query) use($rules) {
			$query->where('status', 'confirmed'); // changed confirmed to completed when soren pushes it
		})->whereIn('ticket_id', $rules['tickets']) //->orWhereIn('training_id', $rules['classes']) ADD WHEN SOREN ADDS TRAINING IS TO DETAILS
		->leftJoin('customers', 'customers.id', '=', 'booking_details.customer_id')->lists('email');

		$customer_emails = array_merge($customer_emails, $booked_customers);
		$customer_emails = array_unique($customer_emails);

		$data['num_sent'] = sizeof($customer_emails);

		$campaign = new CrmCampaign($data);

		if( !$campaign->validate() )
		{
			return Response::json( array('errors' => $campaign->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$campaign = Context::get()->campaigns()->save($campaign);

		$campaign->groups()->sync($group_ids);

		// LOOP THROUGH CUSTOMER EMAILS AND SEND THEM EMAIL

		foreach($customer_email in $customer_emails)
		{
			//$html_string = str_replace('{{customer_name}}', 'GET CUSTOMER NAME', $data['html_string'])
			Mail::send('emails.customerEmail', array('company' => Context::get(), 'data' => $data), function($message) use ($data) {
				$message->to($customer_email) // POSSIBLY ADD CUSTOMER NAME
				->subject($data['subject'])
				->from('contact@scubawhere.com') // POSSIBLY CHANGE TO USERS EMAIL
				->setBody($data['html_string'], 'text/html');
			});
		}

		return Response::json( array('status' => '<b>OK</b> Campaign created and emails sent', 'id' => $campaign->id, 'emails' => $customer_emails), 201 ); // 201 Created

		// THINGS TO ADD
		// Instead of getting customer emails, get entire customer, that way dc can put customer attributes in email, i.e. {{{customer_name}}} then find and replace that string for customer attr
		// Allow of sending email later
	}
	*/

}
