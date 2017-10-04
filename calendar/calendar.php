<?php
	include_once('../fns/functions.php');
	include_once('../class/classes.php');

	//calendar
	$getName = explode("/",$_SERVER['REQUEST_URI']);

	$page = new CalendarPage($getName[3]); 

?>