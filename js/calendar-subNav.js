	// for ajax call
	var showDates = [];
	var showKeywords = [];

	// store initial values, then adjust when user clicks arrows
	var monthOnView;
	var yearOnView;

	// scrollCal elements
	var dateOffsets=[];
	var navHeight;
	var currentCalDate;
	var currentCalDateIndex = 0;
	var prevCalDateIndex = 0;
	var currentCalMonth;
	var subNavOffset;
	var mainSectionOffset;
	// binary search
	var startIndex;
	var stopIndex;
	var middle;

	// find the width, then use to determine other css values...
	var calendarOuterWidth = $('.calendar').outerWidth(true);

	// calendar algorithm variables
	var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	var day = ["S", "M", "T", "W", "T", "F", "S"];
	var dateToMonth = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
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

		// print out the number of days the correct month has
		for(var i=1;i<=daysInGregorianMonth(y,m);i++){
			$('.'+ className).append("<label class=\"calSquare filled\"><input class=\"calInput\" type=\"checkbox\" name=\"days[]\" value=\"" + y  + "-"+ pad(m)  + "-" + pad(i) + "\">" + i +"</label>");
		}

	}

	var makeCalendar = function(y, m) {

		navHeight = $("#mainNav").height();
		mainSectionOffset = $(".mainSection").offset().top;

		$('.container').css('width', calendarOuterWidth);

		$('.threeCalendars').css({
			"width" : calendarOuterWidth * 3,
			"left" : calendarOuterWidth * -1
		});

		calendar(y, m-1, 'oldCalendar');
		calendar(y, m, 'calendar');
		calendar(y, m+1, 'newCalendar');

		monthOnView = m;
		yearOnView = y;

		$('.threeCalendars').on("click",'.calSquare .calInput', function(){
			markClicked($(this));
			
			//if(show($(this).val(), 'date')){
				scrollToDate($(this));
			//}
		});

		$('.keyword .calInput').on("click", function(){
			markClicked($(this));
			show($(this).val(), 'keyword');
		});

		$('#selectMonth').on("click", function(){
			selectMonth();
		});

		dateOffsets = updateDateOffsets();
		subNavOffset = $('.stopSubNav').offset().top - navHeight;

		//this gets the date highlighted when page is loaded
		currentCalDate = $(".date").eq(currentCalDateIndex).children(".startDate").html();
		prevCalDateIndex = currentCalDateIndex;
		updateCalendarMarks();

		// call the function with the scroll trigger
		$('.rightColumn').scroll(function(){
			scrollCal();
		});

		// calendar image scripts
		showImage();
		buildEventViewPort();

	}

	function scrollToDate(obj){

		var id = obj.attr("value");
		var target;

		if (!$("#"+id).offset()){
			
		} else {

			$('.rightColumn').animate({
        		scrollTop: $("#"+id).position().top + $(".rightColumn").scrollTop()
    		}, 1000);
 
		}

	}

	// finds the date element offset values in calendar page
	function updateDateOffsets(){
		var result=[];

		currentCalDate = $(".date").each(function(index){
			result[index] = $(this).offset().top - mainSectionOffset;
		});

		return result;
	}

	function scrollCal(){

		//asign some variables
		position = $('.rightCol').scrollTop();
		currentCalDateIndex = binarySearch(dateOffsets, position);
		//console.log(currentCalDateIndex);

		// check if the viewer scrolled past date section
		if(currentCalDateIndex != prevCalDateIndex){

			// get date data and update mark
			currentCalDate = $(".date").eq(currentCalDateIndex).children(".startDate").html();
			prevCalDateIndex = currentCalDateIndex;
			updateCalendarMarks();
		}

		

		dateOffsets = updateDateOffsets();
		//currentCalMonth; 

		// this holds the subnav when user scrolls
		stopSubNav(position);
		
	}

	function stopSubNav(pos){

		var width = $('.stopSubNav').width();

		if(pos > subNavOffset){

			$('.stopSubNav').css({
				"position": "fixed",
				"top": navHeight,
				"width": width
			});

		} else {

			$('.stopSubNav').css({
				"position": "relative",
				"top": 0
			});
		}
	}

	function updateCalendarMarks(){

		$('.calSquare').each(function(){
			if($(this).children().attr("value")==currentCalDate) {
				$(this).css("background-color", "rgba(0, 0, 0, .3)");
			} else {
				$(this).css("background-color", "transparent");
			}
		});

	}

	function markClicked(obj) {

		var count = 0;

		if($(obj).attr('class') == 'calInput changed') {
			$(obj).parent().removeAttr("style");
			$(obj).removeClass('changed');

		}else if($(obj).attr('class') != 'calInput changed') {
			$(obj).parent().css("background-color", "rgba(0, 0, 0, .3)");
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

	function showImage() {
		//$('.calendarElement > img').css("display", "none");
		//$('.calendarElement .description').css("display", "none");
	}

	function buildEventViewPort() {
		
		var windowHeight =window.innerHeight;
		var rightColumnHeight = windowHeight - mainSectionOffset;

		$('#calendar .rightColumn').css({
			height: rightColumnHeight,
			overflow: "scroll"
		});
	}

	// pads a given number with 0 if size is less than 2. ex. 01, 02, 03 etc.
	function pad(num) {  	
    	var s = "0" + num;
    	return s.substr(s.length-2);
	}

	// ajax call
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
		    url: "http://www.nbmaa.org/adjure/calendarEvents.php",
		 
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

		return true;
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

	// modified to fit the arrays used
	function binarySearch(items, value){

	   	startIndex  = 0;
	    stopIndex   = items.length - 1;
	    middle      = ((stopIndex + startIndex)/2) | 0;

	    (function(){ 

	    	while(!(items[middle] <= value && value <= items[middle + 1]) && startIndex < stopIndex){

		        //adjust search area
		        (function(){
		        	if (value < items[middle]){
			            stopIndex = middle - 1;
			        } else if (value > items[middle]){
			            startIndex = middle + 1;
			        }
			    }());

		        //recalculate middle
		        middle = ((stopIndex + startIndex)/2) | 0;
	    	}

	    }());

	    //make sure it's the right value
	    return middle;
	}

	// bind the event listeners
	$('.calendarWrapper').children('.rightArrow').on('click', function(){
		rightClick();
	});

	$('.calendarWrapper').children('.leftArrow').on('click', function(){		
		leftClick();
	});



