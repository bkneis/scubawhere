$(function() {
	// This is just a bad example. This should be made into a Report.js controller!

	// Fetch all from the first quarter of 2015
	var params = {
		after: '2015-01-01',
		before: '2015-05-01', // This date is EXCLUSIVE, so it needs to be one day AFTER the final date that should be included
	};

	$.ajax({
		url: '/api/report',
		data: params,
		success: function(data) {
			$('code').text(JSON.stringify(data, null, 4));
		}
	});
});
