var date = {}; // August 7, 2014
date.month = "August";
date.day = "Sunday";
date.date = 7;
date.daysAddress;
date.temp;

var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

var newDays = new Array(7);

console.log(newDays.length)

	for (var i=0;i<days.length;i++){
		// get days[] address of day
		if (days[i]==date.day){
		date.daysAddress = i;
		}
	}


	for (var i=0;i<7;i++){
		if(date.daysAddress==i){
			newDays[6] = days[i];
			//console.log(days[i]);
		}
		else if(date.daysAddress+i < 7){
			newDays[i]=days[date.daysAddress+i];
			//console.log(days[date.daysAddress+i]);
		}else{
			newDays[i]=days[i-date.daysAddress];
			//console.log(days[i-date.daysAddress]);
		}
	}

	for ( var i=0;i<newDays.length;i++){
		console.log(newDays[i]);
	}

	var daysInGregorianMonths = [31,28,31,30,31,30,31,31,30,31,30,31];

	var isGregorianLeapYear = function(year) {
		var isLeap = false;
		if(year%4==0) { 
			isLeap = true;
		}
		if(year%100==0) {
			isLeap = false;
		}
		if(year%400==0) {
			isLeap = true;
		}
		return isLeap;
	}

	var dayOfYear = function(y, m, d) {
		var c = 0;	
		for (var i=1; i<m; i++) { // Number of months passed
			c = c + daysInGregorianMonth(y,i);
		}
		c = c+d;
		return c;
	}

	var daysInGregorianMonth = function(y, m) {
		var d = daysInGregorianMonths[m-1];
		if (m==2 && isGregorianLeapYear(y)) {
			d++;
		}
		return d;
	}

	var dayOfWeek = function(y,m,d){
		var w=1; // 01-Jan-0001 is Monday, so base is Sunday
		y = (y-1)%400 + 1; // calendar cycle is 400 years
		var ly = (y-1)/4;
		ly = ly - (y-1)/100; //Adjustment
		ly = ly + (y-1)/400;
		var ry = y - 1 - ly; // Regular years passed
		w = w + ry; // Regular year has one extra week day
		w = w + 2*ly; // Leap year has two extra days
		w = w + dayOfYear(y,m,d);
		w = (w-1)%7 + 1;
		return w;
	}

			
	console.log(dayOfWeek(2014,7,1) | 0); // simply or 0 the float to truncate to integer

	var calendar = function() {

		for(var i=1;i<(dayOfWeek(2014,8,1) | 0);i++){
			$('.calendar').append("<div class=\"calSquare\"></div>");
		}

		for(var i=1;i<=31;i++){
			$('.calendar').append("<div class=\"calSquare\">" + i +"</div>");
		}
	}