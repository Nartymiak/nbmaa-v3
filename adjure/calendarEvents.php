<?php
	include_once('../fns/functions.php');
	//variables
	$calendarEvents;

	// check if Post Data is set
	if(!empty($_POST['date'])) {
		
		// run query
		$query = querySubNavCalendar($_POST['date']);
	}

	// create string
	$calendarEvents = makeHTMLCalendarEvents($query);
    
		// print calendar events
		if(!$calendarEvents){
			//handle error
		} else {

			// loop through the array to access the elements
			foreach ($calendarEvents as $element) {
					
				echo $element;
			}
		}

	/** 
	* Copied from the Pages.php file!!
	* creates a string representing an img element
	* @param 	dir 		The directory of the image. It has to be found in the images directory.
	* @param 	fileName 	The filename of the image
	* @param 	alt 		The data for alt attribute
	* @return 	String 		String representing an img element
	*/
	function toImg($dir, $fileName, $alt){

		if($alt==NULL || $alt==""){	$alt = "The New Britain Museum of American Art";}
			$string = "<img src=\"" .$GLOBALS['rootDirectory']. "/images/" .$dir. "/" .$fileName. "\" title=\"" .$alt. "\"> ";

		return $string;

	}

	// accepts an array. Array should be an array of events. returns the array list formatted for html
 	function makeHTMLCalendarEvents($events){

		$todaysDate = date('Y-m-d');
		$result = array();
		$itemPropLocationspan="itemprop=\"location\" itemscope itemtype=\"http://schema.org/Place\">
										<meta itemprop=\"name\" content=\"New Britain Museum of American Art\">
										<address itemprop=\"address\" itemscope itemtype=\"http://schema.org/PostalAddress\">
											<span itemprop=\"name\">New Britain Museum of American Art</span>
											<span itemprop=\"streetAddress\">56 Lexington Street</span>,
											<span itemprop=\"addressLocality\">New Britain</span>,
											<span itemprop=\"addressRegion\">CT</span>, <span itemprop=\"addressCountry\">USA</span>
										</address>
									</span>";

		if($events == null){
			array_push($result, "	<div class=\"date\"><h5><i class=\"fa fa-calendar\"> </i> Today's Events</h5>\r\n
										<p>There are no events for the category you have selected</p>\r\n
									</div>\r\n");

		} else {

			foreach($events as $tuple){

				$eventLink =null;
				$timestamp;
				$evenString;
				$tempDate;

				// setup the link
				if($tuple['Link']){
						
					if($tuple['OutsideLink'] == true){
						$eventLink = $tuple['Link'];
					}else{
						$eventLink = $GLOBALS['rootDirectory']. "/event/" .$tuple['Link']. "";
					}
				}

				// setup timestamp
				if($tuple['StartDate'] != $tempDate){

					List($y,$m,$d) = explode("-",$tuple['StartDate']);
					$timestamp = mktime(0,0,0,$m,$d,$y);

					// check to see if the date section = today's date
					if($tuple['StartDate']==$todaysDate) {
						// write this instead of actual date
						array_push($result, "	<div class=\"date\" id=\"" .$tuple['StartDate']. "\"><h5><i class=\"fa fa-calendar\"> </i> Today's Events</h5>
																<div class=\"startDate\">" .$tuple['StartDate']. "</div>
															</div>");

					} else {

						array_push($result, "	<div class=\"date\" id=\"" .$tuple['StartDate']. "\"><h5><i class=\"fa fa-calendar\"> </i> " .date("l, F d, Y", $timestamp). "</h5>
																<div class=\"startDate\">" .$tuple['StartDate']. "</div>
															</div>");
					}

					$tempDate = $tuple['StartDate'];
				}


				// the first line of the calendar html element
				$eventString = 							"<div class=\"calendarEventsWrapper\" itemprop = \"Event\" itemscope itemtype=\"http://schema.org/Event\">\r\n";
				// the link tag
				if($eventLink) { 	$eventString .=  	"<a href=\"" .$eventLink. "\" itemprop=\"url\">\r\n";}
				// start the inner div "calendarElement"
				$eventString .= 						"<div class=\"calendarElement\">\r\n";
				// image
				//if($tuple['ImgFilePath']){			$eventString .=  toImg("event-page-images", $tuple['ImgFilePath'], $tuple['Title'], "image"). "\r\n"; }
				// event name
				if($tuple['EventTitle']){			$eventString .= "<h3 itemprop=\"name\">" .$tuple['EventTitle']. "</h3>\r\n";}
				// event type title
				if($tuple['TypeTitle']){			$eventString .= "<h4 class=\"calendarEventType\">" .$tuple['TypeTitle']. "</h4>\r\n";}
				// event description
				//if($tuple['Description']){			$eventString .= "<p class=\"description\" itemprop=\"description\">" .shortenText($tuple['Description']). "</p>\r\n";}
				// event time and itemprop data
				if($tuple['StartDate'] || $tuple['StartTime'] || $tuple['EndTime']) {
													$eventString .= "<p class=\"timeDate\">\r\n
																		<meta itemprop=\"startdate\" content=\"".$tuple['StartDate']."T".$tuple['StartTime']."\">\r\n
																		<time class=\"startDate\">" .date("l, F d", $timestamp). "</time>\r\n
								 										".date("g:i a", strtotime($tuple['StartTime'])). " to " .date("g:i a", strtotime($tuple['EndTime'])). 
							 									"</p>\r\n";
				}
				//itemprop location
				if(itemPropLocation){		$eventString .= $itemPropLocation;}
				// close inner div "calendar element"
				$eventString .= 						"</div>\r\n";
				// close a tag
				if($eventLink) { 	$eventString .=  	"</a>\r\n";}
				// close calendar html element
				$eventString .= 						"</div>\r\n";

				// push the eventString onto the result array
				array_push($result, $eventString);

				}	
			}

			return $result;

		}

?>