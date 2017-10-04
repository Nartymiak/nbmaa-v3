<?php
	include_once('../fns/functions.php');
	include_once('../class/classes.php');

	//exhibition
	$getName = explode("/",$_SERVER['REQUEST_URI']);

	$link = explode("?", $getName[2]);

	$page = new ExhibitionPage($link[0]);

?>