<?php

class TicketModelTest extends ModelTestCase {
		
	public function setUp()
	{		
		parent::setUp();		
	}
	
	public function testCRUD(){
		
		//Create/Read
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$ticket_id = ModelTestHelper::createTicket($company_id);
		$ticket = Ticket::find($ticket_id);
		
		$this->assertNotEquals(0, $ticket->id, "Unexpected id value");
		$this->assertEquals($company_id, $ticket->company_id, "Unexpected id value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $ticket->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING, $ticket->description, "Unexpected description value");
		$this->assertNotEquals("0000-00-00 00:00:00", $ticket->created_at);
		$this->assertNotEquals("0000-00-00 00:00:00", $ticket->updated_at);
				
		//Update		
		$ticket->name = ModelTestHelper::TEST_STRING_UPDATED;
		$ticket->description = ModelTestHelper::TEST_STRING_UPDATED;
		$ticket->save();		
		$ticket = Ticket::find($ticket_id);
		
		$this->assertNotEquals(0, $ticket->id, "Unexpected id value");		
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $ticket->name, "Unexpected name value");
		$this->assertEquals(ModelTestHelper::TEST_STRING_UPDATED, $ticket->description, "Unexpected description value");
				
		//Delete - soft, restore, force
		$ticket->delete();		
		$ticket = Ticket::find($ticket_id);
		$this->assertNull($ticket, "Ticket not soft deleted");
		
		$ticket = Ticket::onlyTrashed()->where('id', '=', $ticket_id)->first();
		$this->assertNotNull($ticket, "Ticket soft deleted but cant be found");
		$this->assertNotNull($ticket->deleted_at);
		
		Ticket::onlyTrashed()->where('id', '=', $ticket_id)->restore();
		$ticket = Ticket::find($ticket_id);
		$this->assertNotNull($ticket, "Ticket not restored");
		$this->assertNull($ticket->deleted_at);
				
		Ticket::withTrashed()->where('id', '=', $ticket_id)->forceDelete();
		$ticket = Ticket::withTrashed()->where('id', '=', $ticket_id)->first();
		$this->assertNull($ticket, "Ticket not force deleted");
	}
	
	public function testValidation(){
		$this->markTestIncomplete('This test needs to be completed!');
	}
	
	public function testRelationships(){		
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$ticket_id = ModelTestHelper::createTicket($company_id);
		$ticket = Ticket::find($ticket_id);
		
		$this->assertNotNull($ticket->company, "Unexpected company relationship value");
	}
	
	public function testFunctions(){
		$continent_id = ModelTestHelper::createContinent();
		$currency_id = ModelTestHelper::createCurrency();
		$country_id = ModelTestHelper::createCountry($continent_id, $currency_id);
		$company_id = ModelTestHelper::createCompany($country_id, $currency_id);
		$ticket_id = ModelTestHelper::createTicket($company_id);
		$ticket = Ticket::find($ticket_id);
		
		$this->assertFalse($ticket->has_bookings, "Unexpected has_bookings value");
		$this->assertFalse($ticket->trashed, "Unexpected trashed value");
	}
	
	public function testEdges(){
		$this->assertTrue(true);
	}	
	
}
