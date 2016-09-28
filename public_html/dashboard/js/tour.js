function TourManager() {

	var settingsTour = new Tour({
		steps: [
			{
				element   : '#account-info', // @todo find a way to float this centre screen
				title	  : 'We Want To Know More About You',
				palcement : 'right',
				content   : 'Please fill in the following fields to create your profile.'
			},
			{
				element   : '#business-website',
				title     : 'Basic Info',
				placement : 'right',
				content   : 'Please enter your business website address.',
				onShown	  : function(tour) { }
			},
			{
				element   : '#agencies-list',
				title     : 'Accredited Agencies',
				placement : 'right',
				content   : 'Please select all of the diving agencies you are accredited to.',
				onShown	  : function(tour) { }
			},
			{
				element   : '#credit-info',
				title     : 'Account Credit Usage',
				placement : 'right',
				content   : 'Here, you can see when your scubawhereRMS licence will automatically renew, how many booking and e-mail credits you have remaining.',
				onShown	  : function(tour) { }
			},
			{
				element   : '#credit-info',
				title     : 'Need More Credits?',
				placement : 'right',
				content   : 'If you need any more credits throughout the year, you can purchase Bolt Ons <a target="_blank" href="http://scubawhere.com/bolt-ons">here</a>.',
				onShown	  : function(tour) { }
			},
			{
				element   : '#postcode-div',
				title     : 'Locating your business',
				placement : 'left',
				content   : 'We need your address so it can be included on all Booking and Transaction confirmation e-mails.',
				onShown	  : function(tour) { }
			},
			{
				element   : '#legal-info',
				title     : 'Your Business Information',
				placement : 'left',
				content   : 'Please enter your VAT/ GST and business registration number.',
				onShown	  : function(tour) { }
			},
			{
				element   : '#country_id',
				title     : 'Your Country and Currency',
				placement : 'left',
				content   : 'Please select your country and preferred currency from the drop down menu. This will be the currency that your business primarily operates in.',
				onShown	  : function(tour) { }
			},
			{
				element   : '#terms-file',
				title     : 'Uploading your Terms and Conditions',
				placement : 'left',
				content   : 'Upload your customer booking Terms and Conditions in .PDF format. This will be included on every booking and transaction confirmation e-mail sent to your customers.',
				onShown	  : function(tour) { }
			},
			{
				element   : '#save-company-info',
				title     : 'Save and Finish your Settings',
				placement : 'left',
				content   : "Click 'End Tour' and make sure you have completed all of the required fields. When you are ready, click 'SAVE' then click 'NEXT' to continue the Setup Wizard.",
				onShown	  : function(tour) { }
			}
		],
		onEnd : function(tour) {
			$('html, body').css('overflowY', 'auto'); 
			$('html, body').css('overflowY', 'auto'); 
		}
	});

	var accommodationTour = new Tour({
		steps: [
			{
				element   : '#accommodations-list', // @todo find a way to float this centre screen
				title	  : 'Managing Accommodations',
				content   : "Do you create reservations for accommodation that you own or take money for? If so, let us guide you through how to create accommodation. Otherwise, click 'END TOUR' and then click 'NEXT STEP'."
			},
			{
				element   : '#acom-name',
				title     : 'Adding Accommodation',
				placement : 'left',
				content   : "You can configure scubawhereRMS to manage individual rooms, room types or dormitories. To get started, enter the room name or the type of room and description. For example, we have called this type of room '3* Hotel Single Room'. Go ahead and change it to create your own room.",
				onShown	  : function(tour) {
					$("#room-name").val("3* Hotel Single Room");
					CKEDITOR.instances['acom-description'].setData('<p>A very spacious single room with a great view.</p>');
				}
			},
			{
				element	  : '#acom-base',
				title	  : 'Assigning a Price',
				placement : 'left',
				content	  : 'Here, you can set a price per night for the room.',
				onShown   : function(tour) {
					$("#acom-price").val(50);
				}
			},
			{
				element	  : '#acom-season',
				title	  : 'Assigning Seasonal Price Changes',
				placement : 'left',
				content	  : 'If your prices vary depending on the season, you can create seasonal prices here.',
				onShown   : function(tour) {
					$("#acom-season-price").click();
					$("#seasonal-prices-list #acom-price").val(60);
				}
			},
			{
				element   : '#acom-rooms',
				title     : 'Assiging the number of rooms',
				placement : 'left',
				content   : 'Enter the number of rooms you have available. If you are creating rooms individually, just enter 1. If you are creating rooms by room type, enter the number of rooms you have of this type at the price you have entered. If you are creating a dormitory room, you will need to create these individually and you will need to enter how many Beds are in this room.',
				onShown   : function(tour) {
                	$("#room-amount").val(6);
				}
			},
			{
				element   : '#add-accommodation',
				title     : 'Saving you Accommodation',
				placement : 'left',
				content   : 'Click SAVE to create your accommodation.',
				onShown   : function(tour) { }
			},
			{
				element   : '#accommodations-list',
				title     : 'Viewing Your Accommodation',
				placement : 'right',
				content   : "All saved accommodation will appear in this list. Click on an item to view or edit the details. Now, click 'End Tour' and when you are ready, click 'NEXT' to continue the Setup Wizard.",
				onShown   : function(tour) {
					$('form').data('hasChanged', false);
					renderEditForm();
					$("#no-accommodations").remove();
					$("#accommodation-list").append('<li id="dummy-room"><strong>3* Hotel Single Room</strong> | 6 | 50</li>');
			   	}
			}
		],
		onEnd : function(tour) {
			$('html, body').css('overflowY', 'auto'); 
			$('#dummy-room').remove();
		}
	});

	var agentsTour = new Tour({
		steps: [
			{
				element : '#agent-list-div', // @todo find a way to float this centre screen
				title	: 'Managing Agents',
				content : "Do you receive bookings from third party agents, such as a local hotel travel desk or an international travel agent? If so, let's set these agents setup. If you do not receive from third-party agents, please click 'End Tour' now and then click 'NEXT STEP' to continue the setup wizard." 
			},
			{
				element : '#agent-form-container',
				title: 'Adding Agents',
				placement: 'left',
				content: 'Create an Agent profile by entering their details. Such as their name, business website, branch address, contact telephone number and branch e-mail address. If the billing address is different to the branch address, check the checkbox and enter in the address where invoices should be sent.',
				onShown	  : function(tour) {
					$("#agent-name").val("John doe");
					$("#agent-web").val("http://www.onlinescubaholidays.com");
					$("#branch-name").val("Scuba holidays R us");
					$("#branch-address").val("46 grand avenue tenerife");
					$("#branch-phone").val("+44 7866565047");
					$("#branch-email").val("john.doe@scubaholidays.com");
				}
			},
			{
				element	  : '#commission-div',
				title	  : 'Assigning a Commission',
				placement : 'left',
				content	  : 'Enter the percentage of commission the agent recieves for each reservation.',
				onShown   : function(tour) {
					$("#commission-amount").val(20);
				}
			},
			{
				element	  : '#terms-div',
				title	  : 'Defining the business terms',
				placement : 'left',
				content	  : "Define your Terms of Business with the agent from one of the drop-down options. 'Deposit only' means the agent will only take the commission as a deposit from your customers and the customer must pay the remaining balance to you. 'Full amount' means the customer pays the agent the full amount for the booking, then you will invoice the agent for payment minus commission. 'Banned' means that the agent is blocked and they are no longer allowed to make reservations.",
				onShown   : function(tour) {
					$('#terms').val('deposit');
				}
			},
			{
				element   : '#add-agent',
				title     : 'Saving your Agent',
				placement : 'left',
				content   : "Click 'SAVE' to create you agent profile.",
				onShown   : function(tour) { }
			},
			{
				element   : '#agent-list-div',
				title     : 'Viewing your agents',
				placement : 'right',
				content   : "Once an agent is saved, you will see it appear in this list. You can click on an agent profile to edit the information as you require. Now click 'End Tour' and when you are ready, click 'NEXT STEP' to continue the setup wizard.",
				onShown   : function(tour) {
					$('form').data('hasChanged', false);
					renderEditForm();
					$("#no-agents").remove();
					$("#agent-list").append('<li id="dummy-agent"><strong>John doe</strong> | Scuba holidays r us</li>');
			   	}
			}
		],
		onEnd : function(tour) {
			$('html, body').css('overflowY', 'auto'); 
			$('#dummy-agent').remove();
		}
	});

	var locationsTour = new Tour({
		steps: [
			{
				element   : '#map-container', // @todo find a way to float this centre screen
				title	  : 'Managing Locations',
				placement : 'top',
				content   : 'Now you need to create your dive sites. These will be used when create a trip later.'
			},
			{
				element   : '#add-location',
				title     : 'Adding Locations',
				placement : 'bottom',
				content   : "To add a location, simply enter the longitude and latitude coordinates.",
				onShown	  : function(tour) {
				}
			},
			{
				element   : '#showLocation',
				title     : 'Viewing the location',
				placement : 'left',
				content   : 'Click ‘Show’ to see exactly where the co-ordinates display on the Map.',
				onShown	  : function(tour) {
				}
			},
			{
				element   : '#createLocation',
				title     : 'Creating the location',
				placement : 'left',
				content   : "When you click 'Create', a pop up box will appear. Enter a name and description for the dive location. Additionally, you can select any relevant tags for the dive location.",
				onShown	  : function(tour) {
				}
			},
			{
				element   : '#map-container',
				title     : 'Viewing your locations',
				placement : 'top',
				content   : 'Here is a map that displays all the available dive locations, made by you and other businesses. The house icon represents where your dive operation is based.',
				onShown	  : function(tour) { }
			},
			{
				element   : '#markers-info',
				title     : 'Loctation Markers',
				placement : 'bottom',
				content   : "There are three different types of markers on this map. Red markers indicate a dive site that you visit. Green markers are dive sites that you have selected but have not created yet. Blue markers indicate dive sites other dive operators visit. If there is a Blue marker on the dive site you visit, you can share this marker by clicking on the dive site and then clicking 'Add to your Locations'. You can return to this screen at any time to add and remove locations as you wish. Now, click 'End Tour' and when you have created all of your locations, click 'NEXT STEP'.",
				onShown	  : function(tour) { }
			}
		],
		onEnd : function(tour) {
			$('html, body').css('overflowY', 'auto'); 
		}
	});

	var boatsTour = new Tour({
		steps: [
			{
				element   : '#boat-list-container', // @todo find a way to float this centre screen
				title	  : 'Managing Your Boats',
				placement : 'right',
				content   : 'Now you can create your boats, which are assigned to trips when scheduled. This allows you to manage your schedule for day and liveaboard trips.'
			},
			{
				element   : '#cabins-container',
				title     : 'Manage Cabins',
				placement : 'right',
				content   : 'If you run liveaboard trips, you will need to create cabins. You need to create all of the cabins individually. For example, cabin A, cabin B or King Suite, Deluxe Suite.',
				onShown	  : function(tour) { }
			},
			{
				element   : '#boatroom-list-container',
				title     : 'Creating Cabins',
				placement : 'right',
				content   : "To create a cabin, click 'ADD CABIN'. Then enter the desired name and a description if you wish., then click 'SAVE'.",
				onShown	  : function(tour) { }
			},
			{
				element   : '#boat-form-container',
				title     : 'Creating Boats',
				placement : 'left',
				content   : "To create a boat, click 'ADD BOAT' and enter the name and a description of the boat.",
				onShown	  : function(tour) {
					$("#boat-name").val("Barry's big boat");
					CKEDITOR.instances['boat-description'].setData('<p>This is our largest boat at 29 feet, it hold up to 18 divers comftably and has a small deck near the back perfect for getting some sun.</p>');
				}
			},
			{
				element   : '#boat-capacity',
				title     : 'Assigning the Boats Size',
				placement : 'left',
				content   : 'Enter the maximum capacity of customers for your boat, excluding all crew.',
				onShown	  : function(tour) {
					$("#boat-capacity").val(25);
				}
			},
			{
				element   : '#boat-cabins',
				title     : 'Assigning Cabins to a Boat',
				placement : 'left',
				content   : "If your boat is a liveaboard, here is where you add your previously created cabins. To attach a cabin to a boat, click 'Add Cabin'. Select which cabin you would like to add from the drop-down options and enter the number beds in that cabin.",
				onShown	  : function(tour) {
					$("#room-types").append('<p id="cabin-option">' +
					'<select class="room-type-select">' +
					'<option value="{{id}}">Single Cabin</option>' +
					'</select> Number of rooms:' +
					'<input type="number" value="6" placeholder="0" style="width: 100px;" min="0">' +
					'<button class="btn btn-danger remove-room">&#215;</button>' +
					'</p>');
				}
			},
			{
				element   : '#add-boat',
				title     : 'Saving you Boat',
				placement : 'left',
				content   : "Click 'SAVE' to create your boat.",
				onShown	  : function(tour) { }
			},
			{
				element   : '#boat-list-container',
				title     : 'Viewing Your Boats',
				placement : 'right',
				content   : "Once you have saved your boat, it will appear here in this list. You can view and edit a boat by clicking on it. Just remember to click 'SAVE'! Now, click 'End Tour' and when you are ready, click 'NEXT STEP' to continue to the Setup Wizard.",
				onShown	  : function(tour) {
					$('form').data('hasChanged', false);
					renderEditForm();
					$('#no-boats').remove();
					$("#boat-list").append('<li id="dummy-boat"><strong>Barrys big boat</strong> | Capacity: 25</li>');
			   	}
			}
		],
		onEnd : function(tour) {
			$('html, body').css('overflowY', 'auto'); 
        	$("#dummy-boat").remove();
		}
	});
	
	var tripsTour = new Tour({
		steps: [
			{
				element   : '#trip-list-container', // @todo find a way to float this centre screen
				title	  : 'Managing Trips',
				placement : 'right',
				content   : "Here is where you create your trips. A trip consists a duration and locations so let's get started!"
			},
			{
				element   : '#trip-form-container',
				title     : 'Creating a trip',
				placement : 'left',
				content   : "Enter a name, trip description and the duration for the trip in hours. If your trip is for a shore-based dive, make sure you check the checkbox under the name text field labelled 'This is a shore-based dive and does not require a boat'.",
				onShown	  : function(tour) {
					$("#trip-name").val("Single boat dive");
					CKEDITOR.instances['description'].setData('<p>This dive is great for new and experienced divers, we visit a beautiful reef only a few miles of the shore.</p>');
					$("#tripDuration").val(4);
				}
			},
			{
				element   : '#locationsList',
				title     : 'Selecting locations',
				placement : 'left',
				content   : 'Select all the locations this trip visits.',
				onShown	  : function(tour) {
                	$('#locationsList').find('.location').filter(':first').click();
				}
			},
			{
				element   : '#tagsList',
				title     : 'Adding tags',
				placement : 'left',
				content   : "Select the relevant tags for the trip too.",
				onShown	  : function(tour) {
					$('#tagsList').find('.tag').filter(':first').click();
				}
			},
			{
				element   : '#add-trip',
				title     : 'Saving you Trip',
				placement : 'left',
				content   : "Click 'SAVE' to create your trip.",
				onShown	  : function(tour) { }
			},
			{
				element   : '#trips-list-div',
				title     : 'Viewing Trips',
				placement : 'right',
				content   : "Once a trip is saved, you it appear in this list. You can click on a trip to view or edit it's details. Now, click 'End Tour' and when you are ready, click 'NEXT STEP' to continue the Setup Wizard",
				onShown	  : function(tour) {
					$('form').data('hasChanged', false);
					renderEditForm();
					$("#no-trips").remove();
					$("#trip-list").append('<li id="dummy-trip"><strong>Single boat dive</strong> | 0d 4h </li>');
			   	}
			}
		],
		onEnd : function(tour) {
			$('html, body').css('overflowY', 'auto'); 
        	$("#dummy-trip").remove();
		}
	});

	var ticketsTour = new Tour({
		steps: [
			{
				element   : '#tickets-list-div', // @todo find a way to float this centre screen
				title	  : 'Managing Tickets',
				placement : 'right',
				content   : 'Here is where you create your tickets. A ticket is used to control trip capacity and the difference reasons why that person is on the trip. For example, you might need a Snorkeller ticket, a Certified Diver ticket and a Course Diver ticket. All these tickets are different prices but can go on the same trip together. Let us guide you through creating your first ticket!'
			},
			{
				element   : '#container-ticket-description',
				title     : 'Creating a Ticket',
				placement : 'left',
				content   : "Create a name for the ticket and give it a description. Enter the base price for the ticket too. This is the gross price the customer pays for the trip. If you want to change your prices, simply return to this screen and click 'Add another base price', select the date the price change will be effective from and don't forget to click 'SAVE'.",
				onShown	  : function(tour) {
					$("#ticket-name").val("2 dive boat ticket");
					CKEDITOR.instances['description'].setData('<p>This ticket is perfect for divers of any skill level, our first dive goes to either a reef or ship wreck depending on the conditions. The next dive is just a few miles of the shore were turtles are regularly seen. The ticket also comes with a fre lunch.</p>');
					$("#acom-price").val(50); //@todo change handlebars tempate to change name based on tab
				}
			},
			{
				element	  : '#tickets-seasonal',
				title	  : 'Assigning Seasonal Price Changes',
				placement : 'left',
				content	  : 'If you operate with seasonal price changes, here you can set the dates for when price increases or decreases are applicable for.',
				onShown   : function(tour) {
					$("#seasonal-prices-checkbox").click();
					$("#seasonal-prices-list input").first().val(175);
				}
			},
			{
				element   : '#tickets-trips',
				title     : 'Assigning Trips',
				placement : 'left',
				content   : 'Select which trip this ticket can be used for. A ticket can be used for multiple trips too.',
				onShow 	  : function(tour) {
					$("#seasonal-prices-checkbox").click();
				},
				onShown	  : function(tour) {
					$('input').filter('[name="trips[]"]').first().click();
				}
			},
			{
				element   : '#tickets-boats',
				title     : 'Assigning Specific Boats to a Trip',
				placement : 'left',
				content   : 'You can be specific to which boat the ticket is applicable for. Simply, check the checkbox for the boat you want to limit this ticket too.',
				onShown	  : function(tour) {
					//$("#tickets-boats-checkbox").click();
				}
			},
			{
				element   : '#tickets-boatrooms',
				title     : 'Assigning Specific Boatrooms to a Boat',
				placement : 'left',
				content   : 'You can be even more specific to which cabin the ticket is applicable for too. Simply, check the checkbox for the cabin you want to limit this ticket too.',
				onShow	  : function(tour) {
					//$("#tickets-boats-checkbox").click();
				},
				onShown	  : function(tour) {
					$("#tickets-boatroom-checkbox").click();
				}
			},
			{
				element   : '#tickets-availability',
				title     : "Special Offer Ticket",
				placement : 'left',
				content   : 'If this ticket is a limited time Special Offer, you can set the dates to when the ticket can be purchased and specific range of dates to when the customer can use the ticket.',
				onShow	  : function(tour) {
					$("#tickets-boatroom-checkbox").click();
				},
				onShown	  : function(tour) {
					$('#tickets-availability-checkbox').click();
				}
			},
			{
				element   : '#add-ticket',
				title     : 'Saving you Ticket',
				placement : 'left',
				content   : "Click 'SAVE' to create your ticket.",
				onShown	  : function(tour) { }
			},
			{
				element   : '#ticket-list-container',
				title     : 'Viewing Tickets',
				placement : 'right',
				content   : "Once a ticket is saved, you will see it appear here in this list. You can click on a ticket to view and edit it's details. Now, click 'End Tour' and when you are ready, click 'NEXT STEP' to continue the Setup Wizard.",
				onShown	  : function(tour) {
					$('form').data('hasChanged', false);
					renderEditForm();
					$("#no-trips").remove();
					$("#trip-list").append('<li id="dummy-trip"><strong>Single boat dive</strong> | 0d 4h </li>');
			   	}
			}
		],
		onEnd : function(tour) {
			$('html, body').css('overflowY', 'auto'); 
        	$("#dummy-ticket").remove();
		}
	});

	var classesTour = new Tour({
		steps: [
			{
				element   : '#classes-list-div', // @todo find a way to float this centre screen
				title	  : 'Managing Classes',
				placement : 'right',
				content   : "This is the first stage to creating your Courses. A course is created by first creating Classes. This is where you create your Classroom theory based sessions and Confined Water sessions, if you conduct training elements in a swimming pool. You will create your Course in the next step of the Setup Wizard. So, for now, let's create your Classes. Note: Classes cannot be booked by customer, they need to be part of a course, which is the next step."
			},
			{
				element   : '#class-form-container',
				title     : 'Adding a Class',
				placement : 'left',
				content   : 'Enter a name, description and duration for the class.',
				onShown	  : function(tour) {
					$("#class-name").val("Open Water Theory");
					CKEDITOR.instances['description'].setData('<p>This is a classroom session that teaches the basics of diver safety, it runs for about 2 hours with a short test at the end.</p>');
					$("#tripDuration").val(5); // @todo rename this in classes and go through all mangement tabs
				}
			},
			{
				element   : '#add-class',
				title     : 'Saving the Class',
				placement : 'left',
				content   : "Click 'SAVE' to create the class.",
				onShown	  : function(tour) { }
			},
			{
				element   : '#classes-list-div',
				title     : 'Viewing you Classes',
				placement : 'right',
				content   : "Once a class is saved, you can see it appear in your list here. Click on a class to view or edit it's details. Now, click 'End Tour' and when you are ready to move on, click 'NEXT STEP'.",
				onShown	  : function(tour) {
					$('form').data('hasChanged', false);
					renderEditForm();
					$('#no-classes').remove();
					$("#class-list").append('<li id="dummy-class"><strong>Open Water Theory</strong> </li>');
				}
			}
		],
		onEnd : function(tour) {
			$('html, body').css('overflowY', 'auto'); 
        	$("#dummy-class").remove();
		}
	});

	var coursesTour = new Tour({
		steps: [
			{
				element   : '#packages-list-div', // @todo find a way to float this centre screen
				title	  : 'Managing your Courses',
				placement : 'right',
				content   : "This is the 2nd stage of building a course. A Course consists of Classes and Tickets that you have previously created. For example, an Open Water Course could consist of 2 Classes and 2 Tickets, which could equal to a 4 Day Open Water Course. Let's get started and build your course."
			},
			{
				element   : '#select-certification',
				title     : 'Selecting a Certification',
				placement : 'left',
				content   : 'Select the qualification this course will provide your customer with on completion. If you do not see the agency listed, you will need to go back to Settings and check the relevant checkbox to include the agency qualifications.',
				onShown	  : function(tour) {
					$('#select-certification').val(4);
				}
			},
			{
				element   : '#course-form-container',
				title     : 'Creating a Course',
				placement : 'left',
				content   : 'Create the name of the course, give it a description and set the capacity for the course. The capacity set should represent your maximum students to instructor ratio.',
				onShown	  : function(tour) {
					$("#course-name").val("PADI open water");
					CKEDITOR.instances['description'].setData('<p>This open water course is perfect to new comers to the sport. After this course you will be qualified to dive anywhere aslong as you have a buddy!</p>');
					$("#course-capacity").val(10);
				}
			},
			{
				element   : '#course-classes',
				title     : 'Assigning Classes to your Course',
				placement : 'left',
				content   : 'Now, select the classes that you want to include in the course.',
				onShown	  : function(tour) {
					$('.class-select').first().find('option:eq(1)').attr('selected', true);
				}
			},
			{
				element   : '#course-tickets', // @todo change this to course-classes
				title     : 'Assigning Tickets to your Course',
				placement : 'left',
				content   : 'Select the tickets which your customers will use to complete their Open Water Dives for the course.',
				onShown	  : function(tour) {
					$('.ticket-select').first().find('option:eq(2)').attr('selected', true);
				}
			},
			{
				element   : '#course-base', // @todo change this to course-classes
				title     : 'Setting a Price',
				placement : 'left',
				content   : 'Enter how much the course will cost the customer. When setting this price, the cost for the included tickets will be overridden to enable you to charge the customer to correct amount.',
				onShown	  : function(tour) {
					$('#acom-price').val(200);;
				}
			},
			{
				element	  : '#course-seasonal',
				title	  : 'Assigning Seasonal Price Changes',
				placement : 'left',
				content	  : 'Just like in Tickets, you can add future price changes to base and seasonal prices if required.',
				onShown   : function(tour) {
					$("#course-season-price").click();
					$("#seasonal-prices-list input").first().val(175);
				}
			},
			{
				element   : '#add-course',
				title     : 'Saving the Course',
				placement : 'left',
				content   : "When you are happy with the course, click 'SAVE'. Note : If you want to change the quantities of Tickets and Classes of a Course later, you will need to delete this course and re-build the course as your require.",
				onShown	  : function(tour) { }
			},
			{
				element   : '#packages-list-div',
				title     : 'Viewing your Courses',
				placement : 'right',
				content   : "Once you have saved a Course, you will see it appear in this list. Click on a Course to view and edit it's details. Now, click 'End Tour' and when you are happy to continue with the Setup Wizard, click 'NEXT STEP'.", 
				onShown	  : function(tour) {
					$('form').data('hasChanged', false);
					renderEditForm();
					$("#course-list").append('<li id="dummy-course"><strong>PADI open water</strong> | £10.00 </li>');
			   	}
			}
		],
		onEnd : function(tour) {
			$('html, body').css('overflowY', 'auto'); 
        	$("#dummy-course").remove();
		}
	});

	var addonsTour = new Tour({
		steps: [
			{
				element   : '#addon-list-div', // @todo find a way to float this centre screen
				title	  : 'Managing Addons',
				placement : 'right',
				content   : "Here is where you create Add-Ons. An Add-On is optional purchases that a customer may wish to purchase as required, such as hiring a Digital Camera, Nitrox Air, a Guided Dive or a Hotel/ Airport Transfer. So, let's guide you through how to create Add-Ons."
			},
			{
				element   : '#addon-form-container',
				title     : 'Creating an Addon',
				placement : 'left',
				content   : 'Enter a name, description and price for the add-on.',
				onShown	  : function(tour) {
					$("#addon-name").val("Reef diving tax");
					CKEDITOR.instances['description'].setData('<p>Reef tax is compulsory for all divers in this country, the money goes towards cleaning of the ocean to ensure we preserve our beautiful dive spots.</p>');
					$("#acom-price").first().val(10); //@todo look at price input template
				}
			},
			{
				element   : '#add-addon',
				title     : 'Save the Addon',
				placement : 'left',
				content   : "When you are happy, click 'SAVE'.",
				onShown	  : function(tour) { }
			},
			{
				element   : '#addon-list-div',
				title     : 'Viewing Addons',
				placement : 'right',
				content   : "Once you have saved your Add-On, it will appear here in this list. You view and edit your Add-Ons by selecting them individually. Now, click 'End Tour' and when you are ready to move on to the next stage of the Setup Wizard, click 'NEXT STEP'.",
				onShown	  : function(tour) {
					$('form').data('hasChanged', false);
					renderEditForm();
					$("#addon-list").append('<li id="dummy-addon"><strong>Reef diving tax</strong> | £10.00 </li>');
				}
			}
		],
		onEnd : function(tour) {
			$('html, body').css('overflowY', 'auto'); 
        	$("#dummy-addon").remove();
		}
	});

	var packagesTour = new Tour({
		steps: [
			{
				element   : '#packages-list-div', // @todo find a way to float this centre screen
				title	  : 'Managing Packages',
				placement : 'right',
				content   : 'This is where you create packages. A package can include Tickets, Add-Ons, Courses and Accommodation. Let us guide you through how to create a package.'
			},
			{
				element   : '#container-package-description',
				title     : 'Creating Packages',
				placement : 'left',
				content   : "Let's begin with giving the Package a name and description.",
				onShown	  : function(tour) {
					$("#package-name").val("Family dive day");
					CKEDITOR.instances['description'].setData('<p>This package is perfect for families, 2 adults and 2 children dives are included.</p>');
					$('#acom-price').val(150);
				}
			},
			{
				element   : '#package-entities',
				title     : 'Assigning Tickets, Courses, Addons and Accommodations',
				placement : 'left',
				content   : 'Now, select the Tickets, Courses, Accommodations and Add Ons that you want to include in the package.',
				onShown	  : function(tour) {
					$('.entity-select').filter('[data-model="ticket"]').find('option:eq(1)').attr('selected', true);
					$('.entity-select').filter('[data-model="course"]').find('option:eq(1)').attr('selected', true);
					$('.entity-select').filter('[data-model="addon"]').find('option:eq(1)').attr('selected', true);
					$('.entity-select').filter('[data-model="accommodation"]').find('option:eq(1)').attr('selected', true);
				}
			},
			{
				element	  : '#package-base',
				title	  : 'Assigning a Price',
				placement : 'left',
				content	  : 'Enter the price for this package. This price will override all the previous prices set for the individual items of this package.',
				onShown   : function(tour) {
					$('#acom-price').val(600);
				}
			},
			{
				element	  : '#package-seasonal',
				title	  : 'Assigning Seasonal Price Changes',
				placement : 'left',
				content	  : 'Just like before, you can set price changes and seasonal prices as required too.',
				onShown   : function(tour) {
					$("#package-season-price").click();
					$("#seasonal-prices-list input").first().val(175);
				}
			},
			{
				element   : '#package-availability',
				title     : 'Special Offer Package',
				placement : 'left',
				content   : 'You can create Special Offer Packages by setting the dates of when the customer can purchase the package and what dates the package can be used.',
				onShow 	  : function(tour) {
					$("#package-season-price").click();
				},
				onShown	  : function(tour) {
					$('#package-availability-checkbox').click();
				}
			},
			{
				element   : '#add-package',
				title     : 'Saving your Package',
				placement : 'left',
				content   : "Click 'SAVE' to create your package.",
				onShow    : function(tour) {
					$('#package-availability-checkbox').click();
				},
				onShown	  : function(tour) { }
			},
			{
				element   : '#packages-list-div',
				title     : 'Viewing Packages',
				placement : 'right',
				content   : "Once a package is saved, it will appear here in your list. You can click on a package to view or edit it's details. Now, click 'End Tour' and when you are ready click 'FINISH TOUR'.",
				onShow    : function(tour) {
					$('#package-availability-checkbox').click();
				},
				onShown	  : function(tour) {
					$('form').data('hasChanged', false);
					renderEditForm();
					$("#package-list").append('<li id="dummy-package"><strong>Family dive day</strong> | £150.00 </li>');
			   	}
			}
		],
		onEnd : function(tour) {
			$('html, body').css('overflowY', 'auto'); 
        	$("#dummy-package").remove();
		}
	});

	this.getSettingsTour = function() {
		if(window.tourStart) 
		{
			settingsTour.init();
			settingsTour.start(true);	
			// Force the tour to start at the beginning
			settingsTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 1) {
                    window.currentStep = {
                        tab: "#accommodations",
                        position: 2
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#acom-tab').addClass("selected");
                window.location.href = "#accommodations";
            });
		}
	};

	this.getAccommodationsTour = function() {
		if(window.tourStart) 
		{
			accommodationTour.init();
			accommodationTour.start(true);	
			// Force the tour to start at the beginning
			accommodationTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 2) {
                    window.currentStep = {
                        tab: "#agents",
                        position: 3
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#agent-tab').addClass("selected");
                window.location.href = "#agents";
            });
		}
	};

	this.getAgentsTour = function() {
		if(window.tourStart) 
		{
			agentsTour.init();
			agentsTour.start(true);	
			// Force the tour to start at the beginning
			agentsTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 3) {
                    window.currentStep = {
                        tab: "#locations",
                        position: 4
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#location-tab').addClass("selected");
                window.location.href = "#locations";
            });
		}
	};

	this.getLocationsTour = function() {
		if(window.tourStart) 
		{
			locationsTour.init();
			locationsTour.start(true);	
			// Force the tour to start at the beginning
			locationsTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 4) {
                    window.currentStep = {
                        tab: "#boats",
                        position: 5
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#boat-tab').addClass("selected");
                window.location.href = "#boats";
            });
		}
	};

	this.getBoatsTour = function() {
		if(window.tourStart) 
		{
			boatsTour.init();
			boatsTour.start(true);	
			// Force the tour to start at the beginning
			boatsTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 5) {
                    window.currentStep = {
                        tab: "#trips",
                        position: 6
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#trip-tab').addClass("selected");
                window.location.href = "#trips";
            });
		}
	};

	this.getTripsTour = function() {
		if(window.tourStart) 
		{
			tripsTour.init();
			tripsTour.start(true);	
			// Force the tour to start at the beginning
			tripsTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 6) {
                    window.currentStep = {
                        tab: "#tickets",
                        position: 7
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#ticket-tab').addClass("selected");
                window.location.href = "#tickets";
            });
		}
	};

	this.getTicketsTour = function() {
		if(window.tourStart) 
		{
			ticketsTour.init();
			ticketsTour.start(true);	
			// Force the tour to start at the beginning
			ticketsTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 7) {
                    window.currentStep = {
                        tab: "#classes",
                        position: 8
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#class-tab').addClass("selected");
                window.location.href = "#classes";
            });
		}
	};

	this.getClassesTour = function() {
		if(window.tourStart) 
		{
			classesTour.init();
			classesTour.start(true);	
			// Force the tour to start at the beginning
			classesTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 8) {
                    window.currentStep = {
                        tab: "#courses",
                        position: 9
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#course-tab').addClass("selected");
                window.location.href = "#courses";
            });
		}
	};

	this.getCoursesTour = function() {
		if(window.tourStart) 
		{
			coursesTour.init();
			coursesTour.start(true);	
			// Force the tour to start at the beginning
			coursesTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 9) {
                    window.currentStep = {
                        tab: "#add-ons",
                        position: 10
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#addon-tab').addClass("selected");
                window.location.href = "#add-ons";
            });
		}
	};

	this.getAddonsTour = function() {
		if(window.tourStart) 
		{
			addonsTour.init();
			addonsTour.start(true);	
			// Force the tour to start at the beginning
			addonsTour.goTo(0);	

            $("#tour-next-step").on("click", function() {
                if (window.currentStep.position <= 10) {
                    window.currentStep = {
                        tab: "#packages",
                        position: 11
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#package-tab').addClass("selected");
                window.location.href = "#packages";
            });
		}
	};

	this.getPackagesTour = function() {
		if(window.tourStart) 
		{
			packagesTour.init();
			packagesTour.start(true);	
			// Force the tour to start at the beginning
			packagesTour.goTo(0);	

			$('#tour-next-step').hide();
			$('#tour-finish').show();

            $("#tour-finish").click(function(event) {
                var params = {
                    _token: window.token
                };
                Company.initialise(params, function success(data) {
                    pageMssg("Thank you for following our wizard. Your system is now fully configured.", 'success');
					localStorage.setItem('finishedTour', 'true');
                    setTimeout(function() {
                        window.location.hash = "#scheduling";
                        window.location.reload(true);
                    }, 3000);
                });
            });
		}
	};

};

var TourManager = new TourManager();

/*var Tour = {

    getAcommodationsTour: function() {
        if (window.tourStart) {
            $("#tour-next-step").show();
            $("#tour-finish").hide();
            introJs().setOptions({
                showStepNumbers: false,
                exitOnOverlayClick: false,
                exitOnEsc: false
            }).start().onchange(function(targetElement) {
                switch (targetElement.id) {
                    case "accommodation-form-container":
                        $("#room-name").val("3* Hotel Single Room");
                        $("#acom-price").val(50);
                        break;
                    case "acom-base":
                        $("#add-base-price").click();
                        $("#acom-price").val(50);
                        break;
                    case "acom-season":
                        $("#acom-season-price").click();
                        $("#acom-price").val(50);
                        break;
                    case "acom-rooms":
                        $("#room-amount").val(6);
                        break;
                    case "accommodations-list":
                        $("#no-accommodations").remove();
                        $("#accommodation-list").append('<li id="dummy-room"><strong>3* Hotel Single Room</strong> | 6 | 50</li>');
                        break;
                }
            }).oncomplete(function() {
                $("#dummy-room").remove();
                $("#accommodation-list").append('<p id="no-accommodations">No accommodations available.</p>');
                clearForm();
            });

            $("#tour-next-step").on("click", function() {
                window.location.href = "#agents";
                if (window.currentStep.position <= 1) {
                    window.currentStep = {
                        tab: "#agents",
                        position: 2
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#agent-tab').addClass("selected");
            });
        }
    },

    getAgentsTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 2) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "agent-form-container":
                            $("#agent-name").val("John doe");

                            $("#agent-web").val("http://www.onlinescubaholidays.com");
                            $("#branch-name").val("Scuba holidays R us");
                            $("#branch-address").val("46 grand avenue tenerife");
                            $("#branch-phone").val("+44 7866565047");
                            $("#branch-email").val("john.doe@scubaholidays.com");
                            break;

                        case "commission-div":
                            $("#commission-amount").val(20);
                            break;

                        case "agent-list-div":
                            $("#no-agents").remove();
                            $("#agent-list").append('<li id="dummy-agent"><strong>John doe</strong> | Scuba holidays r us</li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-agent").remove();
                    $("#agent-list").append('<p id="no-agents">No agents available.</p>');
                    clearForm();
                });
            }
            $("#tour-next-step").on("click", function() {
                window.location.href = "#locations";
                if (window.currentStep.position <= 2) {
                    window.currentStep = {
                        tab: "#locations",
                        position: 3
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#location-tab').addClass("selected");
            });
        }
    },

    getLocationsTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 3) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start();
            }
            $("#tour-next-step").on("click", function() {
                window.location.href = "#boats";
                if (window.currentStep.position <= 3) {
                    window.currentStep = {
                        tab: "#boats",
                        position: 4
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#boat-tab').addClass("selected");
            });
        }
    },

    getBoatsTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 4) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {

                        case "change-to-add-boatroom":
                            $("#boatroom-list").append('<li id="dummy-boatroom"><strong>Single Cabin</strong></li>');
                            break;

                        case "boat-form-container":
                            $("#boat-name").val("Barry's big boat");
                            //CKEDITOR.setData("Add a description of your boat here."); 
                            $("#boat-capacity").val(25);
                            break;

                        case "boat-cabins":
                            $("#room-types").append('<p id="cabin-option"> \
			        		<select class="room-type-select"> \
			        		<option value="{{id}}">Single Cabin</option> \
			        		</select> Number of rooms: \
			        		<input type="number" value="6" placeholder="0" style="width: 100px;" min="0"> \
			        		<button class="btn btn-danger remove-room">&#215;</button> \
			        		</p>');
                            break;

                        case "boats-list-div":
                            $("#no-boats").remove();
                            $("#cabin-option").remove();
                            $("#boat-list").append('<li id="dummy-boat"><strong>Barrys big boat</strong> | Capacity: 25</li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-boat").remove();
                    $("#boat-list").append('<p id="no-boats">No boats available.</p>');
                    clearForm();
                });
            }

            $("#tour-next-step").on("click", function() {
                if (window.boats.length != 0) {
                    window.location.href = "#trips";
                    if (window.currentStep.position <= 4) {
                        window.currentStep = {
                            tab: "#trips",
                            position: 5
                        };
                    }
                    $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                    $('#trip-tab').addClass("selected");
                } else alert("You need to add atleast one boat");
            });

        }
    },

    getTripsTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 5) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "trip-form-container":
                            $("#trip-name").val("Single boat dive");
                            $("#tripDuration").val(4);
                            break;
                        case "locationsList":
                            $('#locationsList').find('.location').filter(':first').click();
                            break;
                        case "tagsList":
                            $('#tagsList').find('.tag').filter(':first').click();
                            break;
                        case "trips-list-div":
                            $("#no-trips").remove();
                            $("#trip-list").append('<li id="dummy-trip"><strong>Single boat dive</strong> | 0d 4h </li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-trip").remove();
                    $("#trip-list").append('<p id="no-trips">No trips available.</p>');
                    clearForm();
                });
            }
            $("#tour-next-step").on("click", function() {
                if (window.trips.length != 0) {
                    window.location.href = "#tickets";
                    if (window.currentStep.position <= 5) {
                        window.currentStep = {
                            tab: "#tickets",
                            position: 6
                        };
                    }
                    $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                    $('#ticket-tab').addClass("selected");
                } else alert("You need to add atleast one ticket");
            });
        }
    },

    getTicketsTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 6) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "ticket-form-container":
                            $("#ticket-name").val("2 dive boat trip");
                            $("#ticket-base").val(50);
                            break;
                        case "tickets-seasonal":
                            $"#seasonal-prices-checkbox").click();
                            break;
                        case "tickets-boats":
                            $("#tickets-boats-checkbox").click();
                            break;
                        case "tickets-boatrooms":
                            $("#tickets-boatroom-checkbox").click();
                            break;
                        case "tickets-list-div":
                            $("#no-tickets").remove();
                            $("#ticket-list").append('<li id="dummy-ticket"><strong>New diving trip</strong> | £50.00 </li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-ticket").remove();
                    $("#ticket-list").append('<p id="no-tickets">No tickets available.</p>');
                    clearForm();
                });
            }

            $("#tour-next-step").on("click", function() {
                if (window.tickets.length != 0) {
                    window.location.href = "#classes";
                    if (window.currentStep.position <= 6) {
                        window.currentStep = {
                            tab: "#classes",
                            position: 7
                        };
                    }
                    $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                    $('#class-tab').addClass("selected");
                } else pageMssg("Please add atleast one ticket");
            });
        }
    },

    getClassesTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 7) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "class-form-container":
                            $("#class-name").val("Open Water Theory");
                            $("#trip-duration").val(5);
                            break;
                        case "class-list-div":
                            $("#no-classes").remove();
                            $("#class-list").append('<li id="dummy-class"><strong>Open Water Theory</strong> | £10.00 </li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-class").remove();
                    $("#class-list").append('<p id="no-classes">No classes available.</p>');
                    clearForm();
                });
            }

            $("#tour-next-step").on("click", function() {
                window.location.href = "#courses";
                if (window.currentStep.position <= 7) {
                    window.currentStep = {
                        tab: "#courses",
                        position: 8
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#course-tab').addClass("selected");
            });
        }
    },

    getCoursesTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 8) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "course-form-container":
                            $("#course-name").val("PADI open water");
                            $("#course-capacity").val(10);
                            //$("#course-description").val("Beginers course to diving");
                            break;
                        case "course-list-div":
                            $("#no-courses").remove();
                            $("#course-list").append('<li id="dummy-course"><strong>PADI open water</strong> | £10.00 </li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-course").remove();
                    //$("#course-list").append('<p id="no-courses">No courses available.</p>');
                    clearForm();
                });
            }

            $("#tour-next-step").on("click", function() {
                window.location.href = "#add-ons";
                if (window.currentStep.position <= 8) {
                    window.currentStep = {
                        tab: "#add-ons",
                        position: 9
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#addon-tab').addClass("selected");
            });
        }
    },

    getAddonsTour: function() {
        if (window.tourStart) {
            $("#tour-next-step").hide();
            $("#tour-finish").show();
            if (window.currentStep.position < 9) {
                window.location.href = window.currentStep.tab;
            } else {
                $("#tour-next-step").show();
                $("#tour-finish").hide();
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "addon-form-container":
                            $("#addon-name").val("Reef diving tax");
                            $("#addon-price").val(10);
                            break;
                        case "addon-compulsory-div":
                            $("#addon-compulsory").attr("checked", true);
                            break;
                        case "addon-list-div":
                            $("#no-addons").remove();
                            $("#addon-list").append('<li id="dummy-addon"><strong>Reef diving tax</strong> | £10.00 </li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-addon").remove();
                    $("#addon-list").append('<p id="no-addons">No addons available.</p>');
                    clearForm();
                });
            }

            $("#tour-next-step").on("click", function() {
                window.location.href = "#packages";
                if (window.currentStep.position <= 9) {
                    window.currentStep = {
                        tab: "#packages",
                        position: 10
                    };
                }
                $('.nav-wizard a').filter('.selected').first().addClass("done").removeClass("selected");
                $('#package-tab').addClass("selected");
            });
        }
    },

    getPackagesTour: function() {
        if (window.tourStart) {
            if (window.currentStep.position < 10) {
                window.location.href = window.currentStep.tab;
                console.log(window.currentStep.tab);
            } else {
                //$("#tour-button").empty();
                $("#tour-next-step").hide();
                $("#tour-finish").show();
                //$("#tour-button").append('<button id="tour-finish" class="btn btn-success text-uppercase">Finish tour</button>');
                introJs().setOptions({
                    showStepNumbers: false,
                    exitOnOverlayClick: false,
                    exitOnEsc: false
                }).start().onchange(function(targetElement) {
                    switch (targetElement.id) {
                        case "package-form-container":
                            $("#package-name").val("Family dive day");
                            break;
                        case "package-tickets":
                            $("#package-tickets").find(".ticket-select").filter(":first").val(1);
                            $("#package-tickets").find(".quantity-input").filter(":first").val(4);
                            break;
                        case "package-base":
                            $("#package-base").find(".base-price").filter(":first").val(150);
                            break;
                        case "package-seasonal":
                            $("#package-seasonal").find('input[type=checkbox]').filter(':first').click();
                            break;
                        case "package-size":
                            $("#package-capacity").val(4);
                            break;
                        case "packages-list-div":
                            $("#no-packages").remove();
                            $("#package-list").append('<li id="dummy-package"><strong>Family dive day</strong> | £150.00 </li>');
                            break;
                    }
                }).oncomplete(function() {
                    $("#dummy-package").remove();
                    $("#package-list").append('<p id="no-packages">No packages available.</p>');
                    clearForm();
                });
            }

            $("#tour-finish").click(function(event) {
                var params = {
                    _token: window.token
                };
                Company.initialise(params, function success(data) {
                    //$('#addon-tab').addClass("done");
                    pageMssg("Thank you for following our wizard. Your system is now fully configured.", true);
                    setTimeout(function() {
                        window.location.href = "#dashboard";
                        window.location.reload(true);
                    }, 3000);
                });
            });

        }
    }

};*/
