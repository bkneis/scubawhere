var total_tabs = 0;
//var customer_count = 1;

$(function() {
    //change to global variable

    // initialize first tab
    total_tabs++;
    //addtab(total_tabs);
    addLeadTab(total_tabs);

    $("#addtab, #litab").click(function() {
        total_tabs++;
        addtab(total_tabs);
        return false;
    });

    /*function clearChecks(count) {

        var checkLead = document.getElementById("is_lead"+count).checked;

        var choice;

        if(checkLead) {
            choice = true;
        } else {
            choice = false;
        }

        var i;
        var isLead;
        for(i=0; i < totsl_tabs; i++){
            if(i != count){
                isLead = document.getElementbyID("is_lead"+i);
                isLead.disabled = choice;
            } 
        }

    }*/

    function addtab(count) {
		var tabform = ['<form id="customer'+count+'">',
        //'<input type="checkbox" id="is_lead'+count+'" value="1" onClick=""><label id="is_lead_label'+count+'" for="is_lead'+count+'" style="font-size:10pt; padding-left:5px; padding-right:15px">Lead Customer</label>',
        '<input type="text" name="fname" id="fname'+count+'" placeholder="First Name" />',
		'<input type="text" name="lname" id="lname'+count+'" placeholder="Last Name" />',
		'<input type="text" name="phone" id="phone'+count+'" placeholder="Contact Number" />',
		'<input type="text" name="email" class="email" id="email'+count+'" placeholder="Email Address" />',
		'<select id="country'+count+'" ><option>Please select country of origin</option></select>',
        '<button id="add-cust-'+count+'" onclick="addCustomer('+count+', 0)">Add customer</button>',
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
            var x;
            if (confirm("Are you sure you want to delete this customer?") == true) {
                /*var cust = document.getElementById("customers");
                for (var i=0; i<cust.length; i++){
                  if (cust.options[i].value == 'A' )
                     cust.remove(i);
                  }*/
            }
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

    function addLeadTab(count) {
        var tabform = ['<form id="customer'+count+'">',
        //'<input type="checkbox" id="is_lead'+count+'" value="1" onClick=""><label id="is_lead_label'+count+'" for="is_lead'+count+'" style="font-size:10pt; padding-left:5px; padding-right:15px">Lead Customer</label>',
        //'<p>Please fill in all details for the lead customer</p>',
        '<input type="text" name="fname" id="fname'+count+'" placeholder="First Name" />',
        '<input type="text" name="lname" id="lname'+count+'" placeholder="Last Name" />',
        '<input type="text" name="phone" id="phone'+count+'" placeholder="Contact Number" />',
        '<input type="text" name="email" id="email'+count+'" placeholder="Email Address" />',
        '<select id="leadCountry" ><option>Please select country of origin</option></select>',
        '<button id="add-cust-'+count+'" onclick="addCustomer('+count+', 1)">Add customer</button>',
        '</form>',
        '<div>',
        //'<ul id="assigned-tickets'+count+'"></ul>',
        //'<input type="text" name="valid" id="valid'+count+'" value="0"/>',
        '<input type="text" name="cust-id" id="cust-id-'+count+'" value=""/>',
        '</div>'];

        //customer_count++;
        
        var closetab = '<a href="" id="close'+count+'" class="close">&times;</a>';
        $("#tabul").append('<li id="t'+count+'" class="ntabs">Lead Customer &nbsp;&nbsp;'+closetab+'</li>');
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
            /*var x;
            if (confirm("Are you sure you want to delete this customer?") == true) {
                var cust = document.getElementById("customers");
                cust.remove(cust.selectedIndex);
            }
            // activate the previous tab
            $("#tabul li").removeClass("ctab");
            //customer_count - 1;
            //total_tabs--;
            $("#tabcontent p").hide();
            $(this).parent().prev().addClass("ctab");
            $("#c"+count).prev().fadeIn('slow');

            $(this).parent().remove();
            $("#c"+count).remove();
            */
            alert("Sorry, you cannot delete the lead customer");
            return false;
        });
    }//add tab function

});// JavaScript Document
