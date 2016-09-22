<?php namespace ScubaWhere;

interface CrmMailerInterface
{
	/**
	 * Function to use when sending emails of any type.
	 * Please Note : Only the CrmCampaign Controller should reference this function, all others should us the specific send* function so that the email is saved in the DB.
	 * @param $campaign The Campaign model that was created in CrmCampaign Controller after being validated
	 * @param $customer The Customer model who should receive the email
	 * @return void
     */
	public static function send($campaign, $customer, $email_html);

	public static function sendBookingConf($booking_id);

	public static function sendTransactionConf($payment_id);

	public static function sendRefundConf($refund_obj);

	public static function sendRegisterConf($user);
}

class CrmMailer implements CrmMailerInterface
{
	public static function send($campaign, $customer, $email_html)
	{
		// We need to assign variables required for the mail closure as the eloquent models aren't serializable (which is required of queuing)
		$company = Context::get();
		$terms_file = storage_path() . '/scubawhere/' . $company->name . '/terms.pdf';
		$email_to = $customer->email;
		$name_to = $customer->name;
		$subject = $campaign->subject;
		$email_from = $company->business_email;
		$name_from = $company->name;
		//$email_html = $campaign->email_html;
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

	public static function sendTransactionConf($payment_obj)
	{
		# 1. Get booking information
		\Request::replace(["id" => $payment_obj->id]);

		$app = app();
		$controller = $app->make('PaymentController');
		$payment    = $controller->callAction('getIndex', []);
		$booking = $payment->booking;

		# 2. Generate email HTML
		$html = \View::make('emails.transaction', ['company' => Context::get(), 'payment' => $payment])->render();

		# 3. Send email via CrmCampaignController
		\Request::replace([
			'subject'          => Context::get()->name . ' Transaction confirmation',
			'email_html'       => $html,
			'name'             => 'Transaction Confirmation for ' . $booking->reference,
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

	public static function sendRefundConf($refund_obj)
	{
		# 1. Get booking information
		\Request::replace(["id" => $refund_obj->id]);

		$app = app();
		$controller = $app->make('RefundController');
		$refund     = $controller->callAction('getIndex', []);
		$booking    = $refund->booking;

		# 2. Generate email HTML
		$html = \View::make('emails.refund', ['company' => Context::get(), 'refund' => $refund])->render();

		# 3. Send email via CrmCampaignController
		\Request::replace([
			'subject'          => Context::get()->name . ' Refund confirmation',
			'email_html'       => $html,
			'name'             => 'Refund Confirmation for ' . $booking->reference,
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

	public static function sendRegisterConf($user)
	{
		$email = $user->email;
		$name = $user->name;
		Mail::queue('password.reset', array('email' => $user->email), function($message) use ($email, $name)
		{
			$message->to($email, $name)
			->subject('Welcome to scubawhere ' . $name)
			->from('no-reply@scubawhere.com');
			// @todo add here an attachment of scubawhere terms.pdf
		});
	}
}
