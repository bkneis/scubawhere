$(function() {
    var newHash      = "",
        $mainContent = $("#content"),
        $pageWrap    = $("#page-wrap"),
        baseHeight   = 0,
        $el;

    /*
    $pageWrap.height($pageWrap.height());
    baseHeight = $pageWrap.height() - $mainContent.height();
    */

    $(window).on('hashchange', function() {

        newHash = window.location.hash.substring(1); // Fetch hash without #

        // Default to dashboard when no hash found
        if(newHash === '') {
            window.location.hash = 'dashboard';
            return;
        }

        // Prepare deferred
        var tabLoaded = $.Deferred();

        // Fire off AJAX to load new content
        var newContentUrl = "tabs/" + newHash + "/index.php";
        $.ajax({
            url: newContentUrl,
            type: "GET",
            success: function(data) {
                tabLoaded.resolve(data);
            },
        });

        // Set live tab
        $('.tab-active').removeClass('tab-active');
        $('#sidenav a[href="#'+newHash+'"]').parent().addClass('tab-active');

        // Open Management submenu if one of its tabs is selected
        submenu = [
            'accommodations',
            'activate-trip',
            'add-ons',
            'agents',
            'boats',
            'locations',
            'packages',
            'tickets',
            'trips'
        ];
        if(submenu.indexOf(newHash) !== -1)
            $('#management-submenu').css('display', 'block');

        // Blend out old content and display new content
        $mainContent.find('#wrapper').fadeOut(200, function() {
            $('#wrapper').remove();

            if(tabLoaded.state() === "pending")
                $mainContent.html(LOADER);

            tabLoaded.done(function(html) {
                $mainContent.html(html);
            });
        });

        // Get the page title from the menu item
        var newTitle = $('#sidenav a[href="#'+newHash+'"]').text();

        // Set breadcumb(s)
        if(newHash === 'add-transaction')
            newTitle = '<a href="#manage-bookings">Manage Bookings</a> <small><i class="fa fa-chevron-right fa-fw text-muted"></i></small> Add Transaction';
        $("#breadcrumbs").html('<a href="#dashboard" class="breadcrumbs-home"><i class="fa fa-home fa-lg fa-fw"></i></a> <small><i class="fa fa-chevron-right fa-fw text-muted"></i></small> ' + newTitle);
    });

    // Trigger content loading on initial dashboard load
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
