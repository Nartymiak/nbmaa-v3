<?php

	abstract class Page {

		abstract protected function makeBody();
		abstract protected function makeHTMLElements($result);
		abstract protected function makeSubNav();

		private $navCategories = array();
		private $navCategoryLinks = array();

		// print an HTML header
		protected function HTMLheader($title = '') {
		?>
			<html>
				<!DOCTYPE html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">

					<title> NBMAA | <?php echo str_replace(array("\r", "\n"), '',$title); ?></title>
					<!-- css -->
					<link href="<? echo $GLOBALS['rootDirectory'] ?>/css/nbmaa3.css" rel="stylesheet" type="text/css" />
					<!-- font -->
					<link href='http://fonts.googleapis.com/css?family=Lato:100,400,700,100italic,400italic,700italic' rel='stylesheet' type='text/css'>
					<link rel="stylesheet" href="<? echo $GLOBALS['rootDirectory'] ?>/css/font-awesome.min.css">
					<!-- jquery -->
					<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/jquery-1.11.1.js"></script>
				</head>
			<body>
				<!--<div id="grid"></div>-->
		<?
		}

		/** prints the nav header **/
		protected function nav(){
			
			$logoFile = "nbmaa-white-logo.png";
			$logoBGColor = "rgba(71, 61, 55, .7)";
			$exhibitionsResult = queryCalendarPageExhibitions();
			$exhibitionImage;
			$exhibitonGallery;
			$randomInt;

			// get the links in the main Nav
			if($mainNav = queryNav()) {
				// retrieve the nav categories from the db
				foreach($mainNav as $title){
					if($temp = queryReference("NAV_CATEGORY", "NavTitle", $title['Title'])){
						array_push($this->navCategories, $temp);
					}
				}
			}

			// get all the categories for each of the links in the main Nav
			if($this->navCategories!=NULL){
				// retrieve the links in the categories
				foreach ($this->navCategories as $category) {
					foreach($category as $categoryLink) {
					if($temp = queryReference("NAV_CATEGORY_LINK", "NavCategoryID", $categoryLink['NavCategoryID'])){
						array_push($this->navCategoryLinks, $temp);
						}
					}
				}
			}

			// get the image and gallery reference of the main exibition
			if($exhibitionsResult != NULL){
				// get random exhibition
				$size = sizeof($exhibitionsResult);
				$randomInt = rand(0, $size-1);
				$exhibitionImage 	= 	queryReference('ARTWORK', 'ArtworkID', $exhibitionsResult[$randomInt]['ArtworkReferenceNo']); 
				$exhibitonGallery 	=	queryReference('GALLERY', 'GalleryID', $exhibitionsResult[$randomInt]['GalleryReferenceNo']);
			}

			// keywords in the calendar query
			$keywords = queryKeywords();

			?>
			<header>
				<div class="logo" style="background-color:<?php echo $logoBGColor ?>;"><? echo $this->toImg("logos", $logoFile, "new britain museum of american art logo"); ?></div>
				<nav id="mainNav">
					<div class="menuItem visitLink">
						<a>VISIT</a>

						<div class="dropDown">
							<div class="logoSpace"><!-- place holder for under the logo --></div>
							<div class="left">
							
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[0])!=0){		
									echo "<div class=\"navCategory\">" . $this->navCategories[0][0]["IconHTML"] ."".$this->navCategories[0][0]["Title"] ."</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[0][0]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="middle">
							
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[0])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[0][1]["IconHTML"]."".$this->navCategories[0][1]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[0][1]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="right">
								
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[0])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[0][2]["IconHTML"]."".$this->navCategories[0][2]["Title"] ."</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[0][2]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="clear"></div>
						</div>
					</div>

					<div class="menuItem exhibitionLink">
						<a>EXHIBITION</a>

						<div class="dropDown">
							<div class="logoSpace"><!-- place holder for under the logo --></div>
							<div class="left">

							<?php
								// write the nav category header
								if(sizeof($this->navCategories[1])!=0){	

									echo "<div class=\"navCategory\">" .$this->navCategories[1][0]["IconHTML"]."".$this->navCategories[1][0]["Title"]. "</div>" ; 

									//get the image and data for the main exhibition, write the html
									if($exhibitionImage != NULL) { 

										echo 	"<div class=\"image\" style=\"background-image:url('" .$GLOBALS['rootDirectory']. "/images/exhibition-page-images/" .$exhibitionImage[0]['ImgFilePath']. "');\">

												</div>";
									}

									//write each link
									$this->writeNavLinks($this->navCategories[1][0]["NavCategoryID"]);
								
									// write the caption for the exhibition in a floating div
									if($exhibitionsResult[0] != NULL && $exhibitionImage != NULL) {

										echo 	"<div class=\"exhibitonCaption\">
													<div class=\"inner\">
														<h6>" .$exhibitionsResult[$randomInt]['Title']. "</h6>
														<p class=\"title\">" .$exhibitonGallery[0]['NickName']. "</p>
													</div>
												</div>";
									}
								}
									
								
							?>
								<div class="clear"></div>
							</div>
							<div class="right">
								
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[1])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[1][1]["IconHTML"]."".$this->navCategories[1][1]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[1][1]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="clear"></div>
						</div>
					</div>
					
					<!-- calendar link -->
					<div class="menuItem calendarLink">
						<a href="http://artofwineandfood.org/nbmaa3/calendar/today">CALENDAR</a>
						
						<div class="dropDown">
							<form action="http://artofwineandfood.org/nbmaa3/calendar/today" method="post">
								<div class="logoSpace"><!-- place holder for under the logo --></div>
								<div class="middle">
									<div class="calendarWrapper">
										<!-- javascript calendar -->
										<a class="leftArrow"></a>
										<div class="container">
											<div class="threeCalendars">
												<div class="oldCalendar">
												</div>
												<div class="calendar">
												</div>
												<div class="newCalendar">
												</div>
												<div class="clear"></div>
											</div>
										</div>
										<a class="rightArrow"></a>
										<div class="clear"></div>
									</div>
								</div>
								<div class="right keyword">
									<?php 

									// print the keywords from the query
									if(!$keywords){
										// handle error
									} else {
										foreach($keywords as $keyword){ 
										?>
											<label><input class="calInput" type="checkbox" name="keyword[]" value="<?php echo $keyword['KeywordID'] ?>"><?php echo $keyword['Word'] ?></label>
										<?php
										}
									}
									?>
										<label><input class="calInput" type="checkbox" name="keyword[]" value="all">All</label>
									
									<input type="submit" value="Search" class="submit">
									<div class="clear"></div>
								</div>
								<div class="clear"></div>
							</form>
						</div>
					</div>
					
					<div class="menuItem educationLink">
						<a>EDUCATION</a>

						<div class="dropDown">
							<div class="logoSpace"><!-- place holder for under the logo --></div>
							<div class="left">
							
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[2])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[2][0]["IconHTML"]."".$this->navCategories[2][0]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[2][0]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="middle">
							
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[2])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[2][1]["IconHTML"]."".$this->navCategories[2][1]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[2][1]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="right">
								
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[2])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[2][2]["IconHTML"]."".$this->navCategories[2][2]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[2][2]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="clear"></div>
						</div>
					</div>

					<div class="menuItem supportLink">
						<a>SUPPORT US</a>

						<div class="dropDown">
							<div class="logoSpace"><!-- place holder for under the logo --></div>
							<div class="left">
							
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[3])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[3][0]["IconHTML"]."".$this->navCategories[3][0]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[3][0]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="middle">
							
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[3])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[3][1]["IconHTML"]."".$this->navCategories[3][1]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[3][1]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="right">
								
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[3])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[3][2]["IconHTML"]."".$this->navCategories[3][2]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[3][2]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="clear"></div>
						</div>
					</div>
					<a href="shop.html">SHOP</a>
				</nav>
			</header>
			<h1>NEW BRITAIN MUSEUM OF AMERICAN ART</h1>
		<?
		}

		/** prints the footer **/
		protected function HTMLfooter(){
		
			// get the year and the month
			if(!$_POST['month']) {
				List($y,$m) = explode(" ", date("Y m"));
			} else {
				List($m,$y) = explode("-", $_POST['month']);
			}
		?>
			<footer>
				<div class="wrapper">
					<div class="column">
						<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/about">About</a>
						<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/contact-us">Contact</a>
						<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/employment">Employment</a>
					</div>
					<div class="column right">
						<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/opportunities-for-artists">Opportunities for Artist</a>
						<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/accessibility">Accessibility</a>
						<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/media">Marketing Material</a>
					</div>
					<div class="columnTwo">
						<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/giving">How to Donate to the Museum</a>
						<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/giving">Annual Report</a>
					</div>
					<div class="columnTwo">
						<a href=""><img src="<? echo $GLOBALS['rootDirectory']. "/images/sponsors/ctvisit-logo-1.jpg" ?>"></a>
					</div>
					<div class="clear"></clear>
				</div>
			</footer>
			<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/menu.js"></script>
			<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/calendar.js"></script>
			<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/color.js"></script>
			<script type="text/javascript">

				$(document).ready(function() { 

					menu(); // menu.js
					makeCalendar(<?php echo $y. ", " .$m ?>); // calendar.js
					logo();

					$(document.getElementById("logoBg")).load(function() {
						getAverageRGB(document.getElementById("logoBg"));
					});

				});

			</script>
		</body>
	</html>
		<?
		}

		/** prints cta in the middle of the page **/
		protected function cta($link){ ?>
			<div class="cta">
				<? 	echo "<a href=" .$link. "\">" ;
					echo $this->toImg("cta-images", "cta.jpg", "nbmaa-cta");
					echo "</a>\r\n"; 
				?>
			</div>
		<? 	echo "\r\n";
		}

		/** 
		* creates a string representing an img element
		* @param 	dir 		The directory of the image. It has to be found in the images directory.
		* @param 	fileName 	The filename of the image
		* @param 	alt 		The data for alt attribute
		* @return 	String 		String representing an img element
		*/
		protected function toImg($dir, $fileName, $alt){

			if($alt==NULL || $alt==""){	$alt = "The New Britain Museum of American Art";}
			$string = "<img src=\"" .$GLOBALS['rootDirectory']. "/images/" .$dir. "/" .$fileName. "\" title=\"" .$alt. "\"> ";

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

		protected function writeNavLinks($navCategoryID){
			//write each link
			echo "<div class=\"navCategoryLinks\">";
			foreach($this->navCategoryLinks as $linkSet){
				foreach($linkSet as $link){
					if($link['NavCategoryID'] == $navCategoryID) {
						echo "<a href=\"".$GLOBALS['rootDirectory']. "/museum-of-american-art/" .$link['Link']. "\">".$link['Title']. "</a>";
					}
				}
			}
			echo "</div>";
		}

		protected function buildTombstone($artwork, $artistNames, $artworkQuery){

			$result;

			// buld tombstone
			if($artwork['TombstoneOverride']){
				$result .= $TombstoneOverride;
			} else {
				if($artistNames){							$result  .= $artistNames. ", "; }
				if($artworkQuery['Title']){ 				$result  .= "<i> " .$artworkQuery['Title']. "</i>, "; }
				if($artworkQuery['DateCreated']){ 		$result  .= " " .date('F d, Y', strtotime($artworkQuery['DateCreated'])). ", ";}
				if($artworkQuery['Medium']){ 			$result  .= " " .ucfirst(strtolower($artworkQuery['Medium'])). ", ";}
				if($artworkQuery['Dimensions']){ 		$result  .= " " .$artworkQuery['Dimensions']. ", ";}
				if($artworkQuery['CourtesyStatement']){ 	$result  .= " " .$artworkQuery['CourtesyStatement']. " ";}
				if($artworkQuery['Location']){ 			$result  .= " " .$artworkQuery['Location']. " ";}
			}

			return $result;
		}

	}

	class StaticPage extends Page{

		private $subNavLinksQuery=array();
		private $NavCategoryLinkQuery;

		private $title;
		private $primaryImage;
		private $imagePath;
		private $imageCaption;
		private $bodyContent;

		function __construct($url){
			$result = queryStaticPage($url);
			$this->HTMLheader($result['Title']);
			$this->makeHTMLElements($result);
			$this->makeBody();
			$this->HTMLfooter();
			
		}

		function makeBody(){ // STATIC PAGE function

			//full-width background
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n
													     	<img style=\"display:none;\" id=\"logoBg\" src=\"" .$this->imagePath. "?".microtime(). "\">\r\n" ;
			}

			// wrapper
			echo "		<div class=\"wrapper\" id=\"event\">\r\n";
			
			// header section
			$this->nav();

			//main section
			echo "		<div class=\"mainSection\">\r\n";
			
			//sub navigation section
			$this->makeSubNav();

			// right column
			echo "		<div class=\"rightColumn\">\r\n";
			if($this->title) {						echo "		<h2>" .$this->title. "</h2>\r\n"; } 
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

		function makeHTMLElements($result){ // STATIC PAGE function

			if(!$result){
				// handle error
			} else {

				$this->subNavLinksQuery = querySubNav($result['Link']);



				// build the html img element for the main image
				if(!$result['ImgFilePath']){

				// handle error
					$imgFile = array('nbmaa-museum-spring.jpg', 'nbmaa-museum-evening.jpg', 'nbmaa-museum-night.jpg', 'nbmaa-museum-day.jpg' );
					$randomInt = rand(0, sizeof($imgFile)-1);
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/static-page-images/" .$imgFile[$randomInt];

				} else {
					//create HTML alt attribute text
					if($result['Title']){	$alt = $result['Title'];}
					//create primary image
					$this->primaryImage=$this->toImg("static-page-images", $result['ImgFilePath'], $alt );
					//create image path for background
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/static-page-images/" .$result['ImgFilePath'];
				}

				// title
				if($result['Title']){ $this->title = nl2br($result['Title']);}

				// build the html img element for the main image
				if(!$result['ImgFilePath']){
				// handle error
				} else {
					//create HTML alt attribute text
					if($result['Title']){	$alt = $result['Title'];}
					//create primary image
					$this->primaryImage=$this->toImg("static-page-images", $result['ImgFilePath'], $alt );
					//create image path for background
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/static-page-images/" .$result['ImgFilePath'];
				}

				// caption
				if($result['ImgCaption']) {	$this->imageCaption = nl2br($result['ImgCaption']);}

				// body content (called Body in STATIC_PAGE table)
				if($result['Body']){ $this->bodyContent = $result['Body'];}

			}

		}

		function makeSubNav(){ // STATIC PAGE function

			echo "		<nav class=\"subNav\">\r\n";

			if($this->subNavLinksQuery){

				echo "		<h3 class=\"top\">More</h3>\r\n";

				foreach ($this->subNavLinksQuery as $link) {
					echo "		<a href=\"" .$GLOBALS['rootDirectory']. "/museum-of-american-art/" .$link['Link']. "\">" .$link['Title']. "</a>\r\n";
				}
			}

			// consistent links
			echo "		<h3>Extra</h3>";
			echo "		<a href=\"\">Join our Email List</a>\r\n";
			echo "		<a href=\"\">Share</a>\r\n";

			echo "		</nav>\r\n";
		}
	}

	class CalendarPage extends Page{

		private $imagePath;
		private $calendarEvents = array();
		private $currentExhibitons = array();
		private $events;

		function __construct($url){

			//first, query the db
			$result = queryCalendarPageEvents();
			$this->HTMLheader($url);
			$this->makeHTMLElements($result);
			$this->makeBody();
			$this->HTMLfooter();

		}

		protected function makeBody(){ // CALENDAR PAGE function
	
			//full-width background
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n
												<img style=\"display:none;\" id=\"logoBg\" src=\"" .$this->imagePath. "?".microtime(). "\">\r\n" ;
			}

			// wrapper
			echo "		<div class=\"wrapper\" id=\"calendar\">\r\n";

			// header section
			$this->nav();
			
			// main section
			echo "		<div class=\"mainSection\">\r\n";

			// left column
			echo "		<div class=\"leftColumn\">\r\n";
			echo "		<h2>CURRENT EXHIBITIONS</h2>";
			// print current exhibitions
			if(!$this->currentExhibitons){
				//handle error
			} else {

				// loop through the array to access the elements
				foreach ($this->currentExhibitons as $element) {
						
					echo $element;
				}
			}
			
			echo "		</div><!-- end left -->\r\n";

			//right column
			echo "		<div class=\"rightColumn\">\r\n";
			echo "		<h2>EVENTS</h2>";
			
			// print calendar events
			if(!$this->calendarEvents){
				//handle error
			} else {

				// loop through the array to access the elements
				foreach ($this->calendarEvents as $element) {
						
					echo $element;
				}
			}


			echo "		</div><!-- end right -->\r\n";
			echo "		<div class=\"clear\"></div>";
			echo "		</div><!-- end mainSection -->\r\n";
			echo "		</div><!-- end wrapper -->\r\n";

			//$this->makeXMLHttpRequestSection();
		}

		protected function makeHTMLElements($result){ // CALENDAR PAGE function



			$events = array();
			$exhibitions = array();
			$receptions;
			$classes;
			$firstExhibitionImage;
			// query for exhibitions
			$exhibitionsResult = queryCalendarPageExhibitions();
			$todaysDate = date('Y-m-d');

			// build the events array by querrying the EVENT table using
			// EVENT_DATE_TIMES array for reference
			if($result == null){
				// handle error

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
							array_push($this->calendarEvents, "<div class=\"date\"><h5>Today's Events</h5></div>");

						} else {

							array_push($this->calendarEvents, "<div class=\"date\"><h5>" .date("F d, Y", $timestamp). "</h5></div>");

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
						$topImg = $this->toImg("event-page-images", $tuple['ImgFilePath'], $tuple['Title']); 
						$class = "showImg";
						array_push($alreadyHasImage, $tuple['ImgFilePath']);
					
					// if for some reason there is no image file path
					} else if(!$tuple['ImgFilePath'] && $nextImage == true){
						$doNextImage = true;
					}

					// print each element
					array_push($this->calendarEvents, "

						<div class=\"calendarEventsWrapper\">
							<a href=\"" .$GLOBALS['rootDirectory']. "/event/" .$tuple['Link']. "\">
								<div class=\"calendarElement ".$class."\">
									".$topImg."
									<h3>" .$tuple['EventTitle']. "</h3>
									<h4>" .$tuple['TypeTitle']. "</h4>
									<p>" .date("g:i a", strtotime($tuple['StartTime'])). " to " .date("g:i a", strtotime($tuple['EndTime'])). "</p>
									<p>" .shortenText($tuple['Description']). "</p>
								</div>
							</a>
						</div>
					");

					$nextImage = false;
				}	

			}

			if(!$exhibitionsResult[0]){
				//handle error

			} else {

				$firstExhibitionImage = queryReference('ARTWORK', 'ArtworkID', $exhibitionsResult[0]['ArtworkReferenceNo']);
				// build the html img element for the main image

				if(!$firstExhibitionImage[0]['ImgFilePath']){
				// handle error
				} else {
					//create HTML alt attribute text
					if($firstExhibitionImage[0]['Title']){	$alt = $firstExhibitionImage[0]['Title'];}
					//create image path for background
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/exhibition-page-images/" .$firstExhibitionImage[0]['ImgFilePath'];
				}
			}


			// build the exhibitions array
			if(!$exhibitionsResult){
				// handle error

			} else {

				$count=0;

				foreach($exhibitionsResult as $tuple){

					$class = "";
					$topImg = "";

					//if its the first element, add the .top class and create the image element
					if($count==0){ 						
						$class = "showImg";
						$topImg = $this->toImg("exhibition-page-images", $firstExhibitionImage[0]['ImgFilePath'], $tuple['Title']);
					}

					array_push($this->currentExhibitons, "
				
						<div class=\"calendarEventsWrapper\">
							<a href=\"" .$GLOBALS['rootDirectory']. "/exhibition/" .$tuple['Link']. "\">
								<div class=\"calendarElement ".$class."\">
									".$topImg."
									<h3>" .$tuple['Title']. "</h3>
									<p>" .shortenText($tuple['BodyContent']). "</p>
								</div>
							</a>
						</div>
					");
				
				$count++;	
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
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n
													     	<img style=\"display:none;\" id=\"logoBg\" src=\"" .$this->imagePath. "?".microtime(). "\">\r\n" ;
			}

			// wrapper
			echo "		<div class=\"wrapper\" id=\"event\">\r\n";
			
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
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n
													     	<img style=\"display:none;\" id=\"logoBg\" src=\"" .$this->imagePath. "?".microtime(). "\">\r\n" ;
			}

			// wrapper
			echo "		<div class=\"wrapper\" id=\"exhibition\">\r\n";
			
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
				echo "		<h3>Related Events</h3>\r\n";
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
				$this->tombstone = $this->buildTombstone($this->artwork[0], $this->artistNames, $this->artworkQuery[0]);

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