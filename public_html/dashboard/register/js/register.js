var error1;
var errorChecking = true;

function validateEmail(email){
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return !(filter.test(email));
}

function validate(sectionNum){

	var error = false;
	var section = "#section" + sectionNum;

	$( ".required", section ).each(function( index ) {
		$(this).css( "border", "2px #c8c8c8" );
	});

	if(sectionNum == 1) error = validateEmail($('[name="email"]').val());
	else if(sectionNum == 2) error = validateEmail($('[name="businessEmail"]').val());

	$( ".required", section ).each(function( index ) {
		if(!($( this ).val())) {
			$(this).css( "border", "2px solid red" );
			error = true;
		}
	});

	if(error){
		return false;
	}
	else {
		$( ".required", section ).each(function( index ) {
			$(this).css( "border", "2px #c8c8c8" );
		});
		return true;
	} 
}

function validateAccount(){
	var password = $('[name="passwd"]').val();
	var repassword = $('[name="repasswd"]').val();
	if(password == repassword) {
		return true;
	} return false;
}

function submitForm() {

	var contactName = $('[name="contactName"]').val();
	var email = $('[name="email"]').val();
	var phone = $('[name="phone"]').val();
	var diveCentreName = $('[name="name"]').val();
	var website = $('[name="website"]').val();
	var description = $('[name="description"]').val();
	var businessPhone = $('[name="businessPhone"]').val();
	var businessEmail = $('[name="businessEmail"]').val();
	var addr1 = $('[name="addr1"]').val();
	var addr2 = $('[name="addr2"]').val();
	var regNumber = $('[name="regNumber"]').val();
	var vatNumber = $('[name="vatNumber"]').val();
	var currency = $('#currencies').val();
	var country = $('#countries').val();
	var username = $('[name="username"]').val();
	var passwd = $('[name="passwd"]').val();
	var city = $('[name="city"]').val();
	var county = $('[name="county"]').val();
	var postCode = $('[name="postCode"]').val();

	var info = {
		username : username,
		password : passwd,
		email : email,
		name : diveCentreName,
		description : description,
		address_1 : addr1,
		address_2 : addr2,
		city : city,
		county : county,
		postcode : postCode,
		country_id : country, // Possibly perform look up for currency though country selection
		//currency : currency, ---- Wait til soren implements pivot to currency ids
		phone : phone,
		contact : contactName,
		website : website,
		business_phone : businessPhone,
		business_email : businessEmail,
		registration_number : regNumber,
		vat_number : vatNumber
	};

	console.log(info);

	$.ajax({
		url: "/register/company",
		type: "POST",
		dataType: "json",
		data: info,
		success: function(data){
			console.log(data.status);
			//alert('Thank you for signing up with scuba where, we have recieved your details and will verify your account shortly. Thanks');
			window.location = "http://www.scubawhere.com/dashboard/thanks";
		}
	});

}

$(function(){

	$("#example-vertical").steps({
		    headerTag: "h3",
		    bodyTag: "section",
		    transitionEffect: "slideLeft",
		    enableFinishButton: false,
		    onStepChanging : function (event, currentIndex, newIndex) {

	        // Always allow going backward even if the current step contains invalid fields!
	        if (currentIndex > newIndex)
	        {
	        	return true;
	        }

	        if(errorChecking){

		        if(currentIndex == 0){ // This is the criteria for the first step
		        	if(validate(1)) {
		        		return true;
		        	} 
		        	else return false;
		        	//return true;                     
		        }

		        if(currentIndex == 1){
		        	if(validate(2)) {
		        		return true;
		        	} 
		        	else return false;
		        	
		        	//return true;   
		        }

	    	}
	    	else return true;
	    }
	    });

	var country_select_options = '';
	$.get("/api/country/all", function(data) {
		for(var key in data) {
			country_select_options += '<option value="' + data[key].id + '">' + data[key].name + '</option>';
		}
		$('#countries').append( country_select_options );
	});

	var currency_select_options = '';
	$.get("/api/currency/all", function(data) {
		for(var key in data) {
			currency_select_options += '<option value="' + data[key].id + '">' + data[key].name + '</option>';
		}
		$('#currencies').append( currency_select_options );
	});

	var agency_options = '';
	$.get("/api/agency/all", function(data) {
		for(var key in data) {
			agency_options += '<label class="certify"><input type="checkbox" value="'+data[key].id+'"><strong>'+data[key].abbreviation+'</strong><br></label>';
		}
		$('#agencies').append( agency_options );
	});

});


						
						
					

