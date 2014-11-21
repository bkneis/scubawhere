<?php

class ModelRelationshipsTest extends TestCase {

	public function setUp()
	{
		parent::setUp();
	}

	public function createRelationships(){
		
	}
	
	public function testHasMany(){
		$this->createRelationships();
	}
	
	
	public function testBelongsToMany(){
		$this->createRelationships();
	}	

}
