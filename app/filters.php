<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	/*DB::listen(function($sql, $bindings, $time)
	{
		Log::info(Request::path()." SQL: ".$sql." Bindings: ".implode(', ', $bindings));
	});*/
});

Route::matched(function($route, $request)
{
	if(!Auth::guest())
		ScubaWhere\Context::set(Auth::user()->company);
});

App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Response::json( array('errors' => array('Authorisation required. Please log in.')), 401 ); // 401 Unauthorized
});


Route::filter('auth.basic', function()
{
	return Auth::basic('username');
});

Route::filter('auth.admin', function()
{
	if(\ScubaWhere\Context::get()->name !== 'admin') return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if ( Request::method() === 'POST' && !Symfony\Component\Security\Core\Util\StringUtils::equals(Session::token(), Input::get('_token')) )
	{
		// throw new Illuminate\Session\TokenMismatchException;

		$message = 'The CSRF token is ' . (!Input::has('_token') ? 'missing' : 'not valid') . '!';

		return Response::json(['errors' => ['TokenMismatchException: ' . $message]], 401); // 401 Unauthorized
	}
});
