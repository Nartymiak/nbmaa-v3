<?php

	function nl2p($str){
		
	    $arr=explode("\n",$str);
	    $out='';

	    for($i=0;$i<count($arr);$i++) {
	        if(strlen(trim($arr[$i]))>0)
	            $out.="		<p>".trim($arr[$i])."</p>\r\n";
	    }
	    return $out;
	}

	function validateDate($date){

    	$d = DateTime::createFromFormat('Y-m-d', $date);
    	return $d && $d->format('Y-m-d') == $date;
	}

	// returns the tuple in an array that matches key=>value
	function getElementByID($array, $key, $value){

		$result = NULL;

		foreach($array as $e){

			if($e[$key] == $value){
				$result = $e;
			}
		}

		return $result;
	}

	//given an array of dates, return the starting and last
	function startAndEndDates($arr){
		$earliest =  date('Y-m-d', 0);
		$latest = date('Y-m-d', 1);
		
		$arraySize = sizeof($arr);
		$result = array();
		
		if($arraySize<= 1){
			$earliest = $arr[0]['StartDate'];
			$latest = $arr[0]['StartDate'];
		}else{
			$earliest = $arr[0]['StartDate'];
			for($i=0;$i<$arraySize;$i++){
				if($arr[$i]['StartDate'] < $earliest){
					$earliest = $arr[$i]['StartDate'];
				}else if($arr[$i]['StartDate'] > $latest){
					$latest = $arr[$i]['StartDate'];
				}
			}
		}
		$result[0]=$earliest;
		$result[1]=$latest;
		return $result;
	}

	function buildDateRange(){
		
		// this gets the first and last day of the current month
		$startDate = date("Y-m-d");
		$endDate = date("Y-m-t");

		$dateArray = array($startDate, $endDate);

		return $dateArray;
	}

	function buildThirtyDayDateRange(){
		
		// this gets the first and last day of the current month
		$startDate = date("Y-m-d");
		$endDate = date('Y-m-d', strtotime("+30 days"));

		$dateArray = array($startDate, $endDate);

		return $dateArray;
	}

	function buildThreeMonthDateRange(){
		
		// this gets the first and last day of the current month
		$startDate = date("Y-m-d");
		$endDate = date('Y-m-d', strtotime("+93 days"));

		$dateArray = array($startDate, $endDate);

		return $dateArray;
	}

	function shortenText($string){

		$string=strip_tags($string);

		$result;

		$text = explode(" ", $string, 25);

		if($text){
			//other thant the <p>, im not sure why—but there seems to be these characters in the string. must remove.
			if(substr($text[0], 0, 5) == "		<p>"){
				$text[0] = substr($text[0], 5);

			}
			for($i=0;$i<count($text)-1;$i++){
				$result .= $text[$i]. " ";
			}

			if($result) { $result .= "..."; }
		}

		return $result;

	}

	function shortenByChar($string, $length) {

		$result;

		$text = str_split($string);

		if($text){

			for($i=0;$i<$length;$i++){
				$result .= $text[$i];
			}

			if($result) { $result .= "..."; }
		}

		return $result;

	}


	function date_compare($a, $b){

		$d1 = strtotime($a['StartDate']);
		$d2 = strtotime($b['StartDate']);

		if($d1 < $d2){
			return -1;
		} elseif ($d1 > $d2){
			return 1;

		} else {
			return strcmp($a['StartTime'], $b['StartTime']);
		}
	}

	function nav_id_sort($a, $b){
		return $a['NavCategoryLinkID'] - $b['NavCategoryLinkID'];
	}

	function date_compare_descend($b, $a){

		$d1 = strtotime($a['StartDate']);
		$d2 = strtotime($b['StartDate']);

		if($d1 < $d2){
			return -1;
		} elseif ($d1 > $d2){
			return 1;

		} else {
			return strcmp($a['StartTime'], $b['StartTime']);
		}
	}

	function toLink($string){

		// replace "&" with "and"
		$link = str_replace("&", "and", $string);
		// strip all but forward slashes, newlines, letters and numbers
		// make it all lowercase
		$link = strtolower ( preg_replace(	'/[^A-Za-z0-9\n\/ \-]/', '', $link));
		// replace spaces, new lines and forward slashes with dashes
		$link = str_replace(array(" ", "\n", "/"), "-", $link);
		// sometimes it makes a double slash so replace those with single slash
		$link = str_replace("--", "-", $link);
		// check if last character is a dash, if so, trim it off
		if(substr($link, -1, 1)=="-"){
			$link = substr($link, 0, -1);
		}
		return $link;
	}

	function linkToString($link){
		// replace spaces, new lines and forward slashes with dashes
		$link = str_replace(array("-", "\n", "/"), " ", $link);
		return $link;
	}

	/**
	*Builds an artist's name 
	*@param a result from querying the ARTIST table
	*@return String artist name
	**/
	function buildArtistName($artistsQuery){

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
