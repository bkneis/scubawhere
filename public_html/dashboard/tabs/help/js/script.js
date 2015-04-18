/**
 * Define your FAQ question and answers here.
 * Don't forget all the right commas in the right places!
 */
var faq = [
	{
		question: "How do I schedule a trip?",
		answer: "<p>Calendar > <a href='#scheduling' alt='Scheduling'>Scheduling</a></p><p>Select a trip you wish to schedule, drag and drop the trip to the date you want to schedule the trip for.</p>"
	},
	{
		question: "How do I timetable a trip?",
		answer: "<p>Calendar > <a href='#scheduling' alt='Scheduling'>Scheduling</a></p><p>Select a trip you wish to schedule, drag and drop the trip to the date you want to schedule the trip for.</p><p>Once the trip is on the calendar, double click on the trip you wish to schedule. Then you can create your trip timetable schedule. By default, any timetable will repeat over 1 year and 6 months unless you specify when the timetable should end.</p>"
	},
	{
		question: "How do I create a Education Course?",
		answer: "<p>First, you need to create the Education Class (Management > <a href='#classes' alt='Classes'>Classes</a>).</p><p> Then you can build your Course (Management > <a href='#courses' alt='Courses'>Courses</a>) by selecting which class is included and which trip the Open Water practicals conducted on. Set the Course price and then click ‘Save’.</p><p> Don’t forget to <a href='#scheduling' alt='Scheduling'>schedule</a> your Classes and Trips!"
	},
	{
		question: "How do I cancel a Reservation?",
		answer: "<p>Manage Bookings > Search for Reservation > Expand Reservation Details > Transaction</p><p>Under the refund section of that reservation transaction, you will need to declare how the customer is being refunded. To cancel the reservation, ensure you check the ‘Cancel Booking’ checkbox. You can also cancel a reservation by clicking the ‘Cancel’ button when you expand the reservation details. However, you will still need to refund the customer to ensure you do not create a discrepancy between scubawhereRMS and your bank account.</p>"
	},
	{
		question: "How do I edit a Reservation?",
		answer: "<p><a href='#manage-bookings' alt='Manage Bookings'>Manage Bookings</a> > Search for Reservation > Expand Reservation Details > View & Edit</p><p>When you click on View and Edit, the reservation is returned to the Add Booking process, where you can edit the booking accordingly and proceed as if it was a new booking.</p>"
	},
	{
		question: "How do I update my Travel Agents?",
		answer: "<p>Management > <a href='#agents' alt='Agents'>Agents</a></p><p>You can update the agent who supply you with bookings when you click on the agent. Remember to click ‘Save’.</p><p>In a re-cap of Business Terms,</p><p>Full Amount means the agent takes the full amount from the customer, which you can send an invoice to or as per your agreed arrangement to getting paid from the agent.</p><p>Deposit Only means the agent purely takes their commission and you need to collect the remaining balance from the customer directly.</p><p>Banned means you no longer are accepting bookings from that agent and effectively block them from generating you custom.</p>"
	},
	{
		question: "What if I don’t have a boat and operate dives from a pontoon/ beach or something similar?",
		answer: "No worries! Simply refer to the boat as ‘Beach’ or whatever you dive from. You can call it whatever you like and set the overall capacity to what you feel is appropriate."
	},
	{
		question: "Something is not working how I would expect it too...",
		answer: "We’re really sorry about that! scubawhereRMS is in BETA development and sad bugs do occur. Please use the contact form on the <a href='#dashboard' alt='Dashboard'>dashboard</a> to report the bug in as much detail as possible. We’ll be in contact with you if we need any further information."
	},
	{
		question: "I’d like to make a suggestion",
		answer: "Fantastic! Please make your suggestion via the feedback form on the <a href='#dashboard' alt='Dashboard'>dashboard</a> - we’re keen to hear from you! "
	},
	{
		question: "I have more questions and would like to speak to someone",
		answer: "Sure! You can contact us through the Contact Form on <a href='#dashboard' alt='Dashboard'>dashboard</a>. You can also e-mail us at support@scubawhere.com or contact us via Skype under the alias 'scubawherethomas'."
	},
];

$(function() {
	var faqTemplate = Handlebars.compile($('#faq-template').html());
	$('#accordion').html(faqTemplate({faq: faq}));
});
