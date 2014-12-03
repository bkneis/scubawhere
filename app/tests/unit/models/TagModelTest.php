<?php

class TagModelTest extends ModelTestCase {

	public function setUp()
	{
		parent::setUp();
	}

	public function testCRUD(){

		//Create/Read
		$tag_id = ModelTestHelper::createTag();
		$tag = Tag::find($tag_id);

		$this->assertNotEquals(0, $tag->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $tag->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $tag->description, "Unexpected description value");
		$this->assertNotEquals("0000-00-00 00:00:00", $tag->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $tag->updated_at);

		//Update
		$tag->name = ModelTestHelper::TEST_STRING_UPDATED;
		$tag->description = ModelTestHelper::TEST_STRING_UPDATED;
		$tag->save();
		$tag = Tag::find($tag_id);

		$this->assertNotEquals(0, $tag->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $tag->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $tag->description, "Unexpected description value");

		//Delete
		$tag->delete();
		$tag = Tag::find($tag_id);

		$this->assertNull($tag, "Tag not deleted");
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
