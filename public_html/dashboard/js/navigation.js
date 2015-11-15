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
            global: false, // Do not trigger global ajax events. Helps prevent double flash of progress bar when loading a tab.
            success: function(data) {
                tabLoaded.resolve(data);
            },
        });

        // Set live tab
        $('.tab-active').removeClass('tab-active');
        $('#sidenav a[href="#'+newHash+'"]').parent().addClass('tab-active');

        // Open submenu if one of its tabs is selected
        var submenuCalendar = [
            'calendar',
            'scheduling',
            'pickup-schedule'
        ];
        var submenuCRM = [
            'customers',
            'mailing-lists',
            'campaigns'
        ];
        var submenuManagement = [
            'accommodations',
            'add-ons',
            'agents',
            'boats',
            'classes',
            'courses',
            'locations',
            'trips',
            'packages',
            'tickets'
        ];

        var submenuToOpen;

        if(  submenuCalendar.indexOf(newHash) !== -1) submenuToOpen = 'calendar-submenu';
        if(       submenuCRM.indexOf(newHash) !== -1) submenuToOpen = 'crm-submenu';
        if(submenuManagement.indexOf(newHash) !== -1) submenuToOpen = 'management-submenu';

        if(submenuToOpen) {
            $('#' + submenuToOpen).css('display', 'block');
            $('#' + submenuToOpen).siblings('div').children('.caret').css('transform', 'rotate(0deg)');
        }

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

        window.scrollTo(0, 0);

        // Send navigation event
        getToken(function() {
            Company.sendHeartbeat({'n': 1});
        });
    });

    // Trigger content loading on initial dashboard load
    $(window).trigger('hashchange');
});

/* ACCORDION NAVIGATION */
$(function(){
    // Function fires if any of the nav-items tags are clicked
    $( "#sidenav > li > div" ).click(function() {
        var $self = $(this);

        // Show child list if not already shown
        if($self.siblings('ul').is(':hidden')) {
            $self.siblings('ul').slideDown( "fast" );
            // Set arrow to down
            $self.children('.caret').css('transform', 'rotate(0deg)');
        }
        else {
            // List already on show so slide it back up
            $self.siblings('ul').slideUp( "fast" );
            // Set arrow to right
            $self.children('.caret').css('transform', 'rotate(-90deg)');
        }
    });
});
