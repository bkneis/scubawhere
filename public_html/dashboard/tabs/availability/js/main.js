/**
 * The root vue instance for the availability tab
 *
 * @beta
 * @author Bryan Kneis
 * @tab availability
 */
new Vue({

    el : '#wrapper',

    data : function() {
        return {
            date : moment(new Date().toString()).format('YYYY-MM-DD')
        };
    },
    
    /**
     * Once the el is compiled and replaced, attach the jquery handlers for the datetimepicker plugin.
     *
     * @note The datetimepicker uses jquery events only, so vue cannot detect these events,
     * hence why we need to explicity listen for them to proxy them back to vue using plain old js events.
     *
     * @todo implement https://github.com/Haixing-Hu/vue-datetime-picker
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
         *
         * @todo Could this be conditionally attached to the app's root vue instance?
         * I could have used vues @keyup but this would bind it to the table element,
         * and I want it to be possible regardless of focused element.
         */
        $(document).keydown(function(e) {
            if (e.which === 37 || e.which === 39) { // 37 left, 39 right
                e.preventDefault();
                let selectDate = $('input.datepicker').val();
                let date = new Date(selectDate);
                if (isNaN(date.getDate())) {
                    date = new Date();
                }
                if (e.which === 37) {
                    date = date.removeDays(10);
                } else {
                    date = date.addDays(10);
                }
                //date.setMonth(date.getMonth() + x);
                self.date = moment(date.toString()).format('YYYY-MM-DD');
            }
        });
    }

});