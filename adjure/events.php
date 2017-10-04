<?php
	include_once('../fns/functions.php');

	$dates = array();
	$keywordIDs = array();
	$query;
	$result = array();
	$calendarEvents = array();
	$todaysDate = date('Y-m-d');

	if(!empty($_GET['date'])) {
		
		$dates=explode(",",$_GET['date']);
	} 

	$query = ajaxQueryCalendarPageEvents($dates);
	// now add query events to result that only match the keywords the user picked
	if(!empty($_GET['keywordID'])){

		// format $_GET into an array
		$parentKeywordIDs = explode(",",$_GET['keywordID']);

		// loop through array
		foreach($parentKeywordIDs as $parentKeywordID){

			// query db against array elements, get the child keywords of parent keyword
			$queriedKeywords = queryParentKeyword($parentKeywordID);

			// add the result to the keywords array
			foreach($queriedKeywords as $key => $el)

				array_push($keywordIDs, array("KeywordID"=>$el['KeywordID']));
			
		}

		foreach($query as $key => $event){

			foreach($keywordIDs as $key => $keywordID){

				//echo "<br>checking: " .$event['EventTypeID']. " vs. " .$keywordID['KeywordID'];
			
				if($event['EventTypeID'] == $keywordID['KeywordID']){

					array_push($result, $event);
				}
			}
		}


	} else {

		$result = $query;
	}
 
	    // copied from Pages.php!!
		// build the events array by querrying the EVENT table using
		// EVENT_DATE_TIMES array for reference
		if($result == null){
				array_push($calendarEvents, "	<div class=\"date\"><h5><i class=\"fa fa-calendar\"> </i> Today's Events</h5>
														<p>There are no events for the category you have selected</p>
													</div>");

		} else {

			$tempDate;

			// use these three variables to set up which element to show image
			$nextImage = false;
			$doNextImage = false;
			$alreadyHasImage = array();

			foreach($result as $tuple){

				$class = "";
				$topImg = "";
					

				if($doNextImage == true && !in_array($tuple['ImgFilePath'], $alreadyHasImage)){
					$nextImage = true;
					$doNextImage = false;
				}

				//print the date for each section
				if($tuple['StartDate'] != $tempDate){

					List($y,$m,$d) = explode("-",$tuple['StartDate']);
					$timestamp = mktime(0,0,0,$m,$d,$y);

					// check to see if the date section = today's date
					if($tuple['StartDate']==$todaysDate) {
						// write this instead of actual date
						array_push($calendarEvents, "	<div class=\"date\"><h5><i class=\"fa fa-calendar\"> </i> Today's Events</h5>
															<div class=\"startDate\">" .$tuple['StartDate']. "</div>
														</div>");

					} else {

						array_push($calendarEvents, "	<div class=\"date\"><h5><i class=\"fa fa-calendar\"> </i> " .date("l, F d, Y", $timestamp). "</h5>
															<div class=\"startDate\">" .$tuple['StartDate']. "</div>
														</div>");

					}
					$tempDate = $tuple['StartDate'];

					// check to see if image hasn't been used
					if(!in_array($tuple['ImgFilePath'], $alreadyHasImage)) {
							
						$nextImage = true;

					// if it has, print the next one
					} else {
						$doNextImage = true;
					}
				}

				// if its the first in the section and has not been used or the first one has already been used
				if( $tuple['ImgFilePath'] && $nextImage == true){	
					$topImg = toImg("event-page-images", $tuple['ImgFilePath'], $tuple['Title']); 
					$class = "showImg";
					array_push($alreadyHasImage, $tuple['ImgFilePath']);
					
				// if for some reason there is no image file path
				} else if(!$tuple['ImgFilePath'] && $nextImage == true){
					$doNextImage = true;
				}

				// print each element
				array_push($calendarEvents, "

					<div class=\"calendarEventsWrapper\">
						<a href=\"" .$GLOBALS['rootDirectory']. "/event/" .$tuple['Link']. "\">
							<div class=\"calendarElement ".$class."\">
								".$topImg."
								<h3>" .$tuple['EventTitle']. "</h3>
								<h4>" .$tuple['TypeTitle']. "</h4>
								<p>" .date("g:i a", strtotime($tuple['StartTime'])). " to " .date("g:i a", strtotime($tuple['EndTime'])). "</p>
								<p>" .shortenText($tuple['Description']). "</p>
								<div class=\"startDate\">" .$tuple['StartDate']. "</div>

							</div>
						</a>
					</div>
				");

				$nextImage = false;
			}
		}

		echo "<h2>EVENTS</h2>";
			
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

?>