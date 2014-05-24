$(function(){
	
	
	$( "#loginDC" ).click(function(e) {
		
		var error = false;
		
		var username = $('[name="username"]').val();
		var password = $('[name="password"]').val();
		
		
		
		if((!username)||(!password)){
			error = true;
			$("#form-error").html("Please complete the form.");
			
		}else{
			
			//>= 5 chars
			if(username.length >= 2){
				//no error
			}else{
				
				error = true;
				$("#form-error").html("The username isn't long enough.");
			}
			
			//check that password is over 6 chars
			if(password.length < 6){
				
				error = true;
				$("#form-error").html("The password isn't long enough.");
			}
		}
		
		
		if(error == true){
			$( "form" ).effect( "shake" );
			console.log(error);
		}else{
			$.ajax({
				url: "/login",
				type: "POST",
				dataType: "json",
				data: $("form#loginForm").serialize(),
				success: function(data){
					window.location.href = "/dashboard/";
				},
				error: function(err){
					$( "form" ).effect( "shake" );
				}
			});
		}
		
		e.preventDefault();
	});
});