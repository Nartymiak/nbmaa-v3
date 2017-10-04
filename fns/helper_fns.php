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