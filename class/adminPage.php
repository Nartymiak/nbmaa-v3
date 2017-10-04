<?php
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

class EditAdminPage extends AdminPage{

	function __construct($urlDetail){

		$this->HTMLheader("Select an Event");
		$this->nav();
		$this->mainWindow($urlDetail);
		//script();
		$this->HTMLfooter();
	}

	protected function mainWindow($urlDetail) {

		?>		<div class="mainWindow">
					<div class="container">
		<?php 			$this->sidebar();		
						$this->content($urlDetail);
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
				</div>
		<?php
	}

	protected function content($urlDetail) {

		$elements= array();

		if(!$urlDetail[1]) {
			//handle error
		} else {

			

			?>		<div class="content">
						<h2><?php echo $urlDetail[1] ?></h2>
						<div class="element attributes">
							<div class="type">Type</div>
							<div class="title">Title</div>
							<div class="date">Date</div>
							<div class="clear"></div>
						</div>
			
			<?php

			if($elements = adminQuery()) {

				foreach($elements as $element) {

					if(strlen($element['EventTitle']) > 50) {
						$element['EventTitle'] =  shortenByChar($element['EventTitle'], 50);
					}

					echo "<div class=\"element\">\r\n";
					echo "<a href=\"" .$GLOBALS['adminFilePath']. "/event?" .$element['Link']. "\">\r\n";
					echo "	<div class=\"type\">" .$element['TypeTitle']. "</div>\r\n";
					echo "	<div class=\"title\">" .$element['EventTitle']. "</div>\r\n";
					echo "	<div class=\"date\">" .date("m-d-y", strtotime($element['MAX(StartDate)'])). "</div>\r\n";
					echo "</a>\r\n";
					echo "</div>\r\n";
				}
			} else {

				// handle error
			}



			?>		</div>
			<?php
		}

	}
}

class EventAdminPage extends AdminPage {

	function __construct($urlDetail){

		$this->HTMLheader("Edit an Event");
		$this->nav();
		$this->mainWindow($urlDetail);
		//script();
		$this->HTMLfooter();
	}

	protected function mainWindow($urlDetail){

		?>		<div class="mainWindow">
					<div class="container">
		<?php 			$this->sidebar();		
						$this->content($urlDetail);
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
				</div>
		<?php
	}

	protected function content($urlDetail) {

		$element = array();

		if($_POST){
			writeEvent();
			
		}

		if(!$urlDetail[1]) {
			//handle error
		} else {


			?>		<div class="content">
						<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/tinymce/tinymce.min.js"></script>
						<script>tinymce.init({selector:'textarea', height : "400px", plugins: "code link wordcount", force_br_newlines : "false"});</script>			
			<?php

						if($element = queryEventPage($urlDetail[1])){

							

			?>			 	<form action="<?php echo $GLOBALS['adminFilePath']. "/event?" .$element['Link'] ?>" method="post">
								<input type="hidden" name="EventID" value="<?php echo $element['EventID'] ?>">
								<label><p>Title</p><input type="text" name="Title" value="<?php echo $element['Title'] ?>"></label>
								<label><p>Background Image File Name</p><input type="text" name="ImgFilePath" value="<?php echo $element['ImgFilePath'] ?>"></label>
								<label><p>Description</p> <textarea name="Description"><?php echo $element['Description'] ?></textarea></label>
								<label><p>Admission Charge</p> <input type="text" name="AdmissionCharge" value="<?php echo $element['AdmissionCharge'] ?>"></label>

			<?php
								if($dateTimes = queryReferenceWithLimit("EVENT_DATE_TIMES", "EventID", $element['EventID'], 50)) {

									// sort dateTimes first
    								usort($dateTimes, 'date_compare_descend');

									echo "<div class=\"schedule\">\r\n";
									echo "	<label><p>Event Schedule</p></label>\r\n";
									echo "	<div class=\"dateTime\">\r\n";
									echo "		<label class=\"date\"> Start Date</label><label class=\"date\">End Date</label><label class=\"time\">Start Time</label><label class=\"time\">End Time</label>\r\n";
									echo "	</div>\r\n";
									foreach($dateTimes as $dateTime) {
										echo "	<div class=\"dateTime\">\r\n";
										echo "		<label class=\"date\"><input type=\"date\" name =\"StartDate\" value=\"" .$dateTime['StartDate']. "\"></label>\r\n";
										echo "		<label class=\"date\"><input type=\"date\" name =\"EndDate\" value=\"" .$dateTime['EndDate']. "\"></label>\r\n";
										echo "		<label class=\"time\"><input type=\"time\" name =\"StartTime\" value=\"" .$dateTime['StartTime']. "\"></label>\r\n";
										echo "		<label class=\"time\"><input type=\"time\" name =\"EndTime\" value=\"" .$dateTime['EndTime']. "\"></label>\r\n";
										echo "		<label class=\"delete\"><i class=\"fa fa-times\"></i></label>\r\n";
										echo "	</div>\r\n";
									}

									echo "	<div class=\"dateTime\">\r\n";
									echo "		<a id=\"addTime\">add new time &nbsp; <i class=\"fa fa-plus\"></i></a>\r\n";
									echo "	</div>\r\n";
									echo "</div><!-- end schedule -->\r\n";

								}
			?>


								<label><input class="submit" type="submit" value="save"></label>
							</form>
							

			<?php		} else {
							// handle error
						}


			?>		</div>
			<?php
		}

	}

}

?>