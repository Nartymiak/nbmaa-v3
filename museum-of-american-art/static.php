<?php
	include_once('../fns/functions.php');
	include_once('../class/classes.php');

	//static
	$getName = explode("/",$_SERVER['REQUEST_URI']);

	$link = explode("?", $getName[2]);

	$page = new StaticPage($link[0]);

?>