<?php

namespace ScubaWhere\Services;

use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use ScubaWhere\Services\ObjectStoreService;
use ScubaWhere\Exceptions\ConflictException;
use ScubaWhere\Exceptions\BadRequestException;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\CrmLinkRepoInterface;
use ScubaWhere\Repositories\CrmTokenRepoInterface;
use ScubaWhere\Repositories\CustomerRepoInterface;
use ScubaWhere\Repositories\CrmCampaignRepoInterface;

class CrmCampaignService {

	/** 
	 *	Repository to access the crmcampaign models
	 *	@var \ScubaWhere\Repositories\CrmCampaignRepo
	 */
	protected $crm_campaign_repo;

	/** 
	 *	Repository to access the CrmLink models
	 *	@var \ScubaWhere\Repositories\CrmLinkRepo
	 */
	protected $crm_link_repo;

	/** 
	 *	Repository to access the CrmToken models
	 *	@var \ScubaWhere\Repositories\CrmTokenRepo
	 */
	protected $crm_token_repo;

	/** 
	 *	Repository to access the Customer models
	 *	@var \ScubaWhere\Repositories\CustomerRepo
	 */
	protected $customer_repo;

	/**
	 * Service used to log issues to trouble shooting when waterfall deleting
	 * @var \ScubaWhere\Services\LogService
	 */
	protected $log_service;

	/**
	 * @param CrmCampaignRepoInterface   Injected using \ScubaWhere\Repositories\CrmCampaignRepoServiceProvider
	 * @param LogService                 Injected using laravel's IOC container
	 * @param CrmLinkRepoInterface       Injected using \ScubaWhere\Repositories\CrmLinkRepoServiceProvider
	 * @param ObjectStoreService         Injected using \ScubaWhere\Services\ObjectStoreServiceProvider
	 * @param CrmTokenRepoInterface      Injected using \ScubaWhere\Repositories\CrmTokenRepoServiceProvider
	 */
	public function __construct(CrmCampaignRepoInterface $crm_campaign_repo,
								LogService $log_service,
								CrmLinkRepoInterface $crm_link_repo,
								ObjectStoreService $object_store_service,
								CrmTokenRepoInterface $crm_token_repo,
								CustomerRepoInterface $customer_repo) 
	{
		$this->crm_campaign_repo    = $crm_campaign_repo;
		$this->log_service          = $log_service;
		$this->crm_link_repo        = $crm_link_repo;
		$this->object_store_service = $object_store_service;
		$this->crm_token_repo       = $crm_token_repo;
		$this->customer_repo        = $customer_repo;
	}

	/**
     * Get an crmcampaign for a company from its id
     * @param int ID of the CrmCampaign
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \CrmCampaign
     */
	public function get($id) {
		return $this->crm_campaign_repo->get($id);
	}

	/**
     * Get all crmcampaigns for a company
     * @param int ID of the crmcampaign
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAll() {
		return $this->crm_campaign_repo->all();
	}

	/**
     * Get all crmcampaigns for a company including soft deleted models
     * @param int ID of the crmcampaign
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function getAllWithTrashed() {
		return $this->crm_campaign_repo->allWithTrashed();
	}

	public function getAnalytics($campaign_id) 
	{
        $analytics = \CrmToken::where('campaign_id', '=', $campaign_id)->with('customer.crmSubscription')->get();
        $total_sent = sizeof($analytics);
        $total_seen = 0;
        foreach ($analytics as $token) {
            if ($token->opened > 0) {
                $total_seen += 1;
            }
        }
        $unsubscriptions = 0;
        foreach ($analytics as $analytic) {
            if ($analytic->customer->crm_subscription->subscribed == 0) {
                $unsubscriptions ++;
            }
        }

        $campaign_links = \CrmLink::with('analytics')->where('campaign_id', '=', $campaign_id)->get();
        $links_clicked = 0;
        foreach ($campaign_links as $link) {
            foreach ($link->analytics as $analytic) {
                $links_clicked += $analytic->count;
            }
        }

        return array('analytics' => $analytics, 'total_sent' => $total_sent, 'total_seen' => $total_seen, 'link_analytics' => $campaign_links, 'num_links_clicked' => $links_clicked, 'num_unsubscriptions' => $unsubscriptions);
	}

	public function generateRules($group_ids) 
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
                if ($rule->agency_id !== null) {
                	// @todo move this to agency repo
                    $agency = \Agency::with('certificates')->findOrFail($rule->agency_id);
                    $certs = $agency->certificates;
                    foreach ($certs as $cert) {
                        array_push($rules['certs'], $cert->id); // ->id
                    }
                } elseif ($rule->certificate_id !== null) {
                    array_push($rules['certs'], $rule->certificate_id);
                } elseif ($rule->training_id !== null) {
                    array_push($rules['classes'], $rule->training_id);
                } elseif ($rule->ticket_id !== null) {
                    array_push($rules['tickets'], $rule->ticket_id);
                }
            }
        }
        return $rules;
	}

	private function generateLinks($email_html)
	{
		// find all instances of a link and create them
        $hrefs = array();
        $dom = new \DOMDocument();
        $dom->loadHTML($email_html);
        $tags = $dom->getElementsByTagName('a');
        foreach ($tags as $tag) {
            if ($tag->getAttribute('href') != "{{unsubscribe_link}}") {
                $link_data = [];
                $hrefs[$tag->getAttribute('href')] = 0;
                $link_data['campaign_id'] = $campaign->id;
                $link_data['link'] = $tag->getAttribute('href');
                $link = $this->crm_link_repo->create($link_data);
                $hrefs[$tag->getAttribute('href')] = $link->id;
            }
        }
        return $hrefs;
	}

	private function downloadTermsFile()
	{
		try 
		{
			$terms_url = $this->object_store_service->getTermsUrl();
			$file_path = storage_path() . '/scubawhere/' . Context::get()->name . '-terms.pdf';
			file_put_contents($file_path, fopen($terms_url, 'r'));
		}
		catch(Exception $e) {}
	}

	private function personaliseEmail($customer, $email_html)
	{
		$cust_name  = $customer->firstname . ' ' . $customer->lastname;
        $email_html = str_replace('{{name}}', $cust_name, $email_html);
        $email_html = str_replace('{{last_dive}}', $customer->last_dive, $email_html);
        $email_html = str_replace('{{number_of_dives}}', $customer->number_of_dives, $email_html);
        $email_html = str_replace('{{birthday}}', $customer->birthday, $email_html);
        return $email_html;
	}

	private function attachTrackingLinks($email_html, $campaign_id, $customer_id, $token, $subscription_token, $hrefs)
	{
		$url = \Request::root();

		$token_api = $url . '/crm_tracking/scubaimage?campaign_id=' . $campaign_id . '&customer_id=' . $customer_id . '&token=' . $token;
        $email_html = str_replace('{{tracking_link}}', $token_api, $email_html);

        $unsubscribe_link = $url . '/crm_tracking/unsubscribe?customer_id=' . $customer_id . '&campaign_id=' . $campaign_id . '&token=' . $subscription_token;
        $email_html = str_replace('{{unsubscribe_link}}', $unsubscribe_link, $email_html);

        $link_tracker_data = array();

        foreach ($hrefs as $link => $link_id) 
        {
            $link_tracker_data['customer_id'] = $customer_id;
            $link_tracker_data['link_id'] = $link_id;
            $link_tracker_data['token'] = str_random(50);
            $link_tracker = $this->crm_link_tracker_repo->create($link_tracker_data);
            $email_html = str_replace($link, $url . '/crm_tracking/link?customer_id=' . $customer_id . '&link_id=' . $link_id . '&token=' . $link_tracker->token, $email_html);
        }
        return $email_html;
	}

	private function send($campaign, $customer, $email_html)
	{
		$company = Context::get();
		$terms_file = storage_path() . '/scubawhere/' . $company->name . '-terms.pdf';
		$email_to = $customer->email;
		$name_to = $customer->name;
		$subject = $campaign->subject;
		$email_from = $company->business_email;
		$name_from = $company->name;
		$is_campaign = $campaign->is_campaign;

		\Mail::queue([], [], function($message) use ($email_to, $name_to, $name_from, $subject, $email_from, $email_html, $is_campaign, $terms_file)
		{
			$message->to($email_to, $name_to)
			->subject($subject)
			->from($email_from, $name_from)
			->setBody($email_html, 'text/html');
			if($is_campaign == 0 && file_exists($terms_file))
			{
				$message->attach($terms_file, array(
					'as' => 'terms.pdf',
					'mime' => 'application/pdf')
				);
			}
		});	
	}

	/**
	 * Validate, create and save the crmcampaign and prices to the database
	 * @param  array Data to autofill crmcampaign model
	 * @return \CrmCampaign
	 */
	public function create($data, $group_ids, $customer_id) 
	{
		/**
		 * 1. Check that either a group, customer or send all flag is specified
		 * 2. Retrieve the customers to send to
		 * 3. Create the campaign
		 * 4. Extract all links from the html of the email
		 * 5. Download the DO's terms file to the local server if it exists
		 * 6. Loop through all customers and attach the tracking api to all links and image
		 * 7. Push the request for sending the email to the customer onto the queue
		 */
        
		// STEP 1.
        if (count($group_ids) < 1 && (int) $data['sendallcustomers'] !== 1 && $data['is_campaign'] !== 0) {
        	throw new InvalidInputException(['Please specify atleast one group to send the email too']);
        }

        // STEP 2.
        $customers = [];
		if ((int) $data['sendallcustomers'] == 1) 
		{
			$customers = $this->customer_repo->getAllWithEmail();
		} 
		elseif ((int) $data['is_campaign'] == 0) 
		{
			$booked_cust = $this->customer_repo->get($customer_id);
            array_push($customers, $booked_cust);
		} 
		else 
		{
            $rules                  = $this->generateRules($group_ids);
            $certificates_customers = $this->customer_repo->getCustomersByCertification($rules['certs']);

            // @todo Move this to booking details repo
			$booked_customer_ids = Context::get()->bookingdetails()
				->whereHas('booking', function ($query) use ($rules) {
                	$query->whereIn('status', \Booking::$counted); 
				})
				->whereIn('ticket_id', $rules['tickets'])
				->orWhereIn('training_id', $rules['classes'])
				->with('customer')
				->get()
				->map(function($obj) {
					return $obj->customer->id;
				})
				->toArray();

			$booked_customers = $this->customer_repo->getCustomersByBookings($booked_customer_ids);
			$customers        = $certificates_customers->merge($booked_customers)->unique();

        }

        // STEP 3.
        $data['sent_at']  = Helper::localTime();
        $data['num_sent'] = sizeof($customers);

        $campaign = $this->crm_campaign_repo->create($data);

        if ((int) $data['sendallcustomers'] == 0 && (int) $data['is_campaign'] == 1) {
            $campaign->groups()->sync($group_ids);
        }

        // STEP 4.
        $hrefs = $this->generateLinks($data['email_html']);

        // STEP 5.
        // @note This feels more appropriate in the object store service ??
		$this->downloadTermsFile();

		// STEP 6.
		$email_html = $data['email_html'];

        foreach ($customers as $customer) 
        {
            // If the customer has unsubscribed skip them and go to the next customer
            // @todo remove this and only get customers with subscriptions
			if(isset($customer->crmSubscription->subscribed)) {
				if ($customer->crmSubscription->subscribed === 0 && (int) $data['is_campaign'] === 1) {
					continue;
				}
			}

            $new_token = $this->crm_token_repo->create($campaign->id, str_random(50), $customer->id);
       
            // Create customer subscriptions
			$subscription_data = array('token' => str_random(50));
			$customer->crmSubscription()->update($subscription_data);

			// Replace occurences of variables such as {{name}} with customer data
			$email_html = $this->personaliseEmail($customer, $email_html);

			// Replace all links with a link tracker and add unsubscribe / tracking link
			$email_html = $this->attachTrackingLinks($email_html, $campaign->id, $customer->id, $new_token->token, $subscription_data['token'], $hrefs);

			// Queue the message to be sent
            $this->send($campaign, $customer, $email_html);
        }	
        // STEP 7.
        return $campaign;
	}

	/**
	 * Remove the crmcampaign from the database.
	 * In addition delete any quotes or packages associated to it. This will fail if their are 
	 * future paid bookings associated to the crmcampaign, and the booking ids are then logged
	 * @throws \ScubaWhere\Exceptions\ConflictException
	 * @throws \Exception
	 * @param  int $id ID of the crmcampaign
	 */
	public function delete($id)
	{
		return $this->crm_campaign_repo->delete($id);	
	}

	public function restore($id) 
	{
		return $this->crm_campaign_repo->restore($id);
	}

}