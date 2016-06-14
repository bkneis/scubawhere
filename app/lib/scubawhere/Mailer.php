<?php namespace ScubaWhere;

interface CrmMailerInterface
{
	public static function send($subject, $customer, $from, $email_html);

	public static function sendBookingConf($booking);

	public static function sendTransactionConf($booking);

	public static function sendRegisterConf($user);
}

class CrmMailer implements CrmMailerInterface
{
	public static function send($subject, $customer, $from, $email_html)
	{
		Mail::send([], [], function($message) use ($subject, $customer, $from, $email_html) 
		{
			$message->to($customer->email, $customer->name)
			->subject($subject)
			->from($from)
			->setBody($email_html, 'text/html');
		});
	}

	public static function sendBookingConf($booking)
	{
		Mail::queue('emails.booking-summary', array('booking' => $booking, 'company' => $company), function($message) use ($booking, $company)
		{
			$message->to($booking->lead_customer->email, $booking->lead_customer->name)
			->subject('Booking Confirmation with ' . $company->name)
			->from($company->email);
			// TODO add to register controller to upload a DO's terms and conditions to this address then it can be added to emails
			//$message->attach(storage_path() . 'terms/' . $company->name . '.pdf');
		});
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
