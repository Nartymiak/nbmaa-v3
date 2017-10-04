<?php

	abstract class Page {

		abstract protected function makeBody();
		abstract protected function makeHTMLElements($result);
		abstract protected function makeSubNav();


		// print an HTML header
		protected function HTMLheader($title = '') {
		?>
			<html>
				<!DOCTYPE html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
					<title> NBMAA | <?php echo str_replace(array("\r", "\n"), '',$title); ?></title>
					<link href="<? echo $GLOBALS['rootDirectory'] ?>/css/nbmaa3.css" rel="stylesheet" type="text/css" />
					<link href='http://fonts.googleapis.com/css?family=Lato:100,400,700,400italic' rel='stylesheet' type='text/css'>
					<script language="Javascript">
					<!--
								     	//-->
					</script>
				</head>
			<body>
				<!--<div id="grid"></div>-->
		<?
		}

		protected function nav(){
			
			$logoFile = "nbmaa-spring-logo.jpg";
			?>
			<header>
				<div class="logo"><? echo $this->toImg("logos", $logoFile, "new britain museum of american art logo"); ?></div>
				<nav id="mainNav">
					<a href="visit.html">VISIT</a>
					<a href="exhibition.html">EXHIBITION</a>
					<a href="calendar.html">CALENDAR</a>
					<a href="education.html">EDUCATION</a>
					<a href="support.html">SUPPORT US</a>
					<a href="shop.html">SHOP</a>
				</nav>
				<h1>NEW BRITAIN MUSEUM OF AMERICAN ART</h1>
			</header>
		<?
		}

		protected function HTMLfooter(){
		?>
			<footer>
				<div class="wrapper">
					<div class="column">
						<a href="">About</a>
						<a href="">Contact</a>
						<a href="">Employment</a>
					</div>
					<div class="column right">
						<a href="">Opportunities for Artist</a>
						<a href="">Accessibility</a>
						<a href="">Marketing Material</a>
					</div>
					<div class="columnTwo">
						<a href="">How to Donate to the Museum</a>
					</div>
					<div class="columnTwo">
						<a href=""><img src="<? echo $GLOBALS['rootDirectory']. "/images/sponsors/ctvisit-logo-1.jpg" ?>"></a>
					</div>
					<div class="clear"></clear>
				</div>
			</footer>
		</body>
	</html>
		<?
		}

		protected function cta($link){ ?>
			<div class="cta">
				<? 	echo "<a href=" .$link. "\">" ;
					echo $this->toImg("cta-images", "cta.jpg", "nbmaa-cta");
					echo "</a>\r\n"; 
				?>
			</div>
		<? 	echo "\r\n";
		}

		protected function toImg($dir, $fileName, $alt){

			if($alt=NULL || $alt=""){	$alt = "The New Britain Museum of American Art";}
			$string = "<img src=\"" .$GLOBALS['rootDirectory']. "/images/" .$dir. "/" .$fileName. "\" alt=\"" .$alt. "\"> ";

			return $string;

		}

		/**
		*Builds an artist's name 
		*@param a result from querying the ARTIST table
		*@return String artist name
		**/
		protected function buildArtistName($artistsQuery){

			$artistName ="";

			if(!$artistsQuery){
					//handle error
			} else {

				if($artistsQuery['Fname'] || $artistsQuery['Mname'] || $artistsQuery['Lname']){
					if($artistsQuery['Fname']){ $artistName.= $artistsQuery['Fname']; }
					if($artistsQuery['Mname']){ $artistName.=  " " .$artistsQuery['Mname']; }
					if($artistsQuery['Lname']){ $artistName.=  " " .$artistsQuery['Lname']; }
				}
			}

			return $artistName;
		}

		protected function buildDateRange(){
			
			$startDate = date("Y-m-01");
			$endDate = date("Y-m-t");

			$dateArray = array($startDate, $endDate);

			return $dateArray;
		}
	}


	class CalendarPage extends Page{

		private $imagePath;
		private $calendarElements = array();

		function __construct($url){


			//first, query the db
			$result = "null";
			$this->HTMLheader($url);
			$this->makeHTMLElements($result);
			$this->makeBody();
			$this->HTMLfooter();
		}

		protected function makeBody(){ // CALENDAR PAGE function
			//full-width background
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n" ;}

			// wrapper
			echo "		<div class=\"wrapper\">\r\n";
			
			// header section
			$this->nav();

			// calendar elements
			if(!$this->calendarElements){
				//handle error
			} else {

				foreach ($this->calendarElements as $element) {
						
					echo $element;
						
				}
			}

			echo "		</div><!-- end wrapper -->\r\n";

			$this->makeXMLHttpRequestSection();
		}

		protected function makeHTMLElements($result){

			$eventDateTimes;
			$events;
			$receptions;
			$classes;

			if(!$result){
				// handle error
			} else {

				// get the first of the month date and last of the month date
				$dateRange = $this->buildDateRange();
			
				// get all the eventDateTimes in the range of dates
				if($eventDateTimes = queryCalendarByRange('EVENT_DATE_TIMES', 'StartDate', $dateRange[0], $dateRange[1] )){
				
					// stores events
					$events=array();
					// store previous eventID to avoid duplicates
					$temp;

					// loop through eventDateTimes to query events by eventID
					foreach($eventDateTimes as $tuple){

						// check if its been querried already
						if($temp != $tuple['EventID']){

							//execute the query and add result to the events array
							$ref=queryReference('EVENT', 'EventID', $tuple['EventID']);
							$ref=$ref[0];
							if(array_push($events, $ref)){
								//everything is good!
							}
						}
						//store the id for checking duplicate
						$temp = $tuple['EventID'];
					}
				}

				if(!$events){
					// handle error
				} else {

					foreach($eventDateTimes as $eventDateTime){

						$event = getElementByID($events, "EventID", $eventDateTime['EventID']);

						array_push($this->calendarElements, "

							<div class=\"calendarElement\">
								<h2>" .$event['Title']. "</h2>
								<p>" .$eventDateTime['StartTime']. " " .$eventDateTime['EndTime']. "</p>
								<p>" .$event['Description']. "</p>
							</div>
						");
					}
				}
			}
		}

		protected function makeSubNav(){

		}

		protected function makeXMLHttpRequestSection(){
			echo "
				<script language=\"Javascript\"></script>
			";
		}
	}


	class EventPage extends Page{

		private $imagePath;
		private $primaryImage;
		private $imageCaption;
		private $title;
		private $bodyContent;
		private $eventType;
		private $eventArtistQuery;
		private $artistsQuery;
		
		function __construct($url){

			//first, query the db
			$result = queryEventPage($url);
			$this->HTMLheader($result['Title']);
			$this->makeHTMLElements($result);
			$this->makeBody();
			$this->HTMLfooter();
		}

		protected function makeBody(){ // EVENT PAGE function
			//full-width background
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n" ;}

			// wrapper
			echo "		<div class=\"wrapper\">\r\n";
			
			// header section
			$this->nav();

			//main section
			echo "		<div class=\"mainSection\">\r\n";
			
			//sub navigation section
			$this->makeSubNav();

			// right column
			echo "		<div class=\"rightColumn\">\r\n";
			if($this->title) {						echo "		<h2>" .$this->title. "</h2>\r\n"; } 
			if($this->eventType) {					echo "		<h3>" .$this->eventType. "</h3>\r\n";}
			if($this->primaryImage) { 				echo "		" .$this->primaryImage.  "\r\n";}
			if($this->imageCaption) {				echo "		<p class=\"tombstone\">" .$this->imageCaption. "</p>\r\n";}
			if($this->bodyContent) {				echo "		<div class=\"bodyContent\">" .$this->bodyContent. "		</div>\r\n";}
			echo "		</div><!-- end rightColumn -->\r\n";
			echo "		<div class=\"clear\"></div>\r\n";
			echo "		</div><!-- end mainSection -->\r\n";

			// call to action (CTA) section
			$this->cta("taco");
			echo "		</div><!-- end wrapper -->\r\n";
			
		}

		protected function makeSubNav(){ // EVENT PAGE function

			echo "		<nav class=\"subNav\">\r\n";

			if($this->artistsQuery){
				echo "		<h3 class=\"top\">About this Event</h3>\r\n";
				if(count($this->artistsQuery)==1){ 				
					$artistName = $this->buildArtistName($this->artistsQuery[0]);

																echo "		<a href=\"\">" .$artistName. "</a>\r\n";
				}
				else {											echo "		<a href=\"\">Artists' Bio</a>\r\n";}
			}

			// consistent links
			echo "		<h3>Extra</h3>";
			//echo "		<a href=\"\">Become a Member</a>\r\n";
			echo "		<a href=\"\">Join our Email List</a>\r\n";
			echo "		<a href=\"\">Share</a>\r\n";
			echo "		</nav>\r\n";
		}

		protected function makeHTMLElements($result){ // EVENT PAGE function

			if(!$result){
				// handle error
			} else {

				// title
				if($result['Title']){ $this->title = nl2br($result['Title']);}

				// event type
				if($eventTypeQuery = queryReference('EVENT_TYPE', 'EventTypeID', $result['EventTypeID'])){
					$this->eventType = $eventTypeQuery[0]['Title'];
				}

				// build the html img element for the main image
				if(!$result['ImgFilePath']){
				// handle error
				} else {
					//create HTML alt attribute text
					if($result['Title']){	$alt = $result['Title'];}
					//create primary image
					$this->primaryImage=$this->toImg("event-page-images", $result['ImgFilePath'], $alt );
					//create image path for background
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/event-page-images/" .$result['ImgFilePath'];
				}

				// caption
				if($result['ImgCaption']) {	$this->imageCaption = nl2br($result['ImgCaption']);}

				// body content (called Description in EVENT table)
				if($result['Description']){ $this->bodyContent = nl2p($result['Description']);}

				// build the EVENT_ARTISTS table query
				if($this->eventArtistQuery = queryReference('EVENT_ARTISTS', 'EventID', $result['EventID'])){

					// build the ARTIST query
					foreach($this->eventArtistQuery as $artist){
						if($this->artistsQuery = queryReference('ARTIST', 'ArtistID', $artist['ArtistID'])){
							// everything is gravy!
						}
					}
				}
			}
		}
	}



	class ExhibitionPage extends Page{

		private $artworkQuery;
		private $artistArtworkQuery;
		private $exhibitionArtworksQuery;
		private $artistsQuery;
		private $exhibitionReceptionsQuery;
		private $exhibitionEventsQuery;
		private $eventQuery;
		private $lecturesArray;

		private $artistNames; // artists names are stored as a single string
		private $primaryImage;
		private $imagePath;
		private $tombstone;
		private $bodyContent;
		private $title;
		private $startDate;
		private $endDate;
		private $gallery;


		function __construct($url){

			//first, query the db
			$result = queryExhibitionPage($url);

			$this->HTMLheader($result['Title']);
			$this->makeHTMLElements($result);
			$this->makeBody();
			$this->HTMLfooter();

		}

		// make html body element
		protected function makeBody(){

			//full-width background
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n" ;}

			// wrapper
			echo "		<div class=\"wrapper\">\r\n";
			
			// header section
			$this->nav();

			//main section
			echo "		<div class=\"mainSection\">\r\n";
			
			//sub navigation section
			$this->makeSubNav();

			// right column
			echo "		<div class=\"rightColumn\">\r\n";
			if($this->title) {						echo "		<h2>" .$this->title. "</h2>\r\n"; } 
			if($this->startDate && $this->endDate){ echo "		<p>" .$this->startDate. "&ndash;" .$this->endDate. "</p>\r\n";}
			if($this->gallery) {					echo "		<p>" .$this->gallery. "</p>\r\n";}
			if($this->primaryImage) { 				echo "		" .$this->primaryImage.  "\r\n";}
			if($this->tombstone) {					echo "		<p class=\"tombstone\">" .$this->tombstone. "</p>\r\n";}
			if($this->bodyContent) {				echo "		<div class=\"bodyContent\">" .$this->bodyContent. "		</div>\r\n";}
			echo "		</div><!-- end rightColumn -->\r\n";
			echo "		<div class=\"clear\"></div>\r\n";
			echo "		</div><!-- end mainSection -->\r\n";

			// call to action (CTA) section
			$this->cta("taco");
			echo "		</div><!-- end wrapper -->\r\n";
		}




		protected function makeSubNav(){

			
			echo "		<nav class=\"subNav\">\r\n";
			if($this->exhibitionArtworksQuery || $this->artistsQuery || $this->exhibitionVideoQuery ){
				echo "		<h3 class=\"top\">About the Exhibition</h3>\r\n";
				if($this->exhibitionArtworksQuery){		echo "		<a href=\"\">Artwork</a>\r\n";}
				if($this->artistsQuery){ 				echo "		<a href=\"\">Artist Bio</a>\r\n";}
				if($this->exhibitionVideoQuery){ 		echo "		<a href=\"\">Videos</a>\r\n";}
			}

			if($this->exhibitionReceptionsQuery || ($this->exhibitionEventsQuery && $eventLinks = $this->makeEventLinks())){
				echo "		<h3>Events of the Exhibition</h3>\r\n";
				if($this->exhibitionReceptionsQuery){	echo"	<a href=\"\">Openings</a>\r\n";}
				if($eventLinks){ 						echo $eventLinks;}

			}

			// consistent links
			echo "		<h3>Extra</h3>";
			echo "		<a href=\"\">Members See it Free</a>\r\n";
			echo "		<a href=\"\">Join our Email List</a>\r\n";
			echo "		<a href=\"\">Share</a>\r\n";

			echo "		</nav>\r\n";

		}

		/**
		* Prints out to html a list of event types that correspond with the exhibition
		**/
		protected function makeEventLinks(){

			$eventTypes = array();
			$returnString;
			// first query the EVENT_TYPE table
			if($eventTypeQuery = get_table("SELECT * FROM EVENT_TYPE")){

				$count = 0;
				// loop through result
				foreach($eventTypeQuery as $row){
					// loop through exhibition events
					foreach($this->eventQuery as $rowE){
						//if an event has an id that matches an id in the EVENT_TYPE table
						if($row['EventTypeID']==$rowE[0]['EventTypeID']){
							// check if its not already in the table to be printed
							if(!in_array($row['Title'], $eventTypes)){
								// add to the table
								$eventTypes[$count] = $row['Title'];
								$count++;
							}
						}
					}
				}
				// print the links
				foreach( $eventTypes as $row) {
					$returnString .= "		<a href=\"\"> " .$row. "</a>\r\n";
				}

			}
			return $returnString;
		}


		/**
		*@param  a result from a query on the EXHIBITIONS table
		*/
		protected function makeHTMLElements($result){

			if(!$result){
				// handle error
			}else{

				// build the exhibition artworks table query
				if($this->exhibitionArtworksQuery = queryReference('EXHIBITION_ARTWORKS', 'ExhibitionID', $result['ExhibitionID'])){
					//everything is good!
				}

				// build the videos query
				if($this->exhibitionVideoQuery = queryReference('EXHIBITION_VIDEOS', 'ExhibitionID', $result['ExhibitionID'])){
					//everything is gravy!
				}

				// build the receptions query
				if($this->exhibitionReceptionsQuery = queryReference('EXHIBITION_RECEPTIONS', 'ExhibitionID', $result['ExhibitionID'])){
					//everything is gold!
				}

				// build the events query
				if($this->exhibitionEventsQuery = queryReference('EXHIBITION_EVENTS', 'ExhibitionID', $result['ExhibitionID'])){

					$count = 0;
					// loop through exhibitionEvent query and get the matching event
					foreach($this->exhibitionEventsQuery as $row){
						// add to eventQuery 
						if($this->eventQuery[$count] = queryReference('EVENT', 'EventID', $row['EventID'])){
							$count++;
						}
						
					}

				}
				// build main image artist name (by querying ARTWORK with artworkReferenceNo in the EXHIBITION table)
				if($this->artworkQuery = queryReference('ARTWORK', 'ArtworkID', $result['ArtworkReferenceNo'])){

					// check if artist_artwork table gets referenced
					if($this->artistArtworkQuery = queryReference('ARTIST_ARTWORKS', 'ArtworkID', $this->artworkQuery[0]['ArtworkID'])){

						// build the artistNames string
						$this->artistNames ="";

						// loop through the artwork query
						foreach($this->artistArtworkQuery as $artist){

							// query the ARTIST table with corresponding ID
							if($this->artistsQuery = queryReference('ARTIST', 'ArtistID', $artist['ArtistID'])){

								// build name and concatenate each return string
								$this->artistNames .= $this->buildArtistName($this->artistsQuery[0]);

							}
						}

					}
				}

				//build gallery
				if ($this->gallery = queryReference('GALLERY', 'GalleryID', $result['GalleryReferenceNo'])[0]['NickName']){
					// then everything is grand!
				}

				// build the html img element for the main image
				if(!$this->artworkQuery[0]['ImgFilePath']){
				//handle error
				} else {
					$alt = $this->artistNames. " " .$this->artworkQuery[0]['Title'];
					$this->primaryImage=$this->toImg("exhibition-page-images", $this->artworkQuery[0]['ImgFilePath'], $alt );			
				
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/exhibition-page-images/" .$this->artworkQuery[0]['ImgFilePath'];
				}

				// buld tombstone
				if($artwork[0]['TombstoneOverride']){
					$this->tombstone .= $TombstoneOverride;
				} else {
					if($this->artistNames){								$this->tombstone  .= $this->artistNames. ", "; }
					if($this->artworkQuery[0]['Title']){ 				$this->tombstone  .= "<i> " .$this->artworkQuery[0]['Title']. "</i>, "; }
					if($this->artworkQuery[0]['DateCreated']){ 			$this->tombstone  .= " " .date('F d, Y', strtotime($this->artworkQuery[0]['DateCreated'])). ", ";}
					if($this->artworkQuery[0]['Medium']){ 				$this->tombstone  .= " " .ucfirst(strtolower($this->artworkQuery[0]['Medium'])). ", ";}
					if($this->artworkQuery[0]['Dimensions']){ 			$this->tombstone  .= " " .$this->artworkQuery[0]['Dimensions']. ", ";}
					if($this->artworkQuery[0]['CourtesyStatement']){ 	$this->tombstone  .= " " .$this->artworkQuery[0]['CourtesyStatement']. " ";}
					if($this->artworkQuery[0]['Location']){ 			$this->tombstone  .= " " .$this->artworkQuery[0]['Location']. " ";}
				}

				//body content
				if(!$result['BodyContent']){
					//handle error
				} else {
					$this->bodyContent = nl2p($result['BodyContent']);
				}
				//build title
				if($result['Title']){ $this->title = nl2br($result['Title']);}

				//build start date and end date
				if($result['StartDate'] && $result['EndDate']){ 
					$this->startDate = date('F d, Y', strtotime($result['StartDate']));
					$this->endDate = date('F d, Y', strtotime($result['EndDate']));
				}				
			}
		}
	}

?>