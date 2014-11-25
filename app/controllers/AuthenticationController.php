<?php

class AuthenticationController extends Controller {

	public function postLogin()
	{
		$credentials = Input::only('username', 'password', 'remember');

		if(Auth::attempt(
			array(
				'username' => Input::get('username'),
				'password' => Input::get('password'),
				'verified' => true
			), Input::get('remember') ))
		{
			// Login successfull!
			$user = Auth::user();

			// TODO Regenerate token (maybe even on every new POST request?)

			// Check to see if the password needs to be re-hashed (when the hash technique is different from when originally saved)
			if ( Hash::needsRehash($user->password) ) {
				$user->password = Hash::make( Input::get('password') );
				$user->updateUniques();
			}

			// Update the updated_at timestamp in the table at each login
			$user->touch();

			return Response::json( array('status' => 'Login successfull. Welcome!'), 202 ); // 202 Accepted
		}
		else
		{
			try
			{
				$company = Company::where('username', Input::get('username'))->first();
			}
			catch(Exception $e)
			{
				$company = false;
			}
			if( $company && $company->verified == 0)
				return Response::json( array('errors' => array('Your account is on the waiting list.<br><br>Please <a href="mailto:hello@scubawhere.com?subject=Please verify my account&body=Hello Team Scubawhere!%0A%0APlease verify my account.%0AMy username is: '.$company->username.'. %0A%0AThank you!">contact us</a> to accelerate your verification.')), 406 ); // 406 Not Acceptable

			return Response::json( array('errors' => array('Oops, something wasn\'t correct.')), 401 ); // 401 Unauthorized
		}
	}

	public function getLogout()
	{
		Auth::logout();

		return Response::json( array('status' => 'Successfully logged out. See you soon!') );
	}

}
