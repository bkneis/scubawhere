$(function() {
	
		/* initialize the external events
		-----------------------------------------------------------------*/
	
		$('div.trip-event').each(function() {
		
			// create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
			// it doesn't need to have a start or end
			var eventObject = {
				title: $.trim($(this).text()), // use the element's text as the event title
				start: "2009-11-05T13:15:30Z",
				
			};
			
			// store the Event Object in the DOM element so we can get to it later
			$(this).data('eventObject', eventObject);
			
			// make the event draggable using jQuery UI
			$(this).draggable({
				zIndex: 999,
				revert: true,      // will cause the event to go back to its
				revertDuration: 0,  //  original position after the drag
				helper:"clone",
				containment:"document"
			});
			
		});
		
		
		
		/* initialize draggable boats
		-----------------------------------------------------------------*/
		$('li.boat-drag').each(function() {
			// make the event draggable using jQuery UI
			$(this).draggable({
				zIndex: 999,
				revert: true,      // will cause the event to go back to its
				revertDuration: 0  //  original position after the drag
			});
			
			
		});
		
		$('li.droppable-event').each(function() {
			$(this).droppable({
				
			    drop:function(event, ui) {
			    	ui.draggable.addClass("trip-boat");
			        ui.draggable.appendTo($(this).children("ul"));
			    }
			});
		});
		


	
	
		/* initialize the calendar
		-----------------------------------------------------------------*/
		
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			
			editable: true,
			droppable: true, // this allows things to be dropped onto the calendar !!!
			drop: function(date) { // this function is called when something is dropped
				
				
				
				// retrieve the dropped element's stored Event Object
				var originalEventObject = $(this).data('eventObject');
				
				
				
				// we need to copy it, so that multiple events don't have a reference to the same object
				var copiedEventObject = $.extend({}, originalEventObject);
				
				var duration = $(this).attr('data-duration');
				
				
				
				// assign it the date that was reported
				copiedEventObject.start = date;
				copiedEventObject.end = moment(date, "dddd, MMMM Do YYYY, h:mm:ss a").add('days', duration).add('hours', duration);
				
				console.log(copiedEventObject.start);
				console.log(copiedEventObject.end);
				
				
				
				checkOverlap(copiedEventObject);
				
				// render the event on the calendar
				// the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
				$('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
				
				// is the "remove after drop" checkbox checked?
				if ($('#drop-remove').is(':checked')) {
					// if so, remove the element from the "Draggable Events" list
					$(this).remove();
				}
				
			}
			
		});
		
		/*	 HACK	 */
        setTimeout(function() {
			$('#calendar').fullCalendar( 'today' );
		}, 100);
		
			 
		});
		

	
	
	
	function checkOverlap(event) {  

	    var start = new Date(event.start);
	    var end = new Date(event.end);
	
	    var overlap = $('#calendar').fullCalendar('clientEvents', function(ev) {
	        if( ev == event)
	            return false;
	        var estart = new Date(ev.start);
	        var eend = new Date(ev.end);
	
	        return (Math.round(estart)/1000 < Math.round(end)/1000 && Math.round(eend) > Math.round(start));
	    });
	
	    if (overlap.length){  
	            alert("Overlap");
	       }                  
	  }