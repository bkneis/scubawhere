<?php namespace ScubaWhere;

use ScubaWhere\Context;

class Mailer 
{
	public function send($subject, $body, $customer)
	{
		Mail::send([], [], function($message) use ($data, $customer, $email_html) 
		{
			$message->to($customer->email, $customer->name)
			->subject($subject)
			->from(Context::get()->business_email)
			->setBody($email_html, 'text/html');
		});
	}

	public function sendBookingConf($booking)
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

	public function sendTransactionConf($booking)
	{
		Mail::queue('emails.transaction', array('booking' => $booking, 'company' => Context::get()), function($message) use ($booking)
		{
			$message->to($booking->lead_customer->email, $user->lead_customer->name)
			->subject('Transaction confirmation with ' . Context::get()->name)
			->from('no-reply@scubawhere.com');
		});
	}

	public function sendRegisterConf($user)
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
