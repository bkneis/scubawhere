$(function(){
	$("body").delegate("#add-agent", "click", function(e){

		e.preventDefault();
		
		$.ajax({
			url: "/api/agent/add",
			type: "POST",
			dataType: "json",
			data: $('form#add-agent-form').serialize(),
			async: false,
			success: function(data){
				//successfull so reload this content and show success message
				pageMssg("Agent Added, you can edit agents in the edit agent tab", true);
				
				window.location.hash = "";
				$("#wrapper").html(LOADER);
				
				window.location.hash = "add-agent";
				
			}
		});
	});
});