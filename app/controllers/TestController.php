<?php

class TestController extends Controller {

	/*
	public function getRegister()
	{
		return View::make('test.register');
	}

	public function getLogin()
	{
		if(Auth::check()) return "You are allready logged in!";

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
