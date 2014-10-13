
function getDate(amount){
	var date = new Date();
    var y = date.getFullYear(),
        m = date.getMonth() + 1,
        d = date.getDate();

    d += amount;

    if(m < 10) m = "0" + m;
    if(d < 10) d = "0" + d;

    var date2 = new Date(y, m, d);

    var y2 = date2.getFullYear();
    var m2 = date2.getMonth() + 1;
    var d2 = date2.getDate();

    var date3 = y + "-" + m + "-" + d + " 00:00:00";

    return date3;
}

function displayTodaysSessions() {

	var yesterday = getDate(-1);
	var tommorow = getDate(1);

	var params =
	{
		before : tommorow,
		after : yesterday
	};

	Session.filter(params, function success(data){
		console.log(data);
	});

}

function slideDown(object) {
    $( "#"+object ).slideToggle( "slow", function() {
        // Animation complete.
    });
}


