/* jshint esversion: 6 */

/** @var AvailabilityService */
var availabilityService;
/** @var DateService */
var dateService = new DateService();

/**
 * Availability table is a vue component to give the DO a glance at the state of the business
 *
 * It shows the availability for a week with color coding to if certain accommodations are booked
 *
 * @todo Use webpack and convert the global objects into es6 classes then import them.
 */
Vue.component('availability-table', {

    template : '#availability-table',

    /**
     * filterDate is a variable passed down from the availability root vue instance
     * so that the date from the filter can be used to refresh the table.
     */
    props : ['filterDate'],

    data : function () {
        return {
            dates          : dateService.getDaysInMonth(),
            accommodations : [],
            bookings       : []
        }
    },

    /**
     * Once the vue component is created, initalise the availability service
     * and pass through an instance of vue so that it can proxy data back
     * to the component.
     */
    created : function() {
        availabilityService = new AvailabilityService(this, dateService);
        availabilityService.refreshTable();
    },

    methods : {
        calcStyle : function (accomm_id, date) {
            return availabilityService.calcStyle(accomm_id, date);
        },
        getCustomer : function (accomm_id, date) {
            return availabilityService.getCustomerName(accomm_id, date);
        },
        showBookingInfo : function (accomm_id, date) {
            return availabilityService.showBookingModal(accomm_id, date);
        }
    },

    /**
     * Listen for changes to filterDate and refresh the tables rows (dates)
     * and data with the new filter.
     */
    watch : {
        filterDate : function (newVal, oldVal) {
            let date = new Date(newVal);
            let oldDate = new Date(oldVal);
            if(!date.isSameMonth(oldDate)) {
                availabilityService.refreshTable(date.getFullYear(), date.getMonth());
            }
        }
    }

});