<div id="wrapper">
	<div id="stats-row">
		<div class="metro-nav">
            <div class="metro-nav-block nav-block-orange">
                <a data-original-title="" href="#">
                    <i class="icon-user"></i>
                    <div class="info">321</div>
                    <div class="status">Agents</div>
                </a>
            </div>
            <div class="metro-nav-block nav-olive">
                <a data-original-title="" href="#">
                    <i class="icon-tags"></i>
                    <div class="info">+970</div>
                    <div class="status">Bookings</div>
                </a>
            </div>
            <div class="metro-nav-block nav-block-yellow">
                <a data-original-title="" href="#">
                    <i class="icon-user"></i>
                    <div class="info">49</div>
                    <div class="status">Customers</div>
                </a>
            </div>
            <div class="metro-nav-block nav-block-green double">
                <a data-original-title="" href="#">
                    <i class="icon-globe"></i>
                    <div class="info">+897</div>
                    <div class="status">Dives booked</div>
                </a>
            </div>
            <div class="metro-nav-block nav-block-red">
                <a data-original-title="" href="#">
                    <i class="icon-bar-chart"></i>
                    <div class="info">+288</div>
                    <div class="status">Trips</div>
                </a>
            </div>
        </div>
        <div class="metro-nav">
            <div class="metro-nav-block nav-light-purple">
                <a data-original-title="" href="#">
                    <i class="icon-tags"></i>
                    <div class="info">29</div>
                    <div class="status">Tickets</div>
                </a>
            </div>
            <div class="metro-nav-block nav-light-blue double">
                <a data-original-title="" href="#">
                    <i class="icon-tasks"></i>
                    <div class="info">$37624</div>
                    <div class="status">Packages</div>
                </a>
            </div>
            <div class="metro-nav-block nav-light-green">
                <a data-original-title="" href="#">
                    <i class="icon-flag"></i>
                    <div class="info">123</div>
                    <div class="status">Locations</div>
                </a>
            </div>
            <div class="metro-nav-block nav-light-brown">
                <a data-original-title="" href="#">
                    <i class="icon-remove-sign"></i>
                    <div class="info">34</div>
                    <div class="status">Cancelled</div>
                </a>
            </div>
            <div class="metro-nav-block nav-block-grey ">
                <a data-original-title="" href="#">
                    <i class="icon-external-link"></i>
                    <div class="info">$53412</div>
                    <div class="status">Total revenue</div>
                </a>
            </div>
        </div>
    </div>
    <div id="portlets-row">
        <div class="portlets-col">
        <!-- BEGIN Portlet PORTLET-->
        <div class="widget red">
            <div class="widget-title">
                <h4><i class="icon-edit"></i> Boat Capacity Report</h4>
                <span class="tools">
                    <a class="icon-chevron-down" onclick="slideDown('boat-capacity')"></a>
                </span>
            </div>
            <div id="boat-capacity" class="widget-body">
                <div class="scroller">
             <ul id="capacities">
                <li>
                    Boat 1 <strong class="label"> 48%</strong>
                    <div class="space10"></div>
                    <div class="progress">
                        <div style="width: 48%;" class="bar"></div>
                    </div>
                </li>
                <li>
                    Boat 2 <strong class="label"> 85%</strong>
                    <div class="space10"></div>
                    <div class="progress progress-success">
                        <div style="width: 85%;" class="bar"></div>
                    </div>
                </li>
                <li>
                    Boat 3 <strong class="label"> 65%</strong>
                    <div class="space10"></div>
                    <div class="progress progress-danger">
                        <div style="width: 65%;" class="bar"></div>
                    </div>
                </li>

            </ul>
        </div>
        </div>
    </div>
    <!-- END Portlet PORTLET-->

    <!-- BEGIN Portlet PORTLET-->
    <div class="widget yellow">
        <div class="widget-title">
            <h4><i class="icon-edit"></i> Calendar</h4>
            <span class="tools">
                <a class="icon-chevron-down" onclick="slideDown('calendar')"></a>
            </span>
        </div>
        <div id="calendar" class="widget-body">
            <button onclick="displayTodaysSessions()">sdghsh</button>
        </div>
    </div>
    <!-- END Portlet PORTLET-->

</div>

<div class="portlets-col2">

        <!-- BEGIN Portlet PORTLET-->
        <div class="widget purple">
            <div class="widget-title">
                <h4><i class="icon-edit"></i> Todays Trips</h4>
                <span class="tools">
                    <a class="icon-chevron-down" onclick="slideDown('todays-trips')"></a>
                </span>
            </div>
            <div id="todays-trips" class="widget-body">
                <div class="scroller" style="height:300px;">
                    <h1>dasd</h1>
                    <h1>dasd</h1>
                    <h1>dasd</h1>
                    <h1>dasd</h1>
                    <h1>dasd</h1>
                    <h1>dasd</h1>
                    <h1>dasd</h1>
                    <h1>dasd</h1>
                    <h1>dasd</h1>
                    <h1>dasd</h1>
                    <h1>dasd</h1>
                </div>
            </div>
        </div>
        <!-- END Portlet PORTLET-->

        <!-- BEGIN Portlet PORTLET-->
        <div class="widget blue">
            <div class="widget-title">
                <h4><i class="icon-edit"></i> Locations</h4>
                <span class="tools">
                    <a class="icon-chevron-down" onclick="slideDown('locations')"></a>
                </span>
            </div>
            <div id="locations" class="widget-body">
                <div id="map"></div>
            </div>
        </div>
        <!-- END Portlet PORTLET-->
    </div>
</div>
</div>

<link href="tabs/dashboard/css/style.css" rel="stylesheet" />
<link href="tabs/dashboard/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
<script type="text/javascript" src="tabs/locations/js/gmaps.js"></script>
<script type="text/javascript" src="tabs/dashboard/js/maps.js"></script>
<script type="text/javascript" src="tabs/dashboard/js/script.js"></script>

<!--<script type="text/javascript" src="../common/js/jquery.mCustomScrollbar.concat.min.js"></script>
<link rel="stylesheet" href="../common/css/jquery.mCustomScrollbar.css" />-->

<link rel="stylesheet" href="http://malihu.github.io/custom-scrollbar/3.0.0/jquery.mCustomScrollbar.min.css" />
<script src="http://malihu.github.io/custom-scrollbar/3.0.0/jquery.mCustomScrollbar.concat.min.js"></script>

<script>
    (function($){
        $(window).load(function(){
            $(".scroller").mCustomScrollbar({
                axis:"y",
                theme:"dark-3"
            });
        });
    })(jQuery);
</script>

<script src="/dashboard/js/Controllers/Sessions.js"></script>



