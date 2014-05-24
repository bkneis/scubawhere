<div id="wrapper">
	
	<div class="yellow-helper">Here you can view and cancel all your activated sessions. Cancelations aren't final until you click save.</div>
	
	<div id='calendar'></div>
	
</div>

<script>

    $(document).ready(function() {
        $('#calendar').fullCalendar({
        	header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
		        eventClick: function(calEvent, jsEvent, view) {
		        
		        var r=confirm("Are you sure you want to cancel '" + calEvent.title + "'?");
				if (r==true)
				  {
					  $('#calendar').fullCalendar( 'removeEvents', calEvent.id );	
				  }
				else
				  {
				  
				  }
				console.log($('#calendar').fullCalendar('clientEvents'));
		
		    },
            defaultDate: '2014-01-12',
			editable: true,
			events: [
				{
					title: 'A Boat Trip', id: 625,
					start: '2014-01-01'
				},
				{
					title: 'A Boat Trip', id: 62,
					start: '2014-01-07',
					end: '2014-01-10'
				},
				{
					id: 324,
					title: 'A Boat Trip',
					start: '2014-01-09T16:00:00'
				},
				{
					id: 234,
					title: 'Triiiippp',
					start: '2014-01-16T16:00:00'
				},
				{
					title: 'A Boat Trip', id: 654,
					start: '2014-01-12T10:30:00',
					end: '2014-01-12T12:30:00'
				},
				{
					title: 'A Boat Trip', id: 435,
					start: '2014-01-12T12:00:00'
				},
				{
					title: 'A Boat Trip', id: 26,
					start: '2014-01-13T07:00:00'
				},
				{
					title: 'A Boat Trip', id: 2345,
					
					start: '2014-01-28'
				}
			]
            
        });
        
		/*	 HACK	 */
        setTimeout(function() {
			$('#calendar').fullCalendar( 'today' );
		}, 100);	 
		});
		
		
		
		
 
</script>


