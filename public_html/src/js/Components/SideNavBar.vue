<template>
    <div id="sidebar">
        <!-- Navigation including accordion drop down menus -->
        <ul id="sidenav">
            <li>
                <a href="/dashboard/#dashboard">
                    <i class="fa fa-tachometer fa-lg fa-fw"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/dashboard/#add-booking">
                    <i class="fa fa-plus fa-lg fa-fw"></i>
                    <span>Add Booking</span>
                </a>
            </li>
            <li>
                <a href="/dashboard/#manage-bookings">
                    <i class="fa fa-pencil fa-lg fa-fw"></i>
                    <span>Manage Bookings</span>
                </a>
            </li>
            <!--<nav-link url="/dashboard" icon="fa-tachometer" name="Dashboard"/>
            <nav-link url="/add-booking" icon="fa-plus" name="Add Booking"/>
            <nav-link url="/manage-booking" icon="fa-pencil" name="Manage Booking"/>-->

            <nav-menu icon="fa-calendar" name="Calendar">
                <li>
                    <a href="/dashboard/#calendar">Calendar</a>
                </li>
                <li>
                    <a href="/dashboard/#scheduling">Scheduling</a>
                </li>
                <li>
                    <a href="/dashboard/#pickup-schedule">Pick-Up Schedule</a>
                </li>
                <!--<nav-link url="/calendar" name="Calendar"/>
                <nav-link url="/scheduling" name="Scheduling"/>
                <nav-link url="/pickup-schedule" name="Pickup Schedule"/>-->
            </nav-menu>

            <nav-menu icon="fa-paper-plane" name="CRM">
                <li>
                    <a href="/dashboard/#customers">My Customers</a>
                </li>
                <li>
                    <a href="/dashboard/#mailing-lists">My Mailing Lists</a>
                </li>
                <li>
                    <a href="/dashboard/#campaigns">My Campaigns</a>
                </li>
                <!--<nav-link url="/customers" name="Customers"/>
                <nav-link url="/mailing-lists" name="Mailing lists"/>
                <nav-link url="/campaigns" name="Campaigns"/>-->
            </nav-menu>

            <li>
                <a href="/dashboard/#reports">
                    <i class="fa fa-line-chart fa-lg fa-fw"></i>
                    <span>Reports</span>
                </a>
            </li>
            <!--<nav-link url="reports" icon="fa-line-chart" name="Reports"/>-->

            <nav-menu :show="true" icon="fa-sitemap" name="Management">
                <nav-link url="/accommodations" name="Accommodations"/>
                <li>
                    <a href="/dashboard/#add-ons">Add-ons</a>
                </li>
                <li>
                    <a href="/dashboard/#agents">Agents</a>
                </li>
                <li>
                    <a href="/dashboard/#boats">Boats</a>
                </li>
                <li>
                    <a href="/dashboard/#classes">Classes</a>
                </li>
                <li>
                    <a href="/dashboard/#courses">Courses</a>
                </li>
                <li>
                    <a href="/dashboard/#locations">Locations</a>
                </li>
                <li>
                    <a href="/dashboard/#packages">Packages</a>
                </li>
                <li>
                    <a href="/dashboard/#tickets">Tickets</a>
                </li>
                <li>
                    <a href="/dashboard/#trips">Trips</a>
                </li>
                <!--<nav-link url="/add-ons" name="Add-ons"/>
                <nav-link url="/agents" name="Agents"/>
                <nav-link url="/boats" name="Boats"/>
                <nav-link url="/classes" name="Classes"/>
                <nav-link url="/courses" name="Courses"/>
                <nav-link url="/locations" name="Locations"/>
                <nav-link url="/packages" name="Packages"/>
                <nav-link url="/tickets" name="Tickets"/>
                <nav-link url="/trips" name="Trips"/>-->
            </nav-menu>

            <nav-menu icon="fa-cog" name="Settings">
                <li>
                    <a href="/dashboard/#settings">Account</a>
                </li>
                <li>
                    <a href="/dashboard/#settings-users">Users</a>
                </li>
                <!--<nav-link url="/settings/account" name="Account"/>
                <nav-link url="/settings/users" name="Users"/>-->
            </nav-menu>

            <li>
                <a href="/dashboard/#troubleshooting">
                    <i class="fa fa-exclamation fa-lg fa-fw"></i>
                    <span>Troubleshooting</span>
                </a>
            </li>
            <!--<nav-link url="/troubleshooting" icon="fa-exclamation" name="Troubleshooting"/>-->
        </ul>
    </div>
</template>
<script type="text/babel">
    import NavLink from 'Components/NavLink.vue';
    import NavMenu from 'Components/NavMenu.vue';
    export default {
        name : 'side-nav-bar',
        components : {
            NavLink,
            NavMenu
        }
    }
</script>
