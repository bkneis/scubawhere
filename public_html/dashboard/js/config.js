
//REQUIRE.js utilisation
//~~~~~~~~~~~~~~~~~~~~~~

//dashboard configuration


//common files for entire site
require(["../../common/js/jquery"], function(){
	require(["../../common/js/moment.min"]);
	require(["../../common/js/jquery.cookie"]);
	require(["../../common/js/hashchange.min"]);
	require(["../../common/js/handlebars"]);
	require(["../../common/js/ui.min/jquery-ui.min"]);
	require(["../../common/ckeditor/ckeditor"]);
	require(["../../common/ckeditor/adapters/jquery"]);
	require(["../../common/js/fullcalendar.min"]);
	require(["../../common/js/gmaps"]);
	
	
	
	//common just for the dashboard
	require(["ui"]);
	require(["navigation"]);
	require(["main"]);
	require(["validate"]);
});



