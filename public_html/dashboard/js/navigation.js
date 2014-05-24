var LOADER = "<div class='loading-wrap'><img src='img/loader.gif'></div>";
$(function() {
    var newHash      = "",
        $mainContent = $("#content"),
        $pageWrap    = $("#page-wrap"),
        baseHeight   = 0,
        $el;

    $pageWrap.height($pageWrap.height());
    baseHeight = $pageWrap.height() - $mainContent.height();

    $("#sidenav").delegate("[data-load]", "click", function(e) {

        window.location.hash = $(this).attr("data-load");
        e.preventDefault();
    });

    $(window).bind('hashchange', function(){

    	$("#wrapper").html(LOADER);

        newHash = window.location.hash.substring(1);

        // inserted by soren
        if(newHash === '') newHash = 'dashboard';

        var newTitle = $('[data-load="'+newHash+'"]').html();
        $("#content-title").html(newTitle);

        // set live tab
        $('[data-load]').removeClass('tab-active');
        $('[data-load="'+newHash+'"]').addClass('tab-active');

        newHash = "tabs/" + newHash + "/index.php";

        if (newHash) {
            $mainContent
                .find("#wrapper")
                .fadeOut(200, function() {
                    $mainContent.hide().load(newHash, function() {
                        $mainContent.fadeIn(200, function() {
                            $pageWrap.animate({
                                height: baseHeight + $mainContent.height() + "px"
                            });
                        });
                    });
                });


        };

    });

    $(window).trigger('hashchange');

});

/* ACCORDION NAVIGATION */
$(function(){
	//set the down arrows by default
	$( '.arrow' ).html( '&#x25BC;' );

	//function fires if any of the nav-items tags are clicked
	$( "#sidenav > li > div" ).click(function(){
		//show child list if not already shown
		if ($(this).parent().children().is( ":hidden" ) ) {
  			$( $( this ).parent().children( "ul" ) ).slideDown( "fast" );
  			//set arrow to up
  			$( $(this).children( ".arrow" ) ).html( '&#x25B2;' );
  		} else {
  			//list already on show so slide it back up
	    	$( $( this ).parent().children( "ul" ) ).slideUp( "fast" );
	    	//set arrow to down
	    	$( $( this ).children( ".arrow" ) ).html( '&#x25BC;' );
		}
	});
});

