$(function() {
    var total_tabs = 0;

    // initialize first tab
    total_tabs++;
    addtab(total_tabs);

    $("#addtab, #litab").click(function() {
        total_tabs++;
        addtab(total_tabs);
        return false;
    });

    function addtab(count) {
		var tabform = ['<input type="text" name="fname" id="fname'+count+'" placeholder="First Name" />',
		'<input type="text" name="lname" id="lname'+count+'" placeholder="Last Name" />',
		'<input type="text" name="phone" id="phone'+count+'" placeholder="Contact Number" />',
		'<input type="text" name="email" id="email'+count+'" placeholder="Email Address" />',
		'<select id="country'+count+'"><option>Please select country of origin</option></select>',
		'<a id="addtrip'+count+'" class="fancybox fancybox.iframe" href="tabs/add-booking/iframe.html" style="float:left; padding-top:10px;">Add Trip</a>'];
		
        var closetab = '<a href="" id="close'+count+'" class="close">&times;</a>';
        $("#tabul").append('<li id="t'+count+'" class="ntabs">Customer '+count+'&nbsp;&nbsp;'+closetab+'</li>');
		$("#tabcontent").append(tabform);

        $("#tabul li").removeClass("ctab");
        $("#t"+count).addClass("ctab");
		
		$("#tabcontent input").hide();
		$("#tabcontent select").hide();
		$("#tabcontent a").hide();
		$("#fname"+count).fadeIn('slow');
		$("#lname"+count).fadeIn('slow');
		$("#phone"+count).fadeIn('slow');
		$("#email"+count).fadeIn('slow');
		$("#country"+count).fadeIn('slow');
		$("#addtrip"+count).fadeIn('slow');

        $("#t"+count).bind("click", function() {
            $("#tabul li").removeClass("ctab");
            $("#t"+count).addClass("ctab");
			$("#tabcontent input").hide();
		$("#tabcontent select").hide();
		$("#tabcontent a").hide();
		$("#fname"+count).fadeIn('slow');
		$("#lname"+count).fadeIn('slow');
		$("#phone"+count).fadeIn('slow');
		$("#email"+count).fadeIn('slow');
		$("#country"+count).fadeIn('slow');
		$("#addtrip"+count).fadeIn('slow');
        });

        $("#close"+count).bind("click", function() {
            // activate the previous tab
            $("#tabul li").removeClass("ctab");
			//total_tabs--;
            $("#tabcontent p").hide();
            $(this).parent().prev().addClass("ctab");
            $("#c"+count).prev().fadeIn('slow');

            $(this).parent().remove();
            $("#c"+count).remove();
            return false;
        });
    }//add tab function
	
	function addElement() {

  var ni = document.getElementById('myDiv');

  var numi = document.getElementById('theValue');

  var num = (document.getElementById('theValue').value -1)+ 2;

  numi.value = num;

  var newdiv = document.createElement('div');

  var divIdName = 'my'+num+'Div';

  newdiv.setAttribute('id',divIdName);

  newdiv.innerHTML = 'Element Number '+num+' has been added! <a href=\'#\' onclick=\'removeElement('+divIdName+')\'>Remove the div "'+divIdName+'"</a>';

  ni.appendChild(newdiv);

}

});// JavaScript Document