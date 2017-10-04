<?php
	include_once('../fns/functions.php');
	include_once('../class/classes.php');
	do_html_header('NBMAA Administrator - Content Builder');

	$spreadSheet;

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