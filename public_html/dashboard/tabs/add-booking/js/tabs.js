var total_tabs = 0;
//var customer_count = 1;

$(function() {
    //change to global variable

    // initialize first tab
    total_tabs++;
    addtab(total_tabs);

    $("#addtab, #litab").click(function() {
        total_tabs++;
        addtab(total_tabs);
        return false;
    });

    function addtab(count) {
		var tabform = ['<form id="customer'+count+'">',
        '<input type="checkbox" id="is_lead'+count+'" value="1"><label id="is_lead_label'+count+'" for="is_lead'+count+'" style="font-size:10pt; padding-left:5px; padding-right:15px">Lead Customer</label>',
        '<input type="text" name="fname" id="fname'+count+'" placeholder="First Name" />',
		'<input type="text" name="lname" id="lname'+count+'" placeholder="Last Name" />',
		'<input type="text" name="phone" id="phone'+count+'" placeholder="Contact Number" />',
		'<input type="text" name="email" id="email'+count+'" placeholder="Email Address" />',
		'<select id="country'+count+'" ><option>Please select country of origin</option></select>',
        '<button id="add-cust-'+count+'" onclick="addCustomer('+count+')">Add customer</button>',
        '</form>',
        '<div>',
        //'<ul id="assigned-tickets'+count+'"></ul>',
        //'<input type="text" name="valid" id="valid'+count+'" value="0"/>',
        '<input type="text" name="cust-id" id="cust-id-'+count+'" value=""/>',
        '</div>'];

        //customer_count++;
		
        var closetab = '<a href="" id="close'+count+'" class="close">&times;</a>';
        $("#tabul").append('<li id="t'+count+'" class="ntabs">Customer '+count+'&nbsp;&nbsp;'+closetab+'</li>');
		$("#tabcontent").append(tabform);

        $("#tabul li").removeClass("ctab");
        $("#t"+count).addClass("ctab");
		$("#tabcontent input").hide();
		$("#tabcontent select").hide();
		$("#tabcontent a").hide();
        $("#tabcontent label").hide();
        $("#tabcontent button").hide();
		$("#fname"+count).fadeIn('slow');
		$("#lname"+count).fadeIn('slow');
		$("#phone"+count).fadeIn('slow');
		$("#email"+count).fadeIn('slow');
		$("#country"+count).fadeIn('slow');
        $("#is_lead"+count).fadeIn('slow');
        $("#is_lead_label"+count).fadeIn('slow');
        $("#add-cust-"+count).fadeIn('slow');

        $("#t"+count).bind("click", function() {
            $("#tabul li").removeClass("ctab");
            $("#t"+count).addClass("ctab");
            //$(".hide").hide();
			$("#tabcontent input").hide();
		$("#tabcontent select").hide();
		$("#tabcontent a").hide();
        $("#tabcontent label").hide();
        $("#tabcontent button").hide();
		$("#fname"+count).fadeIn('slow');
		$("#lname"+count).fadeIn('slow');
		$("#phone"+count).fadeIn('slow');
		$("#email"+count).fadeIn('slow');
		$("#country"+count).fadeIn('slow');
        $("#is_lead"+count).fadeIn('slow');
        $("#is_lead_label"+count).fadeIn('slow');
        $("#add-cust-"+count).fadeIn('slow');
        });

        $("#close"+count).bind("click", function() {
            // activate the previous tab
            $("#tabul li").removeClass("ctab");
            //customer_count - 1;
			//total_tabs--;
            $("#tabcontent p").hide();
            $(this).parent().prev().addClass("ctab");
            $("#c"+count).prev().fadeIn('slow');

            $(this).parent().remove();
            $("#c"+count).remove();
            return false;
        });
    }//add tab function

});// JavaScript Document