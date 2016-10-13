<?php

class CertificateModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function testCRUD(){
		
		//Create/Read
		$agency_id = ModelTestHelper::createAgency();
		$certificate_id = ModelTestHelper::createCertificate($agency_id);
		$certificate = Certificate::find($certificate_id);
		
		$this->assertNotEquals(0, $certificate->id, "Unexpected id value");
		$this->assertEquals($agency_id, $certificate->agency_id, "Unexpected agency_id value");
		$this->assertEquals(ModelTestHelper::TEST_ABBR, $certificate->abbreviation, "Unexpected abbreviation value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $certificate->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $certificate->description, "Unexpected description value");
		$this->assertNotEquals("0000-00-00 00:00:00", $certificate->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $certificate->updated_at);
				
		//Update
		$certificate->abbreviation = ModelTestHelper::TEST_ABBR_UPDATED;
		$certificate->name = ModelTestHelper::TEST_STRING_UPDATED;
		$certificate->description = ModelTestHelper::TEST_STRING_UPDATED;
		$certificate->save();		
		$certificate = Certificate::find($certificate_id);
		
		$this->assertNotEquals(0, $certificate->id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_ABBR_UPDATED, $certificate->abbreviation, "Unexpected abbreviation value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $certificate->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $certificate->description, "Unexpected description value");
				
		//Delete
		$certificate->delete();
		$certificate = Certificate::find($certificate_id);
		
		$this->assertNull($certificate, "Certificate not deleted");
	}
	
	public function testValidation(){
		$this->assertTrue(true);
	}
	
	public function testRelationships(){	
		$agency_id = ModelTestHelper::createAgency();
		$certificate_id = ModelTestHelper::createCertificate($agency_id);
		$certificate = Certificate::find($certificate_id);
	
		$this->assertNotNull($certificate->agency, "Unexpected agency relationship value");
	}
	
	public function testFunctions(){
		$this->assertTrue(true);
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
