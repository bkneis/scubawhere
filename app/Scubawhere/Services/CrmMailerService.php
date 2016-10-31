<?php 

namespace Scubawhere\Services;

class CrmMailer
{
	/*protected $booking_repo;

	public function __construct(BookingRepoInterface $booking_repo) 
	{
		$this->booking_repo = $booking_repo;
	}*/

	public static function sendBookingConf($booking_id)
	{
		# 1. Get boooking
		/*$booking = $this->booking_repo->get($booking_id);

		# 2. Generate email HTML
		$html = \View::make('emails.booking-summary', ['company' => Context::get(), 'booking' => $booking, 'siteUrl' => \Config::get('app.url')])->render();

		$data = array(
			'subject'          => Context::get()->name . ' Booking Itinerary',
			'email_html'       => $html,
			'name'             => 'Booking Itinerary for ' . $booking->reference,
			'sendallcustomers' => 0,
			'is_campaign'      => 0
		);

		$customer_id = $booking->lead_customer_id;

		return $this->crm_campaign_service->create($data, null, $customer_id);*/
	}

	public static function sendReservationConf($booking_id)
	{
		# 1. Get booking information
		\Request::replace(["id" => $booking_id]);

		$app        = app();
		$controller = $app->make('BookingController');
		$booking    = $controller->callAction('getIndex', []);

		# 2. Generate email HTML
		$html = \View::make('emails.booking-reservation', 
			['company' => Context::get(), 'booking' => $booking])
			->render();

		# 3. Send email via CrmCampaignController
		\Request::replace([
			'subject'          => Context::get()->name . ' Booking Reservation',
			'email_html'       => $html,
			'name'             => 'Booking Reservation for ' . $booking->reference,
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
