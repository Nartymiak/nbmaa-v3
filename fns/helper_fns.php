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

	function buildDateRange(){
		
		// this gets the first and last day of the current month
		$startDate = date("Y-m-d");
		$endDate = date("Y-m-t");

		$dateArray = array($startDate, $endDate);

		return $dateArray;
	}

	function shortenText($string){

		$result;

		$text = explode(" ", $string, 25);

		if($text){

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
