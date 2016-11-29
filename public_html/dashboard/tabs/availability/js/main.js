Handlebars.registerHelper('total', function(arr, key) {
    let total = 0;
    _.each(arr, function(obj) {
        total += parseFloat(obj[key]);
    });
    return total;
});

Handlebars.registerHelper('currencySymbol', function() {
    return new Handlebars.SafeString(window.company.currency.symbol);
});

Handlebars.registerHelper('getPrice', function(booking) {
    return booking.real_decimal_price ? booking.real_decimal_price : booking.decimal_price;
});

Handlebars.registerHelper('getOutstanding', function(booking) {
    let price = booking.real_decimal_price ? booking.real_decimal_price : booking.decimal_price;
    let paid = 0;
    _.each(booking.payments, function(obj) {
        paid += parseFloat(obj.amount);
    });
    return parseFloat(price) - paid;
});

Handlebars.registerHelper('sourceString', function(source) {
   switch(source) {
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
});

/**
 * The root vue instance for the availability tab
 *
 * @todo use vue modals instead of reveal so that we do not need to use handlebars
 */
new Vue({

    el      : '#wrapper',

    data : function() {
        return {
            date : moment(new Date().toString()).format('YYYY-MM-DD'),
            companies : []
        }
    },

    created : function() {
        let vm = this;
        userRepo.getCompanies(function (data) {
            vm.companies = data;
        });
    },
    
    /**
     * Once the el is compiled and replaced, attach the jquery handlers for the datetimepicker plugin.
     *
     * @note The datetimepicker uses jquery events only, so vue cannot detect these events,
     * hence why we need to explicity listen for them to proxy them back to vue.
     *
     * @todo https://github.com/Haixing-Hu/vue-datetime-picker implement this once vue is used globally
     */
    mounted : function() {
        let self = this;

        /**
         * Init datetimepicker and attach event listener
         */
        $('input.datepicker').datetimepicker({
            pickDate: true,
            pickTime: false,
            icons: {
                time : 'fa fa-clock-o',
                date : 'fa fa-calendar',
                up   : 'fa fa-chevron-up',
                down : 'fa fa-chevron-down'
            },
            clearBtn: true
        }).on('dp.change', function(event) {
            self.date = moment(event.date.toString()).format('YYYY-MM-DD');
        });

        /**
         * Allow the user to use the up / down keys to render prev and next month
         * @todo move this to vue
         */
        $(document).keydown(function(e) {
            if (e.which === 40 || e.which === 38) { // 40 = down, 38 = up
                e.preventDefault();
                let x;
                let selectDate = $('input.datepicker').val();
                let date = new Date(selectDate);
                if(date.getDate() == NaN) {
                    date = new Date();
                }
                if(e.which === 40) {
                    x = 1;
                } else {
                    x = -1;
                }
                date.setMonth(date.getMonth() + x);
                self.date = moment(date.toString()).format('YYYY-MM-DD');
            }
        });

        $('#modalWindows').on('click', '.view-booking', function(event) {
            // Load booking data and redirect to add-booking tab
            Booking.getByRef($(this).html(), function success(object) {
                window.booking      = object;
                window.clickedEdit  = true;
                window.location.hash = 'add-booking';
            });
        });
    }

});