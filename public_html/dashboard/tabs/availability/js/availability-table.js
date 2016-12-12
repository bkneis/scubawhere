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
 * @todo Use webpack and babel to convert the global objects into es6 modules then import them.
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
            bookings       : [],
            promises       : {
                accommodations : false
            },
            showCustomerInfo : false,
            selectedBooking : null,
            currencySymbol : window.company.currency.symbol
        };
    },

    computed : {
        bookingSource : function () {
            switch(this.selectedBooking.source) {
                case('facetoface'):
                    return 'Walk in';
                case('agent'):
                    return 'Agent booking';
                case('phone'):
                    return 'Phone booking';
                case('email'):
                    return 'Email enquiry';
                default:
                    return '';
            }
        },
        amountOutstanding : function () {
            let booking = this.selectedBooking;
            let price = booking.real_decimal_price ? booking.real_decimal_price : booking.decimal_price;
            let paid = 0;
            _.each(booking.payments, function(obj) {
                paid += parseFloat(obj.amount);
            });
            return (parseFloat(price) - paid).toFixed(2);
        },
        bookingPrice : function () {
            // @todo move this to a function accepting the booking as a param (booking service ?)
            let booking = this.selectedBooking;
            return booking.real_decimal_price ? booking.real_decimal_price : booking.decimal_price;
        },
        amountPaid : function () {
            let total = 0;
            _.each(this.selectedBooking.payments, function(obj) {
                total += parseFloat(obj.amount);
            });
            return total.toFixed(2);
        }
    },

    /**
     * Once the vue component is created, initalise the availability service
     * and pass through an instance of vue so that it can proxy data back
     * to the component.
     */
    created : function () {
        availabilityService = new AvailabilityService(this, dateService);
        availabilityService.refreshTable();
        let vm = this;
        eventHub.$on('showCustomerModal', function (data) {
            console.log(data);
            vm.selectedBooking = data;
            vm.showCustomerInfo = true;
        });
        eventHub.$on('closeCustomerModal', function () { vm.showCustomerInfo = false; });
    },

    methods : {
        calcStyle : function (accomm_id, date) {
            return availabilityService.calcStyle(accomm_id, date);
        },
        getCustomer : function (accomm_id, date) {
            return availabilityService.getCustomerName(accomm_id, date);
        },
        showBookingInfo : function (accomm_id, date) {
            let booking = this.bookings[accomm_id][date];
            if(typeof booking === 'undefined') {
                console.warn('Potentially unexpected behavoiur, a booking should be found');
                return;
            }
            Booking.get(booking.id, function (data) {
                eventHub.$emit('showCustomerModal', data);
            });
        },
        goNext : function () {
            let firstDate = new Date(this.dates[0].key);
            firstDate = firstDate.addDays(10);
            availabilityService.refreshTable(firstDate.getFullYear(), firstDate.getMonth(), firstDate.getDate());
        },
        goPrev : function () {
            let firstDate = new Date(this.dates[0].key);
            firstDate = firstDate.removeDays(10);
            availabilityService.refreshTable(firstDate.getFullYear(), firstDate.getMonth(), firstDate.getDate());
        },
        viewBooking : function () {
            Booking.getByRef(this.selectedBooking.reference, function success(object) {
                window.booking      = object;
                window.clickedEdit  = true;
                window.location.hash = 'add-booking';
            });
        }
    },

    /**
     * Listen for changes to filterDate and refresh the tables rows (dates)
     * and data with the new filter.
     *
     * @note I feel that it might be more appropriate to use the event bus and submit an event
     * when the parent's date changes, so that it can be caught here and handled. Instead of
     * listening to a prop passed down from the parent. #cantwaitforaseconddev
     */
    watch : {
        filterDate : function (newVal, oldVal) {
            let date = new Date(newVal);
            let oldDate = new Date(oldVal);
            availabilityService.refreshTable(date.getFullYear(), date.getMonth(), date.getDate());
        }
    }

});