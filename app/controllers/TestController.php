<?php

use Scubawhere\Context;

class TestController extends Controller
{
    public function getEmail()
    {
        $b = DB::table('bookings')->where('reference', Input::only('reference'))->lists('id');

        if (empty($b)) {
            throw new Exception('Unknown booking reference');
        }

        Request::replace(['id' => $b[0]]);

        $app = app();
        $controller = $app->make('BookingController');
        $booking = $controller->callAction('getIndex', []);

        return View::make('emails.booking-summary', ['company' => Context::get(), 'booking' => $booking, 'siteUrl' => Config::get('app.url')]);
    }

    /*public function getTime()
    {
        phpinfo();
        dd(date('Y-m-d H:i:s T'));
    }*/

    /*
    public function getRegister()
    {
        return View::make('test.register');
    }

    public function getLogin()
    {
        if(Auth::check()) return "You are already logged in!";

        return View::make('test.login');
    }

    public function postTest()
    {
        $data = Input::only('boats', 'accommodations');

        return View::make('test.dump', array('input' => $data));
    }

    public function getForm()
    {
        return View::make('test.form');
    }

    public function postForm()
    {
        $schedule = Input::get('schedule');
        var_dump($schedule);
        echo count($schedule[1]);
    }

    public function getPurifier()
    {
        return View::make('test.purifier');
    }

    public function postPurifier()
    {
        return Purifier::clean( Input::get('description') );
    }
    */
}
