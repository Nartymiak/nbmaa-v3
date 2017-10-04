<?php
	include_once('../fns/functions.php');
	include_once('../class/classes.php');

	//exhibition
	$getName = explode("/",$_SERVER['REQUEST_URI']);

	$page = new ExhibitionPage($getName[3]); 

?>