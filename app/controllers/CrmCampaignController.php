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
			return Context::get()->campaigns()->with('tokens', 'groups', 'crmLinks')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The campaign could not be found.')), 404 ); // 404 Not Found
		}

	}

	public function getAll()
	{
		return Context::get()->campaigns()->with('tokens', 'groups', 'crmLinks')->get();
	}

	public function getAllWithTrashed()
	{
		return Context::get()->campaigns()->withTrashed()->with('tokens', 'groups', 'crmLinks')->get();
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

		$savepath = public_path() . '/uploads/images/' . Context::get()->name . '/';

		if(!file_exists($savepath))
		{
			File::makeDirectory($savepath);
		}

	    $filename = $image->getClientOriginalName() . str_random(20);

	    Input::file('uploaded-image')->move($savepath, $filename);

	    $filepath = substr(Request::root(), 0, -4) . '/uploads/images/' . Context::get()->name . '/' . $filename;

	    return Response::json( array('sucess' => array('File Uploaded.'), 'filepath' => $filepath), 200 ); // 200 Success
	}
    
    public function getAnalytics()
    {
        $campaign_id = Input::only('id');
        $analytics = CrmToken::where('campaign_id', '=', $campaign_id)->with('customer')->get(); // maybe only get their name and email??
        $total_sent = sizeof($analytics);
        $total_seen = 0;
        foreach ($analytics as $token)
        {
            if($token->opened > 0) $total_seen += 1;
        }
        $campaign_links = CrmLink::with('analytics')->where('campaign_id', '=', $campaign_id)->get();

        return Response::json(array('analytics' => $analytics, 'total_sent' => $total_sent, 'total_seen' => $total_seen, 'link_analytics' => $campaign_links), 200);
    }

	public function postAdd()
	{
		$data = Input::only(
			'subject',
			'email_html',
            'name'
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
		})->get();//->lists('email', 'firstname', 'lastname', 'id');
        
		$customers = array_merge($customers, array($certificates_customers));

		$booked_customers = Context::get()->bookingdetails()->whereHas('booking', function($query) use($rules) {
			$query->where('status', 'confirmed'); // changed confirmed to completed when soren pushes it
		})->whereIn('ticket_id', $rules['tickets']) //->orWhereIn('training_id', $rules['classes']) ADD WHEN SOREN ADDS TRAINING IS TO DETAILS
		->leftJoin('customers', 'customers.id', '=', 'booking_details.customer_id')->get();//->lists('email', 'firstname', 'lastname', 'id');

		$customers = array_merge($customers, array($booked_customers));
		$customers = array_unique($customers);
        $customers = $customers[0]; // second index is empty array?
        
		$data['num_sent'] = sizeof($customers);

		$campaign = new CrmCampaign($data);

		if( !$campaign->validate() )
		{
			return Response::json( array('errors' => $campaign->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$campaign = Context::get()->campaigns()->save($campaign);

		$campaign->groups()->sync($group_ids);
        
        // find all instances of a link and create them
        $hrefs = array();
        $dom = new DOMDocument();
        $dom->loadHTML($data['email_html']);
        $tags = $dom->getElementsByTagName('a');
        foreach ($tags as $tag) {
            $link_data = [];
            $hrefs[$tag->getAttribute('href')] = 0;
            $link_data['campaign_id'] = $campaign->id;
            $link_data['link'] = $tag->getAttribute('href');
            $link = new CrmLink($link_data);
            if( !$link->validate() )
            {
                return Response::json( array('errors' => $link->errors()->all()), 406 ); // 406 Not Acceptable
            }
            $link->save();
            $hrefs[$tag->getAttribute('href')] = $link->id;
        }
    
		// LOOP THROUGH CUSTOMER EMAILS AND SEND THEM EMAIL

		foreach($customers as $customer)
		{
            $new_token_data = [];
            $new_token_data['campaign_id'] = $campaign->id;
            $new_token_data['token'] = str_random(50);
            $new_token_data['customer_id'] = $customer->id;
            $new_token = new CrmToken($new_token_data);
            if( !$new_token->validate() )
			{
				return Response::json( array('errors' => $new_token->errors()->all()), 406 ); // 406 Not Acceptable
			}
            $new_token->save();
            
            // add the token api to the scubawhere image
            $token_api = Request::root() . '/crm_tracking/scubaimage?campaign_id=' . $campaign->id . '&customer_id=' . $customer->id . '&token=' . $new_token->token;
            $email_html = str_replace('/img/scubawhere_logo.png', $token_api, $data['email_html']);
            
			$cust_name = $customer->firstname . ' ' . $customer->lastname;
			$email_html = str_replace('{{name}}', $cust_name, $email_html);
            $email_html = str_replace('{{last_dive}}', $customer->last_dive, $email_html);
            $email_html = str_replace('{{number_of_dives}}', $customer->number_of_dives, $email_html);
            $email_html = str_replace('{{birthday}}', $customer->birthday, $email_html);
            
            $link_tracker_data = [];
            // add link and link tracker
            foreach($hrefs as $link => $link_id)
            {
                $link_tracker_data['customer_id'] = $customer->id;
                $link_tracker_data['link_id'] = $link_id;
                $link_tracker_data['token'] = str_random(50);
                $link_tracker = new CrmLinkTracker($link_tracker_data);
                if( !$link_tracker->validate() )
                {
                    return Response::json( array('errors' => $link_tracker->errors()->all()), 406 ); // 406 Not Acceptable
                }
                $link_tracker->save();
                $email_html = str_replace($link, Request::root() . '/crm_tracking/link?customer_id=' . $customer->id . '&link_id=' . $link_id . '&token=' . $link_tracker->token, $email_html);
            }
            
			Mail::send([], [], function($message) use ($data, $customer, $email_html) {
				$message->to($customer->email)
				->subject($data['subject'])
				->from(Context::get()->business_email)
				->setBody($email_html, 'text/html');
			});
		}

		return Response::json( array('status' => '<b>OK</b> Campaign created and emails sent', 'id' => $campaign->id, 'emails' => $customers), 201 ); // 201 Created

	}

}
