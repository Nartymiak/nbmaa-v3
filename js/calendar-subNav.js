	// variables
	var calendarHeight = $("#sideNavCalendar").height();
	var calendarHeightHalf = (calendarHeight/2) | 0;
	var calendarElements;
	var scrollStopToggle = 0;
	var firstLoadToggle = 0;

	// calendar algorithm variables
	var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	var day = ["S", "M", "T", "W", "T", "F", "S"];

	// use an empty string to make array address match literal month values in order to match time stamp values
	var dateToMonth = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	var daysInGregorianMonths = [31,28,31,30,31,30,31,31,30,31,30,31];

	// calendar helper functions	
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

	var displayMonth = function(y, m) {

		var dayOfWeekInt = (dayOfWeek(y,m,1) | 0);
		if(y==2017){ dayOfWeekInt=dayOfWeekInt+1; }

		var month = y + "-" + pad(m);


		$('#sideNavCalendar').append('<div class=\"calendar ' + month + '\"><div class=\"calendarHeader\"><h5 class=\"month\" id=\"' + month + '\""> ' + dateToMonth[m] + ' ' + y + '</h5></div></div>');

		// make the top row of the calendar that states the day name
		for(var i=0;i<7;i++){
			$("." + month ).append("<div class=\"calSquare weekday\">" + day[i] + "</div>");
		}

		// figure out how many days in the week appear before
		// and print out empty squares for each one
		if(dayOfWeekInt != 1 && dayOfWeekInt!= 8){

			for(var i=1;i<dayOfWeekInt;i++){   // simply or 0 the float to truncate to integer
				
				$("." + month ).append("<div class=\"calSquare empty\"></div>");
			}

		}

		// print out the number of days the correct month has
		for(var i=1;i<=daysInGregorianMonth(y,m);i++){
			$("." + month ).append("<label class=\"calSquare filled\"><input class=\"calInput\" type=\"checkbox\" name=\"days[]\" value=\"" + y  + "-"+ pad(m)  + "-" + pad(i) + "\">" + i +"</label>");
		}
	}

	// @param The year and month to initialize from.
	var makeSideNavCalendar = function(y, m, lastDate, todaysDate){

		var startYear = 2015;
		var startMonth = 3;
		var endMonth = new Date(lastDate).getMonth()+2;
		var endYear = new Date(lastDate).getFullYear();
		var todaysCalOffset;
		

		 //create all the calendars in the scrolling div
		while(startMonth != endMonth || startYear != endYear)
		{

			displayMonth(startYear, startMonth);
			
			startMonth++;

			if(startMonth==13){
				startMonth=1;
				startYear++;
			}
		}

		var container = document.getElementById('sideNavCalendar');
		var rowToScrollTo = document.getElementsByClassName('2017-04')[0];

		container.scrollTop = rowToScrollTo.offsetTop;

		markCalSquareSelected(todaysDate);

		calendarElements = $('#sideNavCalendar .calendar');

		// when scroll is triggered, start timer. Keep reseting until user stops scrolling. If no scroll at end of timer, call stopScroll();
		$('#sideNavCalendar').scroll(function(){

			clearTimeout($.data(this, 'scrollTimer'));
   			// toggle so it only calls the ajax call when done scrolling
	   		if(scrollStopToggle == 0){
	   			// add scrollTimer data to $(this) object
	   			$.data(this, 'scrollTimer', setTimeout(function() {
	  				stopScroll();
	    		}, 100));

	    	} else {
	       		scrollStopToggle = 0;
	       	}

		});

		$('.keyword').on('click', function(){
			filter($(this).attr("id"));
		});

		$('.calSquare').on('click', function(){
			if($(this).children().val() !== undefined) {
				showDate($(this).children().val());
			}
		});

		$('.month').on('click', function(){
			show($(this).attr("id"));

		});

		//animate the arrow and slide down
		$('.parentKeyword').on('click', function(){
			$(this).next().children().slideToggle(200);
			$(this).children().toggleClass("down");
		});

		addCountToFilter();
		markSelectedDates();
	}

	function stopScroll(){

		//asign some variables
		position = $('#sideNavCalendar').scrollTop();
		
		// check this variable to make sure its not the first time the page is loaded, should equal 0 if it is.
		if(firstLoadToggle == 1){
			// if user scrolled below the half way mark
			if(position%calendarHeight >= calendarHeightHalf){
				// scroll to calendar below
				$('#sideNavCalendar').animate({
	       			scrollTop: ((position/calendarHeight) | 0) * calendarHeight + calendarHeight,
	    		}, 200);
	    		onViewObj = $(calendarElements[((position/calendarHeight) | 0)+1]).attr('class').split(' ')[1];
	    		// call ajax function
	    		show(onViewObj);

			// if user scrolled above the half way mark
			} else {
				// scroll to calendar above
				$('#sideNavCalendar').animate({
	       			scrollTop: ((position/calendarHeight) | 0) * calendarHeight 
	    		}, 200);
	    		onViewObj = $(calendarElements[((position/calendarHeight) | 0)]).attr('class').split(' ')[1];
	    		// call ajax function
	    		show(onViewObj);
			}

			scrollStopToggle = 1;
		} else {
			// if it is 0, set the toggle to 1 for all other times its scrolled.
			firstLoadToggle = 1;

		}

	}

	// ajax call
	function show(data){

		// the server request
		$.ajax({
 
		    // The URL for the request
		    url: "http://www.nbmaa.org/lab/adjure/calendarEvents.php",
		 
		    // The data to send (will be converted to a query string)
		    data: {
		         "date": data
		 	},
		    // Whether this is a POST or GET request
		    type: "POST",
		 
		    // The type of data we expect back
		    dataType : "text",
		 
		    // Code to run if the request succeeds;
		    // the response is passed to the function
		    success: function( text ) {

		    	$(".rightColumn").empty();

		    	$( ".rightColumn").html( text );

		    	markSelectedDates();

				clearKeywordSelected();

				addCountToFilter();
			    
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

		return true;
	}

	function filter(keyword){

		var flag = 0;

		$('#noEvents').remove();

		$(".calendarEventType").each(function(){

			if($(this).html() != keyword){
				$(this).parents().eq(2).css("display", "none");

				if($(this).parents().eq(2).prevAll(".date:first").data("show") != true){
					$(this).parents().eq(2).prevAll(".date:first").css("display", "none");

				}

			} else {
				$(this).parents().eq(2).css("display", "block");
				$(this).parents().eq(2).prevAll(".date:first").css("display", "block");
				$(this).parents().eq(2).prevAll(".date:first").data("show", true);
				flag = 1;
			}

		});

		if(flag == 0){
			$('.rightColumn').append("<p id=\"noEvents\">There are no events of this type</p>");
		}

		markKeywordSelected(keyword);
		markSelectedDates();

		// clear all stored data so empty for next "click"
		$(".calendarEventType").each(function(){
			$(this).parents().eq(2).prevAll(".date:first").removeData("show");
		});

		$('html, body').animate({
			scrollTop: $(".mainSection").offset().top - 100
		}, 300);
	}

	function showDate(date){

		var flag = 0;
		$('#noEvents').remove();

		$(".date").each(function(){

			if(date != $(this).attr("id")){
				$(this).css("display", "none");
				$(this).nextUntil( ".date" ).css("display", "none");

			} else {
				$(this).css("display", "block");
				$(this).nextUntil( ".date" ).css("display", "block");
				flag = 1;
			}

		});

		if(flag == 0){
			$('.rightColumn').append("<p id=\"noEvents\">There are no events scheduled for the day you selected.</p>");
		}

		markCalSquareSelected(date);

	}

	// pads a given number with 0 if size is less than 2. ex. 01, 02, 03 etc.
	function pad(num) {  	
    	var s = "0" + num;
    	return s.substr(s.length-2);
	}

	function markCalSquareSelected(date) {

		$('.calInput').each(function(){
			if($(this).val()==date){
				$(this).parent().addClass("selected");

			} else {
				$(this).parent().removeClass("selected");

			}
		});
	}

	function markKeywordSelected(id) {

		$('.keyword').each(function(){
			if($(this).attr("id")==id){
				$(this).addClass("selected");

			} else {
				$(this).removeClass("selected");

			}
		});
	}

	function clearKeywordSelected(){

		$('.keyword').each(function(){

			$(this).removeClass("selected");

		});
	}

	function addCountToFilter(){

		var keyword;
		var count = 0;

		$(".count").remove();

		$('.keyword').each(function(){

			keyword = $(this).attr("id");

			$(".calendarEventType").each(function(){
				if($(this).html() == keyword){
					count++;
				}
			});
			
			if(count > 0){
				$(this).append(" <span class=\"count\">(" + count + ")</span>");
			}

			count = 0;

		});

	}

	function markSelectedDates(){

		var date;

		clearMarkSelectedDates();

		$('.date').each(function(){

			date = $(this).attr("id");

			if($(this).css("display") != "none"){

				$('.calInput').each(function(){

					if($(this).val()==date){
						$(this).parent().addClass("selected");
					}
				});
			}
		});

	}

	function clearMarkSelectedDates(){

		$('.calSquare').each(function(){

			$(this).removeClass("selected");

		});
	}





