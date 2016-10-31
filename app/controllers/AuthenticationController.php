<?php

use Scubawhere\Helper;
use Scubawhere\Context;

class AuthenticationController extends Controller {

	public function postLogin()
	{
		$credentials = Input::only('username', 'password', 'remember');

		if(Auth::attempt(
			array(
				'username' => Input::get('username'),
				'password' => Input::get('password'),
			), Input::get('remember') ))
		{
			// Login successfull!
			$user = Auth::user();
			Context::set(Auth::user()->company);

			// Check if assigned company is verified
			if(Context::get()->verified == false) {

				Auth::logout();

				return Response::json( array('errors' => array('Your account is on the waiting list.<br><br>Please <a href="mailto:hello@scubawhere.com?subject=Please verify my account&body=Hello Team scubawhere!%0A%0APlease verify my new RMS account.%0AMy username is: '.$user->username.'. %0A%0AThank you!">contact us</a> to accelerate your verification.')), 406 ); // 406 Not Acceptable
			}

			$credit_info = Context::get()->credits()->first();

			if(isset($credit_info->renewal_date))
			{
				if(Helper::convertToLocalTime($credit_info->renewal_date) < Helper::localtime())
				{
					Auth::logout();
					return Response::json(
								array('errors' => 
									array('Your licence has expired. Please contact support@scubawhere.com to renew your licence. Thank you.')
								), 406);
				}
			}
			else
			{
				if(new DateTime($credit_info->trial_date) < new DateTime('now'))
				{
					Auth::logout();
					return Response::json(
								array('errors' => 
									array('Your trial has expired. Please contact support@scubawhere.com to renew your licence. Thank you.')
								), 406);
				}
			}

			// TODO Regenerate token (maybe even on every new POST request?)

			// Check to see if the password needs to be re-hashed (when the hash technique is different from when originally saved)
			if ( Hash::needsRehash($user->password) ) {
				$user->password = Hash::make( Input::get('password') );
				$user->updateUniques();
			}

			// Update the updated_at timestamp in the table at each login
			if(!$user->touch())
				$user->updateUniques();

			return Response::json( array('status' => 'Login successfull. Welcome!'), 202 ); // 202 Accepted
		}
		else
		{
			return Response::json( array('errors' => array('Oops, something wasn\'t correct.')), 401 ); // 401 Unauthorized
		}
	}

	public function getTest()
	{
		//return $_SERVER['SERVER_NAME'];
		try{
			$db = DB::connection()->getDatabaseName();
		}catch(Exception $e){
			//return $e->getMessage();
			return array('host' => $_SERVER['RDS_HOSTNAME'], 'e' => $e->getMessage(), 'env' => App::environment());
		}
		return array('host' => RDS_HOSTNAME, 'user' => RDS_USERNAME, 'pass' => RDS_DB_NAME, 'db' => $db, 'env' => App::environment());
		//return $db;
	}

	public function getLogout()
	{
		Auth::logout();

		return Response::json( array('status' => 'Successfully logged out. See you soon!') );
	}

}
