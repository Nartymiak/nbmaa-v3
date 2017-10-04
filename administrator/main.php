<?php
	include_once('../fns/adminFunctions.php');
	include_once('../class/adminClasses.php');
	//do_html_header('NBMAA Administrator - Content Builder');

	$spreadSheet;
	$adminWindow;

	//admin
	// get the third string from the url and break out string after "?"
	$url = explode("/",$_SERVER['REQUEST_URI']);
	$urlDetail = explode("?",$url[3]);

	// check the third string from the url
	switch ($urlDetail[0]) {
		case "edit":
			// use the string after "?"
			$adminWindow = new EditAdminPage($urlDetail);
			break;
		case "event":
			$adminWindow = new EventAdminPage($urlDetail);
			break;
		case "static-page":
			$adminWindow = new StaticAdminPage($urlDetail);
			break;
		default:
			$adminWindow = new MainAdminPage();
	}



	if(!$_FILES) {
?>	
		<form action="main.php" method="post" enctype="multipart/form-data" accept-charset="utf-8">
			<label for="file">Filename:</label>
			<input type="file" name="file" id="file"><br>
			<input type="submit" name="submit" value="Submit">
		</form>
<?
	} else {
		echo "adding file"; 
		$spreadSheet = new SpreadSheet();
	}

?>