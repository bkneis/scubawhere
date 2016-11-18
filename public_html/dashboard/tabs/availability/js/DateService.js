/**
 * Service used to perform common date related tasks
 *
 * @todo Inject moment as a dependency
 */
function DateService() {

    /**
     * Get an array of strings representing all the dates between 2 dates.
     *
     * @param startDate
     * @param stopDate
     * @returns {Array}
     */
    this.getDates = function(startDate, stopDate) {
        var dateArray = new Array();
        var currentDate = startDate.addDays(1);

        while (currentDate < stopDate) {
            dateArray.push(moment(currentDate).format('YYYY-MM-DD').toString());
            currentDate = currentDate.addDays(1);
        }

        return dateArray;
    }

    /**
     * Get all the days as a date string in both client and server side format for a month
     *
     * Basically, the server returns dates in the format of YYYY-MM-DD, but this isnt very
     * human readable. So, this functions return an array of objects so that the YYYY-MM-DD
     * can be used as a key when looking up booking in the data array, but then print
     * human readable date strings.
     *
     * @param year
     * @param month
     * @returns {Array}
     */
    this.getDaysInMonth = function(year, month) {
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
            days.push({
                string : new Date(date).toDateString(),
                key    : moment(new Date(date).toDateString()).format('YYYY-MM-DD').toString()
            });
            date.setDate(date.getDate() + 1);
        }

        return days;
    }

}

/**
 * Helper function to add days to a Date object
 *
 * @param integer days Number of days to add
 * @returns {Date}
 */
Date.prototype.addDays = function(days) {
    var dat = new Date(this.valueOf());
    dat.setDate(dat.getDate() + days);
    return dat;
}

/**
 * Helper function to determine if a date object is the same month and year.
 *
 * This is used to check if a refresh / ajax call is needed with calendars
 *
 * @param pDate
 * @returns {boolean}
 */
Date.prototype.isSameMonth = function(pDate) {
    return (
        this.getFullYear() === pDate.getFullYear() &&
        this.getMonth() === pDate.getMonth()
    );
}
