'use strict';

import * as VueRouter from 'vue-router';

import * as Dashboard from 'Tabs/Dashboard.vue';
import * as AddBooking from 'Tabs/AddBooking.vue';
import * as ManageBooking from 'Tabs/ManageBooking.vue';
import * as Calendar from 'Tabs/Calendar.vue';
import * as Scheduling from 'Tabs/Scheduling.vue';
import * as PickupSchedule from 'Tabs/PickupSchedule.vue';
import * as Customers from 'Tabs/Customers.vue';
import * as MailingLists from 'Tabs/MailingLists.vue';
import * as Campaigns from 'Tabs/Campaigns.vue';
import * as Reports from 'Tabs/Reports.vue';
import * as Accommodations from 'Tabs/Accommodations.vue';
import * as Addons from 'Tabs/Addons.vue';
import * as Agents from 'Tabs/Agents.vue';
import * as Boats from 'Tabs/Boats.vue';
import * as Classes from 'Tabs/Classes.vue';
import * as Courses from 'Tabs/Courses.vue';
import * as Locations from 'Tabs/Locations.vue';
import * as Packages from 'Tabs/Packages.vue';
import * as Tickets from 'Tabs/Tickets.vue';
import * as Trips from 'Tabs/Trips.vue';
import * as AccountSettings from 'Tabs/AccountSettings.vue';
import * as UserSettings from 'Tabs/UserSettings.vue';
import * as Troubleshooting from 'Tabs/Troubleshooting.vue';

const routes = [
    { 
        path      : '/dashboard',
        component : Dashboard,
        meta : {
            breadcrumb : 'Dashboard'
        }
    },
    { 
        path      : '/add-booking',
        component : AddBooking,
        meta : {
            breadcrumb : 'Add Booking'
        }
    },
    { 
        path      : '/manage-booking',
        component : ManageBooking,
        meta : {
            breadcrumb : 'Manage Bookings'
        }
    },
    { 
        path      : '/calendar', 
        component : Calendar,
        meta : {
            breadcrumb : 'Calendar'
        }
    },
    { 
        path      : '/scheduling', 
        component : Scheduling,
        meta : {
            breadcrumb : 'Scheduling'
        }
    },
    { 
        path      : '/pickup-schedule', 
        component : PickupSchedule,
        meta : {
            breadcrumb : 'Pickup Schedule'
        }
    },
    { 
        path      : '/customers', 
        component : Customers,
        meta : {
            breadcrumb : 'Customers'
        }
    },
    { 
        path      : '/mailing-lists', 
        component : MailingLists,
        meta : {
            breadcrumb : 'Mailing Lists'
        }
    },
    { 
        path       : '/campaigns', 
        component  : Campaigns,
        meta : {
            breadcrumb : 'My Campaigns'
        }
    },
    { 
        path       : '/reports', 
        component  : Reports,
        meta : {
            breadcrumb : 'Reports'
        }
    },
    { 
        path       : '/accommodations', 
        component  : Accommodations,
        meta : {
            breadcrumb : 'Accommodations'
        }
    },
    { 
        path       : '/addons', 
        component  : Addons,
        meta : {
            breadcrumb : 'Addons'
        }
    },
    { 
        path       : '/agents', 
        component  : Agents,
        meta : {
            breadcrumb : 'Agents'
        }
    },
    { 
        path       : '/boats', 
        component  : Boats,
        meta : {
            breadcrumb : 'Boats'
        }
    },
    { 
        path       : '/classes', 
        component  : Classes,
        meta : {
            breadcrumb : 'Classes'
        }
    },
    { 
        path       : '/courses', 
        component  : Courses,
        meta : {
            breadcrumb : 'Courses'
        }
    },
    { 
        path       : '/locations', 
        component  : Locations,
        meta : {
            breadcrumb : 'Locations'
        }
    },
    { 
        path       : '/packages', 
        component  : Packages,
        meta : {
            breadcrumb : 'Packages'
        }
    },
    { 
        path       : '/tickets', 
        component  : Tickets,
        meta : {
            breadcrumb : 'Tickets'
        }
    },
    { 
        path       : '/trips', 
        component  : Trips,
        meta : {
            breadcrumb : 'Trips'
        }
    },
    { 
        path       : '/settings/account',
        component  : AccountSettings,
        meta : {
            breadcrumb : 'Account Settings'
        }
    },
    { 
        path       : '/settings/users', 
        component  : UserSettings,
        meta : {
            breadcrumb : 'User Settings'
        }
    },
    { 
        path       : '/troubleshooting', 
        component  : Troubleshooting,
        meta : {
            breadcrumb : 'Troubleshooting'
        }
    }
];

const router = new VueRouter({
    routes
});

export default router;
