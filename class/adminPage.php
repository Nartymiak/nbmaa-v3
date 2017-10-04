<?php
	/** 
	* Abstract class for an admin page
	* Includes abstract methods for creating the header, footer and nav
	*/
	abstract class AdminPage {
		
		private $elementArray = array();



		// print an HTML header
		protected function HTMLheader($title = '') {
		
		?>	<html>
			<!DOCTYPE html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">

					<title> NBMAA | <?php echo str_replace(array("\r", "\n"), '',$title); ?></title>
					<!-- css -->
					<link href="<? echo $GLOBALS['rootDirectory'] ?>/css/admin.css" rel="stylesheet" type="text/css" />
					<!-- font -->
					<link href='http://fonts.googleapis.com/css?family=Lato:100,400,700,400italic' rel='stylesheet' type='text/css'>
					<link rel="stylesheet" href="<? echo $GLOBALS['rootDirectory'] ?>/css/font-awesome.min.css">
					<!-- jquery -->
					<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/jquery-1.11.1.js"></script>
				</head>
				<body>
					<header>
						<div class="container">
							<h1>The New Britain Museum of American Art</h1>
						</div>
					</header>
		<?php
		}
		
		/** prints the footer **/
		protected function HTMLfooter(){
		
		?>			<footer>
					</footer>
				</body>
			</html>
		<?php
		}

		/** prints the nav **/
		protected function nav() {
		
		?>			<nav>
						<div class="container">
							<a href="<?php echo $GLOBALS['adminFilePath'] ?>/edit">edit</a>
							<a href="">add new</a>
						</div>
					</nav>
		<?php
		}

	}

	/** 
	*Main Admin Page, for the user the start with
	*/
	class MainAdminPage extends AdminPage {

		function __construct(){

			$this->HTMLheader("Main Window");
			$this->nav();
			//sideBar();
			//elements();
			//script();
			$this->HTMLfooter();
		}

	}

	/** 
	* Shows a list of pages to edit
	*/
	class EditAdminPage extends AdminPage{

		private $urlDetail;

		/** 
		* the constructor
		* @param 	Array		An array of Strings of values deliminated by ? in the url
		*/
		function __construct($urlDetail){

			$this->urlDetail = $urlDetail;
			$this->HTMLheader("Edit an Item on the List");
			$this->nav();
			$this->mainWindow();
			//script();
			$this->HTMLfooter();
		}

		/** 
		* creates the overall window that houses the sidebar and content window
		* @param 	Array		An array of Strings of values deliminated by ? in the url, passed in from the constructor
		*/
		protected function mainWindow() {

			?>		<div class="mainWindow">
						<div class="container">
			<?php 			$this->sidebar();		
							$this->content($this->urlDetail);
			?>
						<div class="clear"></div>
						</div>
					</div>
			<?php
			
		}

		/**
		*Creates the sidebar within the main window
		*/
		protected function sidebar() {

			?>		<div class="sidebar">
						<a href="<?php echo $GLOBALS['adminFilePath'] ?>/edit?exhibitions">exhibitions</a>
						<a href="<?php echo $GLOBALS['adminFilePath'] ?>/edit?events">events</a>
						<a href="<?php echo $GLOBALS['adminFilePath'] ?>/edit?static-pages">static pages</a>
					</div>
			<?php
		}

		/** 
		* creates the content section within the main window
		* @param 	Array		An array of Strings of values deliminated by ? in the url
		*/
		protected function content() {

			// query result variable
			$elements= array();

			if(!$this->urlDetail[1]) {
				//handle error
			
			} else {

				// for static pages
				if($this->urlDetail[1]=="static-pages"){

					// create the header for the content section
					?>		<div class="content">
								<h2><?php echo $this->urlDetail[1] ?></h2>
								<div class="element attributes">
									<div class="type">&nbsp;</div>
									<div class="title">Title</div>
									<div class="date">Date</div>
									<div class="clear"></div>
								</div>
					
					<?php

					// query the db
					if($elements = adminQuery('static')) {

						// print result
						foreach($elements as $element) {

							// check the title, if too long, truncate it and add ...
							if(strlen($element['EventTitle']) > 50) {
								$element['Title'] =  shortenByChar($element['Title'], 50);
							}

							echo "<div class=\"element\">\r\n";
							echo "<a href=\"" .$GLOBALS['adminFilePath']. "/static-page?" .$element['Link']. "\">\r\n";
							echo "	<div class=\"type\">&nbsp;</div>\r\n";
							echo "	<div class=\"title\">" .$element['Title']. "</div>\r\n";
							echo "	<div class=\"date\">" .date("m-d-y", strtotime($element['ChangedOn'])). "</div>\r\n";
							echo "</a>\r\n";
							echo "</div>\r\n";
						}
					} else {

						// handle error
					}

					echo "	</div>\r\n <!-- end content --> \r\n ";

				// for events
				}else if($this->urlDetail[1]=="events"){
					// create the header for the content section
					?>		<div class="content">
								<h2><?php echo $this->urlDetail[1] ?></h2>
								<div class="element attributes">
									<div class="type">Type</div>
									<div class="title">Title</div>
									<div class="date">Date</div>
									<div class="clear"></div>
								</div>
					
					<?php

					// query the db
					if($elements = adminQuery('event')) {

						// print result
						foreach($elements as $element) {

							// check the title, if too long, truncate it and add ...
							if(strlen($element['EventTitle']) > 50) {
								$element['EventTitle'] =  shortenByChar($element['EventTitle'], 50);
							}

							echo "<div class=\"element\">\r\n";
							echo "<a href=\"" .$GLOBALS['adminFilePath']. "/event?" .$element['Link']. "\">\r\n";
							echo "	<div class=\"type\">" .$element['TypeTitle']. "</div>\r\n";
							echo "	<div class=\"title\">" .$element['EventTitle']. "</div>\r\n";
							echo "	<div class=\"date\">" .date("m-d-y", strtotime($element['CreatedOn'])). "</div>\r\n";
							echo "</a>\r\n";
							echo "</div>\r\n";
						}
					} else {

						// handle error
					}

					echo "	</div>\r\n <!-- end content --> \r\n ";
				}
			}

		}
	}

/** 
	*Displays an Event's details after a user clicks on one from the edit page
	*/
	class EventAdminPage extends AdminPage {

		private $urlDetail;
		private $addStartDate;
		private $addEndDate;
		private $addStartTime;
		private $addEndTime;

		/** 
		* the constructor
		* @param 	Array		An array of Strings of values deliminated by ? in the url
		*/
		function __construct($urlDetail){

			$this->urlDetail = $urlDetail;
			$this->HTMLheader("Edit an Event");
			$this->nav();
			$this->mainWindow();
			$this->script();
			$this->HTMLfooter();
		}

		protected function addStartDate($date){
			$this->addStartDate = $date;
		}

		protected function addEndDate($date){
			$this->addEndDate = $date;
		}

		protected function addStartTime($time){
			$this->addStartTime = $time;
		}

		protected function addEndTime($time){
			$this->addEndTime = $time;
		}

		protected function getStartDate(){
			return $this->addStartDate;
		}

		protected function getEndDate(){
			return $this->addEndDate;
		}

		protected function getStartTime(){
			return $this->addStartTime;
		}

		protected function getEndTime(){
			return $this->addEndTime;
		}

		protected function setDateTimeForAddScript($startDate, $endDate, $startTime, $endTime){
			$this->addStartDate($startDate);
			$this->addEndDate($endDate);
			$this->addStartTime($startTime);
			$this->addEndTime($endTime);

		}

		protected function getDateTimeForAddScript(){
			$result=array();
			$result['startDate'] = $this->getStartDate();
			$result['endDate'] = $this->getEndDate();
			$result['startTime'] = $this->getStartTime();
			$result['endTime'] = $this->getEndTime();
			return $result;
		}

		protected function mainWindow(){

			?>		<div class="mainWindow">
						<div class="container">
			<?php 			$this->sidebar();		
							$this->content();
			?>
						<div class="clear"></div>
						</div>
					</div>
			<?php


		}

		protected function sidebar() {

			?>		<div class="sidebar">
						<a href="<?php echo $GLOBALS['adminFilePath'] ?>/edit?exhibitions">exhibitions</a>
						<a href="<?php echo $GLOBALS['adminFilePath'] ?>/edit?events">events</a>
						<a href="<?php echo $GLOBALS['adminFilePath'] ?>/edit?static-pages">static pages</a>
					</div>
			<?php
		}

		

		protected function content() {

			$element = array();
			
			// these are for figuring out the number of eventdates to show / limits the query result
			$startEventDateTimes = 0;
			$eventDateTimesInterval = 50;

			// get limit start
			if($this->urlDetail[2]){	$startEventDateTimes =intval($this->urlDetail[2]); }

			if($_POST){
				writeEvent();
				
			}

			if(!$this->urlDetail[1]) {
				//handle error
			} else {


				?>		<div class="content">
							<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/tinymce/tinymce.min.js"></script>
							<script>tinymce.init({selector:'textarea', height : "400px", plugins: "code link wordcount", force_br_newlines : "false", relative_urls : false, remove_script_host : false, block_formats: "Paragraph=p;Header 2=h2;Header 3=h3"});</script>			
				<?php

							if($element = queryEventPage($this->urlDetail[1])){

								

				?>			 	
								<a class="button" target="_blank" href="<?php echo $GLOBALS['rootDirectory']. "/event/" .$element['Link'] ?>"> preview </a>
								<form action="<?php echo $GLOBALS['adminFilePath']. "/event?" .$element['Link'] ?>" method="post">
									<input type="hidden" name="EventID" value="<?php echo $element['EventID'] ?>">
									<label><p>Title</p><input type="text" name="Title" value="<?php echo $element['Title'] ?>"></label>
									<label><p>Background Image File Name</p><input type="text" name="ImgFilePath" value="<?php echo $element['ImgFilePath'] ?>"></label>
									<label><p>Description</p> <textarea name="Description"><?php echo $element['Description'] ?></textarea></label>
									<label><p>Admission Charge</p> <input type="text" name="AdmissionCharge" value="<?php echo $element['AdmissionCharge'] ?>"></label>

									<!-- event times section -->
									<div class="schedule">
										<label><p>Event Schedule</p></label>
										<div class="dateTime">
											<label class="date">Start Date</label><label class="date">End Date</label><label class="time">Start Time</label><label class="time">End Time</label><label class="delete">Delete</label>
										</div>
										<div class="add"><a style="display:block;" id="addTime">add new time &nbsp; <i class="fa fa-plus"></i></a></div>

				<?php 				if($dateTimes = queryReferencePortion("EVENT_DATE_TIMES", "EventID", $element['EventID'], "StartDate DESC", $startEventDateTimes, $eventDateTimesInterval)) {

										// sort dateTimes first
	    								usort($dateTimes, 'date_compare_descend');

	    								// use $i to make input names unique
	    								$i=0;

	    								$this->setDateTimeForAddScript($dateTimes[0]['StartDate'], $dateTimes[0]['EndDate'], $dateTimes[0]['StartTime'], $dateTimes[0]['EndTime']);


										foreach($dateTimes as $dateTime) {
											echo "	<div class=\"dateTime\">\r\n";
											echo "		<label class=\"date\">\r\n
															<input type=\"date\" name =\"dateTimes[".$i."][StartDate]\" value=\"" .$dateTime['StartDate']. "\">\r\n
															<input type=\"hidden\" name =\"dateTimes[".$i."][oldStartDate]\" value=\"" .$dateTime['StartDate']. "\">\r\n
														</label>\r\n";
											
											if($dateTime['EndDate'] == '0000-00-00'){
												$endDate = $dateTime['StartDate'];
											} else {
												$endDate = $dateTime['EndDate'];
											}

											echo "		<label class=\"date\">\r\n
															<input type=\"date\" name =\"dateTimes[".$i."][EndDate]\" value=\"" .$endDate. "\">
															<input type=\"hidden\" name =\"dateTimes[".$i."][oldEndDate]\" value=\"" .$dendDate. "\">
														</label>\r\n";
											
											echo "		<label class=\"time\">\r\n
															<input type=\"time\" name =\"dateTimes[".$i."][StartTime]\" value=\"" .$dateTime['StartTime']. "\">\r\n
															<input type=\"hidden\" name =\"dateTimes[".$i."][oldStartTime]\" value=\"" .$dateTime['StartTime']. "\">\r\n
														</label>\r\n";
											
											echo "		<label class=\"time\">\r\n
															<input type=\"time\" name =\"dateTimes[".$i."][EndTime]\" value=\"" .$dateTime['EndTime']. "\">\r\n
															<input type=\"hidden\" name =\"dateTimes[".$i."][oldEndTime]\" value=\"" .$dateTime['EndTime']. "\">\r\n
														</label>\r\n";
											
											echo "		<input type=\"checkbox\" id=\"delete".$i."\" name=\"delete[".$i."]\" value=\"d\">\r\n
														<label for=\"delete".$i."\" class=\"delete\">\r\n
															<i class=\"fa fa-times\"></i></label>\r\n
														</label>\r\n";
											echo "	</div>\r\n";
										
										$i++;
										}

										echo "	<div class=\"dateTime\">\r\n";
										
										// button to display event times over the query limit
										if($startEventDateTimes >= $eventDateTimesInterval) {
											echo "	<a href=\"" .$GLOBALS['adminFilePath']. "/event?" .$this->urlDetail[1]. "?" .($startEventDateTimes-$eventDateTimesInterval). "\">prev " .$eventDateTimesInterval. "</a> | \r\n";
										}
										
										// button to display event times over the query limit
										if($eventDateTimesInterval <= count($dateTimes)) {
											echo "		<a href=\"" .$GLOBALS['adminFilePath']. "/event?" .$this->urlDetail[1]. "?" .($startEventDateTimes+$eventDateTimesInterval). "\">next " .$eventDateTimesInterval. "</a>\r\n";
										}

										echo "	</div>\r\n";
									}
				?>

									</div><!-- end schedule -->

									<label><input class="submit" type="submit" value="save"></label>
								</form>
								

				<?php		} else {
								// handle error
							}

				echo "	</div><!-- end content -->\r\n";
			}

		}

		protected function script() {

			if($this->getEndDate() == '0000-00-00'){
				$endDate = $this->getStartDate();
			} else {
				$endDate = $this->getEndDate();
			}

			?>
			<script type="text/javascript">
			<?php 

			echo "  var startDate = new Date(\"" .$this->getStartDate(). "\");\r\n";
			echo "	var endDate = new Date(\"" .$endDate. "\");\r\n";
			echo "	var startTime = \"" .$this->getStartTime(). "\";\r\n";
			echo "	var endTime = \"" .$this->getEndTime(). "\";\r\n";
			
			?>
				//index for add elements
				var i =0;
				var firstClick = 0;

				function dateToString(Date){
				
					var year = Date.getFullYear();
					var month = pad(Date.getMonth()+1);
					var day = pad(Date.getDate());

					return year+'-'+month+'-'+day;
				}

				function stringToDate(string){
					date = new Date();
					str = string.split("-");
					date.setYear(str[0]);
					date.setMonth(str[1]-1);
					date.setDate(str[2]);

					return date;
				}

				// pads a given number with 0 if size is less than 2. ex. 01, 02, 03 etc.
				function pad(num) {  	
			    	var s = "0" + num;
			    	return s.substr(s.length-2);
				}



				$('#addTime').on('click', function(){

					startDate.setDate(startDate.getDate()+8-firstClick);
					endDate.setDate(endDate.getDate()+8-firstClick);
					
					($(this).after('<div class="dateTime"><label class="date"><input class="startDateInput" type="date" name ="add['+i+'][StartDate]" value="'+dateToString(startDate)+'"></label><label class="date"><input class="endDateInput" type="date" name ="add['+i+'][EndDate]" value="'+dateToString(endDate)+'"></label><label class="time"><input type="time" name ="add['+i+'][StartTime]" value="'+startTime+'"></label><label class="time"><input type="time" name ="add['+i+'][EndTime]" value="'+endTime+'"></label><label class="delete deleteAdd"><i class="fa fa-times"></i></label></div>'));
					i++;
					firstClick = 1;

				});

				$('.add').on('click', '.deleteAdd', function(){
					$(this).parent().remove();
					startDate = stringToDate($('.startDateInput').attr('value'));
					endDate = stringToDate($('.endDateInput').attr('value'));

				});

			</script>

		<?php
		}

	}

	/** 
	*Displays an Event's details after a user clicks on one from the edit page
	*/
	class StaticAdminPage extends AdminPage {

		private $urlDetail;
		private $addStartDate;
		private $addEndDate;
		private $addStartTime;
		private $addEndTime;

		/** 
		* the constructor
		* @param 	Array		An array of Strings of values deliminated by ? in the url
		*/
		function __construct($urlDetail){

			$this->urlDetail = $urlDetail;
			$this->HTMLheader("Edit a Static Page");
			$this->nav();
			$this->mainWindow();
			$this->script();
			$this->HTMLfooter();

		}

		protected function mainWindow(){

			?>		<div class="mainWindow">
						<div class="container">
			<?php 			$this->sidebar();		
							$this->content();
			?>
						<div class="clear"></div>
						</div>
					</div>
			<?php


		}

		protected function sidebar() {

			?>		<div class="sidebar">
						<a href="<?php echo $GLOBALS['adminFilePath'] ?>/edit?exhibitions">exhibitions</a>
						<a href="<?php echo $GLOBALS['adminFilePath'] ?>/edit?events">events</a>
						<a href="<?php echo $GLOBALS['adminFilePath'] ?>/edit?static-pages">static pages</a>
					</div>
			<?php
		}

		

		protected function content() {

			$element = array();
			
			// these are for figuring out the number of eventdates to show / limits the query result
			$startEventDateTimes = 0;
			$eventDateTimesInterval = 50;

			// get limit start
			if($this->urlDetail[2]){	$startEventDateTimes =intval($this->urlDetail[2]); }

			if($_POST){
				writeStaticPage();
				
			}

			if(!$this->urlDetail[1]) {
				//handle error
			} else {


				?>		<div class="content">
							<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/tinymce/tinymce.min.js"></script>
							<script>tinymce.init({selector:'textarea', height : "400px", plugins: "code link wordcount", force_br_newlines : "false", relative_urls : false, remove_script_host : false, block_formats: "Paragraph=p;Header 2=h2;Header 3=h3"});</script>			
				<?php

							if($element = queryStaticPage($this->urlDetail[1])){

								

				?>			 	<form action="<?php echo $GLOBALS['adminFilePath']. "/static-page?" .$element['Link'] ?>" method="post">
									<input type="hidden" name="StaticPageID" value="<?php echo $element['StaticPageID'] ?>">
									<label><p>Title</p><input type="text" name="Title" value="<?php echo $element['Title'] ?>"></label>
									<label><p>Background Image File Name</p><input type="text" name="ImgFilePath" value="<?php echo $element['ImgFilePath'] ?>"></label>
									<label><p>Body</p> <textarea name="Body"><?php echo $element['Body'] ?></textarea></label>

									<label><input class="submit" type="submit" value="save"></label>
								</form>
								

				<?php		} else {
								// handle error
							}

				echo "	</div><!-- end content -->\r\n";
			}

		}

		protected function script() {

		}

	}

?>