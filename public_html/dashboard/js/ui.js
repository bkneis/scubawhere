var LOADER = '<div class="loader" style="left: 50%; margin-left: -13px; margin-top: 10em;"></div>';

/*$(window).on('hashchange', function(e) {
	hashHistory.push(window.location.hash);
	if (hashHistory.length > 2) {
		hashHistory.splice(0,hashHistory.length-2);
	}
	if(!window.skipSavedBooking) {
		if(hashHistory[0] === '#add-booking' && !(_.isEmpty(window.booking))) {
			bootbox.confirm({
				title   : 'Save booking?',
				message : 'Would you like to save that booking to return to later?',
				buttons : {
					cancel : {
						label : 'No',
						class : 'btn-danger'
					},
					confirm : {
						label : 'Yes',
						class : 'btn-success'
					}
				},
				callback : function (result) {
					if(!result) {
						window.booking = {};
					}
				}
			});
		}
	} else {
		window.skipSavedBooking = false;
	}
	if(window.location.hash === '#add-booking') {
		if(typeof window.booking !== 'undefined' && !(_.isEmpty(window.booking))) {
			Booking.startEditing(window.booking.id, function success(object) {
				window.booking = object;
				window.booking.mode   = 'edit';
				window.booking.status = 'temporary';
				window.clickedEdit  = true;
			});
		}
	}
});*/

$(function(){

	if(!(/chrom(e|ium)/.test(navigator.userAgent.toLowerCase()))){
		pageMssg('Our system detects that you are not using google chrome. So that we can give you the best experience possible, please download the latest google chrome :)', 'warning', true);
	}

	//click function used for in tab switch
	//content is loaded into section
	$('#guts').delegate(".switch-option", "click", function(){
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 005 found"'); // 2015-02-18

		$('.switch-option').removeClass('option-active');
		$(this).addClass('option-active');

		//which section the switch is for
		var section = "#" + $(this).parent().attr("for");
		//get the load doc
		var doc = $(this).attr("id");
		//set the new content
		$(section).html(LOADER).load(doc);
	});


	//tooltip for hints
	$("body").on("focus", "[data-tooltip]", function() {
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 006 found"'); // 2015-02-20

    	var tooltip = $("[data-tooltip]").attr("data-tooltip");

    	//remove all other tool tips
    	$(".tooltip").remove();

    	//append the tooltip
    	$("[data-tooltip]").parent().append("<div class='tooltip'>"+tooltip+"</div>");

    	$(".tooltip").fadeIn("slow");

    	//get the inputs offset on page
    	var offset = $("[data-tooltip]").offset();

    	//get height of tooltip
    	var elHeight = $(".tooltip").height();

    	//set the new offset of tooltip
    	$( ".tooltip" ).offset({ top: (offset.top - 40 - elHeight), left: offset.left });
	});

	//tooltip for hints
	$("body").on("focusout", "[data-tooltip]", function() {
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 007 found"'); // 2015-02-20
		//remove all tool tips
		$(".tooltip").fadeOut("slow");
    	/* $(".tooltip").remove(); */
	});

	/*
	* Datepicker
	*/

	$('input.datetimepicker').datetimepicker({
		pickDate: true,
		pickTime: true,
		minuteStepping: 5,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
	});

	$('input.datepicker').datetimepicker({
		pickDate: true,
		pickTime: false,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
	});

	$('input.timepicker').datetimepicker({
		pickDate: false,
		pickTime: true,
		minuteStepping: 5,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
	});

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
														//BOX FUNCTIONS
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	//EXPANDABLE BOX / SPACE-SAVER BOX
	$("body").delegate(".expand-box-arrow", "click", function(){
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 008 found"'); // 2015-02-20
		$(this).parent().parent().children(".expandable").slideToggle();
		$(this).toggleClass("rotate");
	});

	//DELETABLE BOX
	$("body").delegate(".del-box", "click", function(){
		alert('If you see this alert, please contact Soren with the following message: "Tombstone 009 found"'); // 2015-02-20
		if($(this).isSure()){
			$(this).parent().parent().smoothRemove(function() {
				// Trigger saveAll
				$('#saveAll').click();
			});
		}
	});
});

function initPriceDatepickers() {
	var today = new Date();
	today.setDate(today.getDate() -1); // @todo create global options for moment to use the DO's time zone
	$('input.datepicker').not('.datepicker-initiated').addClass('datepicker-initiated').datetimepicker({
		minDate: today,
		pickDate: true,
		pickTime: false,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
	});
}

function checkDefaultSwitches(){
	alert('If you see this alert, please contact Soren with the following message: "Tombstone 001 found"'); // 2014-12-31
	//if there is a switch on the page set its default content
	//do when the new content is loaded
	if($(".option-active").length > 0){
		var activeOptions = $(".option-active");
		activeOptions.each(function() {
	    //which section the switch is for
				var section = "#" + $(this).parent().attr("for");
				//get the load doc
				var doc = $(this).attr("id");
				//set the new content
				$(section).html(LOADER).load(doc);
		});
	}
}

$.fn.isSure = function(){
	alert('If you see this alert, please contact Soren with the following message: "Tombstone 002 found"'); // 2014-12-31
	var sure = true;

	if($(this).attr("data-sure")){
		sure = confirm($(this).attr("data-sure"));
	}

	return sure;
};

$.fn.smoothRemove = function(callback){
	alert('If you see this alert, please contact Soren with the following message: "Tombstone 003 found"'); // 2014-12-31
    $(this).animate({height: 0, opacity: 0}, 'slow', function() {
        $(this).remove();

        if(callback !== undefined && typeof callback === "function")
        {
        	callback();
        }
    });
};

//display error message for use when validating form
$.fn.errorMssg = function(mssg){
	alert('If you see this alert, please contact Soren with the following message: "Tombstone 004 found"'); // 2014-12-31
    $(this).after("<div class='errorMssg'>" + mssg + "</div>");
};

function pageMssg(message, type, dismissable) {

	if(typeof type === 'undefined')
		type = 'danger';
	if(typeof type === 'boolean')
		type = 'success';

	if(typeof dismissable === 'undefined')
		dismissable = false;

	var el = '<div class="findMe alert alert-' + type + ' border-' + type + (dismissable ? ' alert-dismissable' : '') + '" role="alert">';

	if(dismissable)
		el += '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';

	switch(type) {
		case 'success': el += '<i class="fa fa-check fa-lg fa-fw"></i> '; break;
		case 'info':    el += '<i class="fa fa-info fa-lg fa-fw"></i> ';  break;
		case 'warning': el += '<i class="fa fa-exclamation fa-lg fa-fw"></i> '; break;
		case 'danger':  el +=  '<i class="fa fa-times fa-lg fa-fw"></i> '; break;
	}

	el += message;

	el += '</div>';

	$('#pageMssg').append(el).find('.findMe').removeClass('findMe').fadeIn(400, function() {
		if($(this).hasClass('alert-dismissable'))
			return;

		var self = this;

		setTimeout(function() {
			$(self).fadeOut(400, function() {
				$(this).remove();
			});
		},3000);
	});
}

// disable mousewheel on a input number field when in focus
// (to prevent Cromium browsers change the value when scrolling)
$('form').on('focus', 'input[type=number]', function (e) {
	$(this).on('mousewheel.disableScroll', function (e) {
		e.preventDefault();
	});
});
$('form').on('blur', 'input[type=number]', function (e) {
	$(this).off('mousewheel.disableScroll');
});