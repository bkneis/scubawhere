var LOADER = '<div class="loader" style="left: 50%; margin-left: -13px; margin-top: 10em;"></div>';

$(function(){

	//click function used for in tab switch
	//content is loaded into section
	$('#guts').delegate(".switch-option", "click", function(){
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
		minuteStepping: 5
	});

	$('input.datepicker').datetimepicker({
		pickDate: true,
		pickTime: false
	});

	$('input.timepicker').datetimepicker({
		pickDate: false,
		pickTime: true,
		minuteStepping: 5
	});

	$(document).on('focus', '.datepicker', function(){
		$(this).data("DateTimePicker").show();
	});

	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
														//BOX FUNCTIONS
	//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	//EXPANDABLE BOX / SPACE-SAVER BOX
	$("body").delegate(".expand-box-arrow", "click", function(){
		$(this).parent().parent().children(".expandable").slideToggle();
		$(this).toggleClass("rotate");
	});

	//DELETABLE BOX
	$("body").delegate(".del-box", "click", function(){
		if($(this).isSure()){
			$(this).parent().parent().smoothRemove(function() {
				// Trigger saveAll
				$('#saveAll').click();
			});
		}
	});
});

function checkDefaultSwitches(){
	alert('If you see this alert, please contact Soren with the following message: "Tombstone 001 found"');
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
	alert('If you see this alert, please contact Soren with the following message: "Tombstone 002 found"');
    var sure = true;

	if($(this).attr("data-sure")){
		var sure = confirm($(this).attr("data-sure"));
	}

	return sure;
}

$.fn.smoothRemove = function(callback){
	alert('If you see this alert, please contact Soren with the following message: "Tombstone 003 found"');
    $(this).animate({height: 0, opacity: 0}, 'slow', function() {
        $(this).remove();

        if(callback !== undefined && typeof callback === "function")
        {
        	callback();
        }
    });
}

//display error message for use when validating form
$.fn.errorMssg = function(mssg){
	alert('If you see this alert, please contact Soren with the following message: "Tombstone 004 found"');
    $(this).after("<div class='errorMssg'>" + mssg + "</div>");
}

function pageMssg(mssg, bool){

	if(bool == true){
		$('#pageMssg').html('<div class="alert alert-success border-success" role="alert">' + mssg + '</div>');
	}else{
		$('#pageMssg').html('<div class="alert alert-danger border-danger" role="alert">' + mssg + '</div>');
	}

	$('#pageMssg').fadeIn("slow").delay(2500).fadeOut("slow");

}
