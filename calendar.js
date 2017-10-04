	var date = {}; 
	date.month = "";
	date.day = "";
	date.date = "";
	date.daysAddress;
	date.temp;

	var showDates = [];
	var showKeywords = [];

	var monthOnView;
	var yearOnView;

	var calendarOuterWidth = $('.calendar').outerWidth(true);

	var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	var day = ["S", "M", "T", "W", "T", "F", "S"];
	var dateToMonth = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

	var newDays = new Array(7);

	for (var i=0;i<days.length;i++){
		// get days[] address of day
		if (days[i]==date.day){
		date.daysAddress = i;
		}
	}


	for (var i=0;i<7;i++){
		if(date.daysAddress==i){
			newDays[6] = days[i];

		}
		else if(date.daysAddress+i < 7){
			newDays[i]=days[date.daysAddress+i];

		}else{
			newDays[i]=days[i-date.daysAddress];

		}
	}

	var daysInGregorianMonths = [31,28,31,30,31,30,31,31,30,31,30,31];

	// stores page id
	var pageID;

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

		

	var calendar = function(y,m, className) {

		$('.'+ className).prepend('<div class=\"calendarHeader\"><h5 class=\"month\"> ' + dateToMonth[m-1] + ' ' + y + '</h5></div>');

		// make the top row of the calendar that states the day name
		for(var i=0;i<7;i++){
			$('.'+ className).append("<div class=\"calSquare weekday\">" + day[i] + "</div>");
		}
		// figure out how many days in the week appear before
		// and print out empty squares for each one
		for(var i=1;i<(dayOfWeek(y,m,1) | 0);i++){   // simply or 0 the float to truncate to integer
			$('.'+ className).append("<div class=\"calSquare empty\"></div>");
		}

		if (pageID == 'calendar'){
			// print out the number of days the correct month has
			for(var i=1;i<=daysInGregorianMonth(y,m);i++){
				$('.'+ className).append("<label class=\"calSquare filled\"><input class=\"calInput\" type=\"checkbox\" name=\"days[]\" value=\"" + y  + "-"+ pad(m)  + "-" + pad(i) + "\">" + i +"</label>");
			}

		} else {
			for(var i=1;i<=daysInGregorianMonth(y,m);i++){
				$('.'+ className).append("<a href=\"http://www.nbmaa.org/calendar/" + y  + "-"+ pad(m)  + "-" + pad(i) + "\"><label class=\"calSquare filled\">" + i +"</label></a>");
			}
		}

	}

	var makeCalendar = function(y, m) {

		pageID = $('.mainSection').parents().attr("id");

		$('.container').css('width', calendarOuterWidth);

		$('.threeCalendars').css({
			"width" : calendarOuterWidth * 3,
			"left" : calendarOuterWidth * -1
		});

		calendar(y, m-1, 'oldCalendar');
		calendar(y, m, 'calendar');
		if(m==12){
			calendar(y+1, 1, 'newCalendar');
			monthOnView = 0;
			yearOnView = y+1;
		} else {
			calendar(y, m+1, 'newCalendar');
			monthOnView = m;
			yearOnView = y;
		}


		if(pageID == 'calendar'){
			$('.threeCalendars').on("click",'.calSquare .calInput', function(){
				markClicked($(this));
				show($(this).val(), 'date');
			});

			$('.keyword .calInput').on("click", function(){
				markClicked($(this));
				show($(this).val(), 'keyword');
			});
		}

		$('#selectMonth').on("click", function(){
			selectMonth();
		});
	}

	function markClicked(obj) {

		var count = 0;

		if($(obj).attr('class') == 'calInput changed') {
			$(obj).parent().removeAttr("style");
			$(obj).removeClass('changed');

		}else if($(obj).attr('class') != 'calInput changed') {
			$(obj).parent().css("background-color", "rgba(255, 255, 255, .3)");
			$(obj).addClass('changed');
		}

		$('.keyword .calInput').each( function(){

			if($(this).attr('class') != 'calInput changed'){
				$(this).parent().css("opacity", ".3");
				count++;
			} else {
				$(this).parent().css("opacity", "1");

			}

		});

		if(count == $('.keyword .calInput').size()){

			$('.keyword .calInput').each( function(){
				$(this).parent().css("opacity", "1");
			});
		}

	}

	function markFilterClicked(obj) {

		$('.keyword .calInput').each( function(){
			if($(this).parent().html() != obj.parent().html()){
				$(this).parent().css("opacity", ".3");
			}

		})
	}

	function selectMonth() {

		$('.calendar .calSquare .calInput').each( function(){
			 markClicked($(this));
			 show($(this).val(), 'date');
		});
	}

	function rightClick() {

		$('.threeCalendars').animate({
			'left': calendarOuterWidth * -2
		
		}, {			
        	"complete" : function() {
        		completeRightClick();
        	}
		});
	}

	function completeRightClick() {

		(function() {
			monthOnView+=1;

			if(monthOnView==12){
				monthOnView=0;
				yearOnView += 1;
			}	


			$('.oldCalendar').remove();
	        $('.calendar').attr('class', 'oldCalendar');
	        $('.newCalendar').attr('class', 'calendar');
	        $('.threeCalendars').css('left', calendarOuterWidth * -1);
	        $('.calendar').after("<div class=\"newCalendar\"></div>");
	        calendar(yearOnView, monthOnView+1, 'newCalendar');
	    }());
	}

	function leftClick() {

		$('.threeCalendars').animate({
			'left': 0
		
		}, {			
        	"complete" : function() {
				completeLeftClick();
        	
			}
		});

	}

	function completeLeftClick() {

		(function() {
			monthOnView -=1;

			if(monthOnView==1){
				monthOnView=13;
				yearOnView -= 1;
			}

		    $('.newCalendar').remove();
        	$('.calendar').attr('class', 'newCalendar');
       		$('.oldCalendar').attr('class', 'calendar');
     		$('.threeCalendars').css('left', calendarOuterWidth * -1);
       		$('.calendar').before("<div class=\"oldCalendar\"></div>");
       		calendar(yearOnView, monthOnView-1, 'oldCalendar');
		}());
	}

	// pads a given number with 0 if size is less than 2. ex. 01, 02, 03 etc.
	function pad(num) {  	
    	var s = "0" + num;
    	return s.substr(s.length-2);
	}

	function show(data, type){
		
		if(type == 'date') {
			showDates == CheckAndAddData(data, showDates);
		} else if(type == 'keyword'){
			showKeywords == CheckAndAddData(data, showKeywords);
		} else {
			console.log("Error: element clicked has wrong type");
		}

		// the server request
		$.ajax({
 
		    // The URL for the request
		    url: "http://www.nbmaa.org/adjure/events.php",
		 
		    // The data to send (will be converted to a query string)
		    data: {
		         "date": showDates.toString(),
		          "keywordID": showKeywords.toString(),  
		 	},
		    // Whether this is a POST or GET request
		    type: "GET",
		 
		    // The type of data we expect back
		    dataType : "text",
		 
		    // Code to run if the request succeeds;
		    // the response is passed to the function
		    success: function( text ) {

		    	$(".rightColumn").empty();

		    	$( ".rightColumn").html( text );
			    
		    },
		 
		    // Code to run if the request fails; the raw request and
		    // status codes are passed to the function
		    error: function( xhr, status, errorThrown ) {
		        alert( "Sorry, there was a problem!" );
		        console.log( "Error: " + errorThrown );
		        console.log( "Status: " + status );
		        console.dir( xhr );
		    },
		 
		    // Code to run regardless of success or failure
		    complete: function( xhr, status ) {
		        //alert( "The request is complete!" );
		    }
		});
	}

	function CheckAndAddData(data, array){

		var flag = false;

		// check if data was already clicked, if not: add, if it was: delete
		for(i=0;i<array.length;i++){

			if(array[i] == data){
				array.splice(i, 1);
				var flag = true;
			} 
		}
				
		if(flag == false){
			array.push(data);
		}

		flag = false;

		return array;

	}

	// bind the event listeners
	$('.calendarWrapper').children('.rightArrow').on('click', function(){
		rightClick();
	});

	$('.calendarWrapper').children('.leftArrow').on('click', function(){		
		leftClick();
	});



