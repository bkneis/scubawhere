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

    public function postDelete()
    {
        $campaign = Context::get()->campaigns()->findOrFail( Input::get('id') );
        $campaign->delete();
        return Response::json(array('status' => 'Campaign has been deleted'));
    }

    public function postRestore()
    {
        $campaign = Context::get()->campaigns()->findOrFail( Input::get('id') );
        $campaign->restore();
        return Response::json(array('status' => 'Campaign has been restored'));
    }

    public function getAnalytics()
    {
        $campaign_id = Input::only('id');
        $analytics = CrmToken::where('campaign_id', '=', $campaign_id)->with('customer.crmSubscription')->get();
        $total_sent = sizeof($analytics);
        $total_seen = 0;
        foreach ($analytics as $token)
        {
            if($token->opened > 0) $total_seen += 1;
        }
        // WOULD GETTING THE CRM_SUBSCRIPTIONS FROM CAMPAIGN ID BE MORE EFFECIENT THAN LOOPING THROUGH EXISITING DATA ???????
        $unsubscriptions = 0;
        foreach($analytics as $analytic)
        {
            if($analytic->customer->crm_subscription->subscribed == 0) $unsubscriptions ++;
        }

        $campaign_links = CrmLink::with('analytics')->where('campaign_id', '=', $campaign_id)->get();
        $links_clicked = 0;
        foreach($campaign_links as $link)
        {
            foreach($link->analytics as $analytic)
            {
                $links_clicked += $analytic->count;
            }
        }

        return Response::json(array('analytics' => $analytics, 'total_sent' => $total_sent, 'total_seen' => $total_seen, 'link_analytics' => $campaign_links, 'num_links_clicked' => $links_clicked, 'num_unsubscriptions' => $unsubscriptions), 200);
    }

	public function postAdd()
	{
		$data = Input::only(
			'subject',
			'email_html',
            'name',
            'sendallcustomers',
            'is_campaign'
		);

		$data['sent_at'] = Helper::localTime();

		$group_ids = Input::only('groups')['groups'];

        $customers = [];

        if((int) $data['sendallcustomers'] == 1)
        {
            $customers = Context::get()->customers()->get();
        }
        else if((int) $data['is_campaign'] == 0)
        {
            $booked_cust_id = Input::get('customer_id');
            $booked_cust = Context::get()->customers()->findOrFail($booked_cust_id); // possibly use where in?
            if(!$booked_cust)
            {
                return Response::json( 'Customer ID is not valid', 406 );
            }
            array_push($customers, $booked_cust);
        }
        else
        {
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
        }

		$data['num_sent'] = sizeof($customers);
        unset($data['customer_id']);

		$campaign = new CrmCampaign($data);

		if( !$campaign->validate() )
		{
			return Response::json( array('errors' => $campaign->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$campaign = Context::get()->campaigns()->save($campaign);

        if((int) $data['sendallcustomers'] == 0 && (int) $data['is_campaign'] == 1)
        {
            $campaign->groups()->sync($group_ids);
        }

        // find all instances of a link and create them
        $hrefs = array();
        $dom = new DOMDocument();
        $dom->loadHTML($data['email_html']);
        $tags = $dom->getElementsByTagName('a');
        foreach ($tags as $tag) {
            if($tag->getAttribute('href') != "{{unsubscribe_link}}")
            {   $link_data = [];
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

            // Create customer subscriptions
            $customer_subscription = CrmSubscription::where('customer_id', '=', $customer->id)->first();
            if(is_null($customer_subscription))
            {
                $subscription_data = [];
                $subscription_data['customer_id'] = $customer->id;
                $subscription_data['token'] = str_random(50);

                $customer_subscription = new CrmSubscription($subscription_data);
                if( !$customer_subscription->validate() )
                {
                    return Response::json( array('errors' => $customer_subscription->errors()->all()), 406 ); // 406 Not Acceptable
                }
                $customer_subscription->save();
            }

            // add the token api to the scubawhere image
            $token_api = Request::root() . '/crm_tracking/scubaimage?campaign_id=' . $campaign->id . '&customer_id=' . $customer->id . '&token=' . $new_token->token;
            $email_html = str_replace('/img/scubawhere_logo.png', $token_api, $data['email_html']);

			$cust_name = $customer->firstname . ' ' . $customer->lastname;
			$email_html = str_replace('{{name}}', $cust_name, $email_html);
            $email_html = str_replace('{{last_dive}}', $customer->last_dive, $email_html);
            $email_html = str_replace('{{number_of_dives}}', $customer->number_of_dives, $email_html);
            $email_html = str_replace('{{birthday}}', $customer->birthday, $email_html);

            $unsubscribe_link = Request::root() . '/crm_tracking/unsubscribe?customer_id=' . $customer->id . '&campaign_id=' . $campaign->id . '&token=' . $customer_subscription->token;
            $email_html = str_replace('{{unsubscribe_link}}', $unsubscribe_link, $email_html);

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

    /*
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

            // Create customer subscription
            $cust_subscription = Context::get()->crmSubscriptions()->where('customer_id', '=', $customer->id)->get();
            if(!$cust_subscription)
            {
                $subscription_data = [];
                $subscription_data['customer_id'] = $customer->id;
                $subscription_data['token'] = str_random(50);

                $customer_subscription = new CrmSubscription($subscription_data);
                $customer_subscription = Context::get()->campaign_subscriptions()->save($customer_subscription);
            }

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
    */

}
