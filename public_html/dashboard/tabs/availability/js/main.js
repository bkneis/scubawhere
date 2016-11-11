
Vue.component('availability-table', {

    template : '#availability-table',
    data     : function () {
        return {
            dates          : getDaysInMonth(),
            accommodations : []
        }
    },
    created  : function() {
        let vm = this;
        var sessionFilters = {
            'after': '2016-07-01',
            'before': '2016-07-30',
            'with_full': 1
        };
        Accommodation.filter(sessionFilters, function success(data) {
            console.log(data);
            vm.accommodations = data;
        });
    }

});

function getDaysInMonth(year, month) {
    let date;
    let days = [];
    var month;

    if(typeof year === 'number' && typeof month === 'number') {
        date = new Date(year, month, 1);
    } else {
        date = new Date();
        date.setDate(1);
    }

    month = date.getMonth();
    while (date.getMonth() === month) {
        days.push(new Date(date).toDateString());
        date.setDate(date.getDate() + 1);
    }

    return days;
}

new Vue({
    el : '#wrapper'
});