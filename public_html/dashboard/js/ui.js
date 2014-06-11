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
			$(this).parent().parent().smoothRemove();
		}
	});
});

function checkDefaultSwitches(){
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
    var sure = true;

	if($(this).attr("data-sure")){
		var sure = confirm($(this).attr("data-sure"));
	}

	return sure;
}

$.fn.smoothRemove = function(){
    $(this).animate({height: 0, opacity: 0}, 'slow', function() {
        $(this).remove();
    });
}

//display error message for use when validating form
$.fn.errorMssg = function(mssg){
    $(this).after("<div class='errorMssg'>" + mssg + "</div>");
}

function pageMssg(mssg, bool){

	if(bool==true){
		$('#pageMssg').html("<span class='greenf'>" + mssg + "</span>");
	}else{
		$('#pageMssg').html("<span class='redf'>" + mssg + "</span>");
	}

	$('#pageMssg').fadeIn("slow").delay(2500).fadeOut("slow");

}
