$(function(){
	$( "#regSubmit" ).click(function(e) {
		
		var error = false;
		
		//reset the form error
		$("#form-error").html("");
		
		//reset all required fields to default grey
		$(".required").each(function(){
				$(this).css("border-bottom-color", "#c8c8c8");		
		});
		
		//get form input data to validate
		var username = $('[name="username"]').val();
		var name = $('[name="name"]').val();
		var email = $('[name="email"]').val();
		var address_1 = $('[name="address_1"]').val();
		var address_2 = $('[name="address_2"]').val();
		var city = $('[name="city"]').val();
		var county = $('[name="county"]').val();
		var postcode = $('[name="postcode"]').val();
		var phone = $('[name="phone"]').val();
		var website = $('[name="website"]').val();

		
		//if the form isnt complete
		if((!username) || (!email) || (!name) || (!address_1) || (!address_2) || (!city) || (!county) || (!postcode) || (!phone)){
			//form isnt complete
			error = true
			$("#form-error").html("Please complete the form.");
			$(".required").each(function(){
				if($(this).val().length < 1){
					$(this).css("border-bottom-color", "#FF7163");
				}
			});
			
		}else{
			
			//check name is not already used and 
			//>= 5 chars
			if(name.length >= 5){
				
			}else{
				error = true;
				$('[name="name"]').errorMssg("Please enter at least 5 characters.");
			}
			
			//check username is not already used and 
			//>= 5 chars
			if(username.length >= 5){
				
			}else{
				error = true;
				$('[name="username"]').errorMssg("Please enter at least 5 characters.");
			}
			
			//check email is an email and
			//isnt already used
			if(!isEmail(email)){
				//its not an email address
				error = true;
				$('[name="email"]').errorMssg("Invalid email.");
			}
			
						
		}

		if(error == true){
			$( "form" ).effect( "shake" );
			
		}else{
			//submit the form
			$.ajax({
				url: "/register/company",
				type: "POST",
				dataType: "json",
				data: $("form#regForm").serialize(),
				success: function(data){
					console.log(data.status);
				},
				error: function(err){
					console.log(err);
				}
			});
		}
		
		e.preventDefault();
	  	
	});
});

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

$.fn.errorMssg = function(mssg){
    $(this).after("<div class='errorMssg'>" + mssg + "</div>");
}