/**
 * Service object used for the availability-table Vue component.
 *
 * This is responsible for getting the data, binding it to the vue instance
 * and utilising the DateService to create the rows / cols nessecary for the table.
 *
 * @param vm          Pointer to the vue instance so that I can proxy back the data to Vue
 * @param dateService Service object used for advanced date operations
 * @constructor
 */
function AvailabilityService(vm, dateService) {

    /**
     * Template used to display more info in a modal to the user
     * @todo use the vue modal instead of handlebars
     */
    let bookingInfoModalTemplate = Handlebars.compile($('#modal-booking-info-template').html());

    /**
     * Calculate the sum of all the payments
     *
     * @todo Move this to a payment service
     *
     * @param {Array}    payments
     * @returns {number} Sum of all the payments amount attribute
     */
    this.sumPayments = function(payments) {
        let paid = 0.0;
        _.each(payments, function(obj) {
            paid += parseFloat(obj.amount);
        });
        return paid;
    };

    let formatBooking = function(booking, top, bottom, paid, cost, customer_name, customer_id) {
        return {
            customer_id   : booking.pivot.customer_id,
            start         : booking.pivot.start,
            end           : booking.pivot.end,
            package_id    : booking.pivot.packagefacade_id,
            top           : top,
            bottom        : bottom,
            paid          : paid,
            cost          : cost,
            customer_name : customer_name,
            customer_id   : customer_id,
            id            : booking.id
        };
    }

    /**
     * Format the data from the API call so that the array is indexed by accommodation id and date.
     *
     * In addition, create seperate days for bookings that are more than one night long and append
     * attributes so that the CSS can calculate styling options.
     *
     * @param {Array} data The response data retrieved from calling /accommodation/availability
     * @returns {Array}
     */
    this.extractBookings = function(data) {
        let bookings = [];
        let self = this;

        _.each(data, function (accomm) {
            bookings[accomm.id] = [];
            _.each(accomm.bookings, function (booking) {
                let paid          = self.sumPayments(booking.payments);
                let cost          = parseFloat(booking.decimal_price);
                let customer_name = booking.lead_customer.firstname + ' ' + booking.lead_customer.lastname;
                let customer_id   = booking.lead_customer.id;

                bookings[accomm.id][booking.pivot.start] = formatBooking(booking, 1, 0, paid, cost, customer_name, customer_id);

                let extraNights = dateService.getDates(new Date(booking.pivot.start), new Date(booking.pivot.end));

                _.each(extraNights, function(obj) {
                    bookings[accomm.id][obj] = formatBooking(booking, 0, 1, paid, cost, '', customer_id);
                });
            })
        });

        return bookings;
    }

    /**
     * Retrieve the accommodations availability and bind the formatted data to the vue instance
     *
     * @param {String} after
     * @param {String} before
     */
    this.bindAvailability = function(after, before) {
        let params = {
            'after': after,
            'before': before
        };
        var self = this;
        Accommodation.getAvailability(params, function success(res) {
            vm.accommodations          = res.data;
            vm.promises.accommodations = true;
            vm.bookings                = self.extractBookings(res.data);
            vm.$nextTick(function() {
                $('#tbl-availability').DataTable();
            });
        });
    };

    /**
     * Refresh the table by assigning new data to the vue instance data property.
     *
     * @param year  Full year of the filter by date
     * @param month Month of the filter by date
     */
    this.refreshTable = function(year, month, day) {
        vm.dates = dateService.getDaysInMonth(year, month, day);
        let after = vm.dates[0].string;
        let before = _.last(vm.dates).string;
        availabilityService.bindAvailability(after, before);
    }

    /**
     * Determine the styling of each cell for the accommodation
     *
     * For example, a booking with 3 nights stay, the first night should not display a bottom border and the
     * last date should not contain a top border so it appears to be an entire block of cells.
     *
     * @param accomm_id
     * @param date
     * @returns {string}
     */
    this.calcStyle = function(accomm_id, date) {
        let css = '';
        let booking = vm.bookings[accomm_id][date];
        if(typeof booking !== 'undefined') {
            if(booking.cost === booking.paid) {
                css += ' background-color: grey;';
            } else {
                css += ' background-color: yellow;';
            }
            if(!booking.top) {
                css += ' border-left: none !important;';
            }
            if(!booking.bottom) {
                css += ' border-right: none !important;';
            }
        } else {
            css = 'background-color: white';
        }
        return css;
    };

    /**
     * Retrieve customer name by accommodation id and date of booking.
     *
     * @param accomm_id
     * @param date
     * @returns {String}
     */
    this.getCustomerName = function(accomm_id, date) {
        if(typeof vm.bookings[accomm_id][date] !== 'undefined') {
            return vm.bookings[accomm_id][date].customer_name;
        }
        return '';
    };

    /**
     * Display the modal showing the additional information of the accommodation booking.
     *
     * @param accomm_id
     * @param date
     */
    this.showBookingModal = function(accomm_id, date) {
        let booking = vm.bookings[accomm_id][date];
        if(typeof booking === 'undefined') {
            console.warn('Potentially unexpected behavoiur, a booking should be found');
            return;
        }
        Booking.get(booking.id, function (data) {
            $('#modalWindows')
                .append( bookingInfoModalTemplate(data) )
                .children('#modal-booking-info')
                .reveal({
                    animation: 'fadeAndPop',
                    animationSpeed: 300,
                    closeOnBackgroundClick: true,
                    dismissModalClass: 'close-modal',
                    onFinishModal: function() {
                        $('#modal-booking-info').remove();
                    }
                });
        });
    };

}