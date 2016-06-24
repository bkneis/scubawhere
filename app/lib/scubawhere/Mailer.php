<?php namespace ScubaWhere;

interface CrmMailerInterface
{
	public static function send($campaign, $customer);

	public static function sendBookingConf($booking_id);

	public static function sendTransactionConf($booking);

	public static function sendRegisterConf($user);
}

class CrmMailer implements CrmMailerInterface
{
	public static function send($campaign, $customer)
	{
		$company = Context::get();
		$terms_file = storage_path() . '/scubawhere/' . $company->name . '/terms.pdf';
		\Mail::send([], [], function($message) use ($campaign, $customer, $company, $terms_file)
		{
			$message->to($customer->email, $customer->name)
			->subject($campaign->subject)
			->from($company->email)
			->setBody($campaign->email_html, 'text/html');
			if($campaign->is_campaign == 0 && file_exists($terms_file))
			{
				$message->attach($terms_file, array(
					'as' => 'terms.pdf',
					'mime' => 'application/pdf')
				);
			}
		});
	}

	public static function sendBookingConf($booking_id)
	{
		# 1. Get booking information
		\Request::replace(["id" => $booking_id]);

		$app        = app();
		$controller = $app->make('BookingController');
		$booking    = $controller->callAction('getIndex', []);

		# 2. Generate email HTML
		$html = \View::make('emails.booking-summary', ['company' => Context::get(), 'booking' => $booking, 'siteUrl' => \Config::get('app.url')])->render();

		# 3. Send email via CrmCampaignController
		\Request::replace([
			'subject'          => Context::get()->name . ' Booking Itinerary',
			'email_html'       => $html,
			'name'             => 'Booking Itinerary for ' . $booking->reference,
			'sendallcustomers' => 0,
			'is_campaign'      => 0,
			'customer_id'      => $booking->lead_customer_id,
		]);

		$controller = $app->make('CrmCampaignController');
		$request    = $controller->callAction('postAdd', []);

		# 4. Check if request was successful
		if($request->getStatusCode() !== 201)
		{
			$json = json_decode($request->getContent());
			throw new \Exception('Email Sending Error: ' . $json->errors, 1);
		}

		return $request;

	}

	public static function sendTransactionConf($booking)
	{
		Mail::queue('emails.transaction', array('booking' => $booking, 'company' => Context::get()), function($message) use ($booking)
		{
			$message->to($booking->lead_customer->email, $user->lead_customer->name)
			->subject('Transaction confirmation with ' . Context::get()->name)
			->from('no-reply@scubawhere.com');
		});
	}

	public static function sendRegisterConf($user)
	{
		// TODO move the blade template to emails and rename it
		Mail::queue('reset', array('email' => $user->email), function($message) use ($user)
		{
			$message->to($user->email, $user->name)
			->subject('Welcome to scubawhere ' . $user->name)
			->from('no-reply@scubawhere.com');
		});
	}
}
