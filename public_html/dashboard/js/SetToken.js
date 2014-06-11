$(function(){
	if(window._token)
		$("[name='_token']").val(_token);

	$.ajax({
		url: "/token",
		type: "GET",
		dataType: "html",
		async: false,
		success: function(_token){
			$("[name='_token']").val(_token);
			window._token = _token;
		}
	});
});
