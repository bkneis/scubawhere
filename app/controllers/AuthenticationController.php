<?php

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Exceptions\Http\HttpPreconditionFailed;

class AuthenticationController extends Controller {

	public function postLogin()
	{
		$credentials = Input::only('username', 'password');
		$rememberMe = Input::get('remember');

		if(Auth::attempt($credentials, $rememberMe)) {
			// Login successfull!
			$user = Auth::user();
			Context::set(Auth::user()->company);

			$this->isCompanyVerified();
			$this->hasTrialExpired();
			
			$this->checkRehash();
			// TODO Regenerate token (maybe even on every new POST request?)

			// Update the updated_at timestamp in the table at each login
			if(!$user->touch()) {
				$user->updateUniques();
			}
			
			return Response::json( array('status' => 'Login successful. Welcome!'), 202 ); // 202 Accepted
		}
		else {
			return Response::json( array('errors' => array('Oops, something wasn\'t correct.')), 401 ); // 401 Unauthorized
		}
	}

	public function isCompanyVerified()
	{
		// Check if assigned company is verified
		if(Context::get()->verified == false) {
			Auth::logout();
			throw new HttpPreconditionFailed(__CLASS__.__METHOD__, ['Your account is on the waiting list.']);
		}
	}

	public function checkRehash()
	{
		// Check to see if the password needs to be re-hashed (when the hash technique is different from when originally saved)
		if ( Hash::needsRehash($user->password) ) {
			$user->password = Hash::make( Input::get('password') );
			$user->updateUniques();
		}
	}

	public function hasTrialExpired()
	{
		$credit_info = Context::get()->credits()->first();

		if(isset($credit_info->renewal_date))
		{
			if(Helper::convertToLocalTime($credit_info->renewal_date) < Helper::localtime())
			{
				Auth::logout();
				throw new HttpPreconditionFailed(__CLASS__.__METHOD__,
					['Your licence has expired. Please contact suport@scubawhere.com to renew your licence. Thank you.']
				);
			}
		}
		else
		{
			if(new DateTime($credit_info->trial_date) < new DateTime('now'))
			{
				Auth::logout();
				throw new HttpPreconditionFailed(__CLASS__.__METHOD__, 
					['Your trial has expired. Please contact support@scubawhere.com to renew your licence. Thank you.']);
			}
		}
	}

	public function getLogout()
	{
		Auth::logout();
		return Redirect::to('/');
	}

}
