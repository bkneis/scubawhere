$(function() {
    var newHash      = "",
        $mainContent = $("#content"),
        $pageWrap    = $("#page-wrap"),
        baseHeight   = 0,
        contentHasLoaded = false,
        $el;

    $pageWrap.height($pageWrap.height());
    baseHeight = $pageWrap.height() - $mainContent.height();

    $("#sidenav").delegate("[data-load]", "click", function(e) {

        window.location.hash = $(this).attr("data-load");
        e.preventDefault();
    });

    $(window).bind('hashchange', function() {

        newHash = window.location.hash.substring(1); // Fetch hash without #

        // Default to dashboard when hash is removed
        if(newHash === '') newHash = 'dashboard';

        // Get the page title from the menu item
        var newTitle = $('[data-load="'+newHash+'"]').text();
        if(newHash === 'add-transaction')
            newTitle = '<a href="#manage-bookings">Manage Bookings</a> <small><i class="fa fa-chevron-right fa-fw text-muted"></i></small> Add Transaction';
        $("#breadcrumbs").html('<a href="#dashboard" class="breadcrumbs-home"><i class="fa fa-home fa-lg fa-fw"></i></a> <small><i class="fa fa-chevron-right fa-fw text-muted"></i></small> ' + newTitle);

        // set live tab
        $('[data-load]').removeClass('tab-active');
        $('[data-load="'+newHash+'"]').addClass('tab-active');

        var newContent = "tabs/" + newHash + "/index.php";

        // Blend out old content and load new content
        contentHasLoaded = false;
        $mainContent.find('#wrapper').fadeOut(200, function() {
            if(!contentHasLoaded)
                $mainContent.html(LOADER);

        });
        $mainContent.load(newContent, function() {
            window.contentHasLoaded = true;
            /*$mainContent.fadeIn(200, function() {
                $pageWrap.animate({
                    height: baseHeight + $mainContent.height() + "px",
                });
            });*/
        });
    });

    // Trigger content loading on page loading
    $(window).trigger('hashchange');

});

/* ACCORDION NAVIGATION */
$(function(){
    //function fires if any of the nav-items tags are clicked
    $( "#sidenav > li > div" ).click(function(){
        //show child list if not already shown
        if ($(this).parent().children().is( ":hidden" ) ) {
            $( $( this ).parent().children( "ul" ) ).slideDown( "fast" );
            //set arrow to up
            $( $(this).children( ".caret" ) ).css('transform', 'rotate(0deg)');
        } else {
            //list already on show so slide it back up
            $( $( this ).parent().children( "ul" ) ).slideUp( "fast" );
            //set arrow to down
            $( $( this ).children( ".caret" ) ).css('transform', 'rotate(-90deg)');
        }
    });
});
