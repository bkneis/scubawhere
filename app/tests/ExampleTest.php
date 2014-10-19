<?php

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		//Example test to ensure the home page is currently redirecting
		$response = $this->client->request('GET', '/');
		$this->assertResponseStatus(302);
	}

}