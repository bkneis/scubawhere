<?php

class FirstTest extends PHPUnit_Framework_TestCase {
	
	/** @test */
	public function test_it_should_pass()
	{
		$test = DB::table('addons')->select('id');
		$this->assertTrue(true);
	}

}
