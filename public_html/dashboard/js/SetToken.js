$(function(){
	$.ajax({
		url: "/token",
		type: "GET",
		dataType: "html",
		async: false,
		success: function(data){
			$("[name='_token']").val(data);
		}
	});
});