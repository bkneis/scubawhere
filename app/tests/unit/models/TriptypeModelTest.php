<?php

class TriptypeModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function testCRUD(){
		
		//Create/Read
		$triptype_id = ModelTestHelper::createTriptype();
		$triptype = Triptype::find($triptype_id);
		
		$this->assertNotEquals(0, $triptype->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $triptype->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $triptype->description, "Unexpected description value");
		$this->assertNotEquals("0000-00-00 00:00:00", $triptype->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $triptype->updated_at);
				
		//Update
		$triptype->name = ModelTestHelper::TEST_STRING_UPDATED;
		$triptype->description = ModelTestHelper::TEST_STRING_UPDATED;
		$triptype->save();		
		$triptype = Triptype::find($triptype_id);
		
		$this->assertNotEquals(0, $triptype->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $triptype->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $triptype->description, "Unexpected description value");
				
		//Delete
		$triptype->delete();
		$triptype = Triptype::find($triptype_id);
		
		$this->assertNull($triptype, "Triptype not deleted");
	}
	
	public function testValidation(){
		$this->assertTrue(true);
	}
	
	public function testRelationships(){
		$this->assertTrue(true);
	}
	
	public function testFunctions(){
		$this->assertTrue(true);
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
