<?php
	class SpreadSheet {
		
		private $tables;
		private $TablesAndAttributes;
		private $PKattributes;
      	private $tableNames;
      	private $schema;

      	// database object
      	private $spreadSheetDatabase;

      	// database object
      	private $trueDatabase;

		public function __construct() {

			// get the table names, attributes, primary key attributes from the database
			$this->TablesAndAttributes = QueryTablesAndAttributes();
         	$this->tableNames = $this->makeTableNamesArray($this->TablesAndAttributes);
         	$this->PKattributes = $this->makePKattributesArray($this->TablesAndAttributes);
         	$this->schema = $this->dbSchema($this->TablesAndAttributes);

         	// read the spread sheet file
         	$this->createTempFile();

         	// create a database object from the spread sheet
         	$this->spreadSheetDatabase = $this->parseFile();

         	//add tuple objects to the database, selectively
			$this->add($this->spreadSheetDatabase);

			$this->addToReferenceTables($this->spreadSheetDatabase);
         	// debug display
         	//$this->spreadSheetDatabase->display();

		}

		// opens the file and stores it as a temp file on the server
		private function createTempFile(){

			try {
		    
			    // Undefined | Multiple Files | $_FILES Corruption Attack
			    // If this request falls under any of them, treat it invalid.
			    if (!isset($_FILES['file']['error']) ||
			        is_array($_FILES['file']['error'])) {
			        throw new RuntimeException('Invalid parameters.');
			    }

			    // Check $_FILES['upfile']['error'] value.
			    switch ($_FILES['file']['error']) {
			        case UPLOAD_ERR_OK:
			            break;
			        case UPLOAD_ERR_NO_FILE:
			            throw new RuntimeException('No file sent.');
			        case UPLOAD_ERR_INI_SIZE:
			        case UPLOAD_ERR_FORM_SIZE:
			            throw new RuntimeException('Exceeded filesize limit.');
			        default:
			            throw new RuntimeException('Unknown errors.');
			    }

			    // You should also check filesize here. 
			    if ($_FILES['file']['size'] > 1000000) {
			        throw new RuntimeException('Exceeded filesize limit.');
			    }

			    // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
			    // Check MIME Type by yourself.
			    $finfo = new finfo(FILEINFO_MIME_TYPE);
			    echo $finfo->file($_FILES['file']['tmp_name'])."<br><br>";

			    if (false === $ext = array_search(
			        $finfo->file($_FILES['file']['tmp_name']),
			        array(
			            'csv' => 'text/plain',
			        ),
			        true
			    )) {
			        throw new RuntimeException('Invalid file format.');
			    }

			} catch (RuntimeException $e) {

		    	echo $e->getMessage();
			}
		}


		// returns the spread sheet array
		private function parseFile() {	

			$database;

			$lastRowWasBlank = TRUE;
			$isAttributesRow = FALSE;
			$isBlankRow = FALSE;
			$attributes;
			$tupleObjects;
			$tupleObjectCount=0;
			$spreadSheetTuples;
			

			$countTableNames = count($this->tableNames);

			if (($handle = fopen($_FILES['file']['tmp_name'], 'r')) !== FALSE) {
				//begin looping through spreadsheet
    			while (($data = fgetcsv($handle, 1000000, ',')) !== FALSE) {

    				// loop through the row to see if its blank
        			$num = count($data);
        			$nullCount = 0;
			        for ($i=0; $i < $num; $i++) {
						 if($data[$i] == NULL) {
			           		$nullCount ++;
			           	}
			       	}

			       	// if entire row is null, ignore the row, set lastRowWasBlank as true for next pass
					if($num==$nullCount){
			        	$lastRowWasBlank = TRUE;
			        }else{
				       	// if last row was blank, next row with text is the name of the table.
				       	// name of the table may be slightly different than actual table name,
				       	// so this finds a match to the db name. It uses the array built from
				       	// the "QueryTablesAndAttributes" function in conjunction with the 
				       	// makeTableNamesArray function. 
	    				if($lastRowWasBlank==TRUE){

	    					$matchedTableName ='';
	    					$patternMatchVal = 0;
	    					$patternMatchTemp = 99;

	    					for($i=0; $i<$countTableNames; $i++){
	    						// use the levenshtein algorithm to return the value corresponding
	    						// to the number of characters needed to be changed to match the target
	     						$patternMatchVal = levenshtein($this->tableNames[$i], $data[0]);

	    						if($patternMatchVal < $patternMatchTemp){
	    							$matchedTableName = $this->tableNames[$i];
	    							$patternMatchTemp = $patternMatchVal;
	    						}
	    					}
	    					$lastRowWasBlank = FALSE;
	    					// since the next row in the while loop is the attributes row, set below to TRUE
	    					$isAttributesRow = TRUE;
	    				}

	    				// if the row being looked at is the attributes, fill the attributes array
	    				else if ($isAttributesRow == TRUE) {

	    					$tupleCount=0;
	    					unset($attributes);
	    					// loops through the row
	    					for ($i=0; $i < $num; $i++) {
	    						if($data[$i] != NULL ){
	    							$attributes[$i] = $data[$i];
	   							}
	    					}

	    					$isAttributesRow = FALSE;
				       	
				        } else {


				        	
				        	// loop through the tuple row to build the spreadSheetTuple
						    for ($i=0; $i < count($attributes); $i++) {

						    	//fill spreadSheetTuple array with attribute => data key pair
					        	$spreadSheetTuples[$matchedTableName][$attributes[$i]] = $data[$i];

					       	}

					       	// check if the table has a link column using schema array
					       	if(array_key_exists( 'Link', $this->schema[$matchedTableName])){

					       		// strip all but forward slashes, newlines, letters and numbers
					       		// make it all lowercase
					       		$link = strtolower ( preg_replace(	'/[^A-Za-z0-9\n\/ \-]/', 
					       											'', 
					       											$spreadSheetTuples[$matchedTableName]['Title'] 
					       										)
					       							);

					       		
					       		// replace spaces, new lines and forward slashes with dashes
					       		$link = str_replace(array(" ", "\n", "/"), "-", $link);
					       		// sometimes it makes a double slash so replace those with single slash
					       		$link = str_replace("--", "-", $link);
					       		// check if last character is a dash, if so, trim it off
					       		if(substr($link, -1, 1)=="-"){
					       			$link = substr($link, 0, -1);
					       		}

					       		//echo $link. "<br>";

					       		//add the key=>value to the tuple
					       		$spreadSheetTuples[$matchedTableName]['Link'] = $link;
					       	}

					       	$tuple = new Tuple(
				        						$matchedTableName, 
					       						$spreadSheetTuples[$matchedTableName], 
					       						$this->PKattributes[$matchedTableName]
				        					);

					       	if(empty($database)){
				        		$database = new Database($table = new Table($matchedTableName, $tuple));
				        	}

				        	else if(!$database->getTableByTableName($matchedTableName)){
				        		$database->addTable($table = new Table($matchedTableName, $tuple));
				        	}

				        	else {
								$database->getTableByTableName($matchedTableName)->addTuple($tuple);

					       	}
							// counts the number of tuples in spread sheet
							$tupleObjectCount++;
						}
					}

			    }	
    			fclose($handle);
			}


		return $database;
		}

      	// accepts the schema array returned by QueryTablesAndAttributes() and 
      	// returns an array of just the table names
      	private function makeTableNamesArray($array){

      		$tableNames;
       	  	$count = 0;

      	
	       	foreach($array as $row){
	            $searchElement = $row['table_name'];
	            if($count !=0 && $searchElement != $tableNames[$count-1] ){
	               $tableNames[$count]=$searchElement;
	               $count++;
	            }
	            else if ($count == 0){
	               $tableNames[0] = $array[0]['table_name'];
	               $count++;
	            }

	        }

         	return $tableNames;
      	}

      	private function dbSchema($array){
      		
       	  	$count = 0;
       	  	$schema;
      	
	       	foreach($array as $row){
	            $searchElement = $row['table_name'];
	            if($count !=0 && $searchElement != $tableNames[$count-1] ){
	               $schema[$array[$count]['table_name']][$array[$count]['column_name']]='';
	               $count++;
	            }
	            else if ($count == 0){
	               $schema[$array[0]['table_name']][$array[0]['column_name']]='';
	               $count++;
	            }
	        }
         	return $schema;

      	}

      	// accepts the schema array returned by QueryTablesAndAttributes() and 
      	// returns an array of table name => primary key pairs.
      	private function makePKAttributesArray($array){

      		$PKAttributes;
      		$count = 0;
      		$referenceTableKeys = $this->tableNames;
      		$refTableKeyCount = count($referenceTableKeys);


      		foreach($array as $row){
      			$searchElement = $row['table_name'];
	            if($count !=0 && $searchElement != $tableNames[$count-1] ){
	            	for($i=0;$i<$refTableKeyCount;$i++){
	            		if($row['table_name'] == $referenceTableKeys[$i] ){
	            			$PKAttributes[$count]=array($row['table_name'] => $row['column_name']);
	            		}
	            	}
	               	$tableNames[$count]=$searchElement;
	               	$count++;
	            }
	            else if ($count == 0){
	            	for($i=0;$i<$refTableKeyCount;$i++){
	            		if($row['table_name'] == $referenceTableKeys[$i] ){
	            			$PKAttributes[$count]=array($row['table_name'] => $row['column_name']);
	            		}
	            	}
	               	$tableNames[0] = $array[0]['table_name'];
	               	$count++;
	            }
	        }

	        //removes indexed keys and returns a clean array
	        $PKtemp;
      		foreach ($PKAttributes as $array){
      			foreach ($array as$tableName=>$PK){
      				$PKtemp[$tableName]=$PK;
      			}
      		}

      		$PKAttributes = $PKtemp;
	        return $PKAttributes;
      	}
     	
      	private function add($database) {

      		// loop through tuple objects
      		foreach($database->getTables() as $table){

	      		// insertDB() and exists() take a filter array which is filled with strings representing
	      		// attributes in the table that can be diferent (or are not to be compared)
	      		$existFilter = array('ExhibitionReferenceNo','EventReferenceNo','ArtistReferenceNo', 'ReceptionReferenceNo', 'BodyContent');
	      		$insertFilter= array('ExhibitionReferenceNo','EventReferenceNo','ArtistReferenceNo', 'ReceptionReferenceNo', 'GalleryID', 'EmployeeID', 'DepartmentID', 'CategoryID');
	      		$artworkFilter = array('ArtworkReferenceNo');
	      		$artworkExistFilter = array('ArtworkReferenceNo', 'Title');
	      		$eventTypeFilter = array('EventTypeID');
	      		$eventExistFilter = array('StartDate', 'EndDate', 'StartTime', 'EndTime', 'Canceled(bool)', 'Description','RegistrationFull(bool)');
	      		$eventInsertFilter = array('StartDate', 'EndDate', 'StartTime', 'EndTime', 'Canceled(bool)', 'RegistrationFull(bool)');

      			if($table->getTableName()=='EVENT'){
      				$existFilter = array_merge($existFilter, $eventExistFilter);
      				$insertFilter = array_merge($insertFilter, $eventInsertFilter);
      			}

      			if($table->getTableName()=='ARTWORK'){
      				$existFilter =  array_merge($existFilter, $artworkExistFilter);
      				$insertFilter = array_merge($insertFilter, $artworkFilter);
      			}

      			if($table->getTableName() == 'EVENT_TYPE'){
      				$insertFilter = array_merge($insertFilter, $eventTypeFilter);

      			}

      			foreach ($table->getTuples() as $tuple){
	      			$id=$this->exists($tuple->getTableName(), $tuple->getTuple(), $existFilter);

	      			if(!$id){
	      				$id = $this->insertDb($tuple->getTableName(), $tuple->getTuple(), $insertFilter, $isrefTable=FALSE);
	      				//echo "<h3>No Match found</h3>";
	      			}
	    			elseif ($id){
	      				echo "<br>exists, heres the id: ". $id." <br>";
	      			}

	      			$tuple->setPK($id);
	  			}

      		}


      	}

      	private function addToReferenceTables($database){

      		// this array defines the relationships between the spread sheet's reference columns and tables
      		// the major keys are tables that are referenced by the tables represented in the minor keys
      		// the minor keys hold arrays that are the reference names in the spread sheet array.
      		$dbReferenceTables = array( 'ARTWORK'	=>	array('ARTIST_ARTWORKS'=>array('ArtistReferenceNo'), 'EXHIBITION_ARTWORKS'=>array('ExhibitionReferenceNo')),
      									'VIDEO'		=>	array('ARTIST_VIDEOS'=>array('ArtistReferenceNo'), 'EVENT_VIDEOS'=>array('EventReferenceNo'), 'EXHIBITION_VIDEOS'=>array('ExhibitionReferenceNo')),
      									'ARTIST'	=>	array('EXHIBITION_ARTISTS'=>array('ExhibitionReferenceNo')),
      									'SPONSOR'	=> 	array('EVENT_SPONSORS'=>array('EventReferenceNo'), 'EXHIBITION_SPONSORS'=>array('ExhibitionReferenceNo')),
      									'EVENT'		=>	array('EVENT_ARTISTS'=>array('ArtistReferenceNo'), 'EVENT_DATE_TIMES'=>array('StartDate','EndDate','StartTime','EndTime'), 'EXHIBITION_EVENTS'=>array('ExhibitionReferenceNo')),
      									'CURATOR'	=> 	array('EXHIBITION_CURATORS'=>array('ExhibitionReferenceNo')),
      									'RECEPTION' =>	array('EXHIBITION_RECEPTIONS'=>array('ExhibitionReferenceNo'))
      								);

      		// use this array to swap out spreadsheet column names with actual db attribute names
      		$refIDAttributes = array(	'ArtistReferenceNo' => 'ArtistID',
      									'EventReferenceNo' => 'EventID',
      									'ExhibitionReferenceNo' => 'ExhibitionID');
      		
      		
      		// loop through the above db reference tables array
      		foreach($dbReferenceTables as $reference => $array){

      			// loop through the spread sheet tables
      			foreach($database->getTables() as $table){
      				
      				//if in the spread sheet you find a table name that matches that in the db reference tables array
      				if($table->getTableName()==$reference){

      					echo "found match: " .$reference. "<br>";

      					// loop through each array to get the db table to write to
      					foreach($array as $refTableName=>$refTable){

      						// loop through the tuples in the matching table
	      					foreach($table->getTuples() as $tuple){

	      						
	      						// Branch here for EVENT_DATE_TIMES
	      						if($refTableName == 'EVENT_DATE_TIMES'){

									// loop through the attributes of each tuple
		      						foreach($tuple->getTuple() as $attribute=>$data){

		      							$tupleToDB['EventID']=$tuple->getPK();

		      							//loop through each db reference tables array array to write to in case there are multiple columns of reference
		      							foreach($refTable as $refAttribute){

		      								// see if the attribute in the spreadsheet tuple matches one of the tuples in the tables we want to write to
		      								if($attribute==$refAttribute){

		      									// if so create tuple to be passed to insert
		      									$tupleToDB[$refAttribute]= $data;

		      								}
		      							}
							      	}
		      						// write to the db

							      	$id=$this->insertDb($refTableName, $tupleToDB, "", $isRefTable=TRUE);

							    // IF NOT EVENT_DATE_TIMES
	      						} else {
	      							// loop through the attributes of each tuple
	      							foreach($tuple->getTuple() as $attribute=>$data){

			      						//loop through each db reference tables array array to write to in case there are multiple columns of reference
			      						foreach($refTable as $refAttribute){

			      							// see if the attribute in the spreadsheet tuple matches one of the tuples in the tables we want to write to
			      							if($attribute==$refAttribute && $data!=""){
				      							
					      						// if so create a tuple to be passed to insert()
						      					$DBtuple = array(	$refIDAttributes[$refAttribute]=>$data,
						      									$tuple->getPKAttribute()=>$tuple->getPK()
						      								);
						      					// write to the db

						      					$id=$this->insertDb($refTableName, $DBtuple, $tuple->getPKAttribute(), $isRefTable=TRUE);
					      						//echo "<h3> inserted, heres id: " .$id. "</h3>";

					      					}

					      					else {
					      						//echo "<p>no match found</p>";
					      					}
					      				}
					      			}
				      			}
		      				}
		      			}
      				}

      			}
      		}
      	}

      	// query the db to find a similarity, if no return FALSE, if yes return the primary key
      	// $filter is the table attributes that are not checked for similarity.
      	private function exists($tableName, $tuple, $filter){

      		$result = FALSE;
      		$where;
      		$filterCount = 0;

      		// check if there is a filter
      		if(!empty($filter)){
      			$filterCount=count($filter);
      		}

      		//build the WHERE clause
      		// loop through tuple 
      		foreach($tuple as $attribute=>$data){

      			// reset the flag
      			$isFilterAttribute = FALSE;

      			if($filterCount != 0){
	      			//loop tuple element through filters array
	      			// if tuple key a.k.a. attribute is a filter,  set the flag.
	      			for($i=0;$i<$filterCount; $i++){
	      				if($filter[$i]==$attribute){
	      					$isFilterAttribute = TRUE;
	      				}
	      			}
      			}

      			// if flag is not set, fix-up the element and
      			// add it to $where string
      			if($isFilterAttribute!=TRUE){

      				if($data==NULL){
      					$where .= $attribute.' IS NULL AND ';

      				}else{
      					$where .= $attribute. '= \'' .$data. '\' AND ';
      				}
	      			
      			}
      		}

      		// trim the last AND 
      		$where = substr($where, 0, -4);
      		
      		// build the query
	      	$query ='	SELECT *
	      				FROM '  .$tableName. '
	      				WHERE ' .$where;

	      	// do the query
	      	$result = get_table($query);

	      	// check result and return
			if(empty($result)){
				$result = FALSE;
			}else{
				// reset() gets the first element of the array which should be the primary key
				$result = reset($result[0]);
			}
			return $result;
      	}

      	// it is possible to call insert into two types of tables. Tables representing Elements
      	// and tables that represent relationships. If it's the latter $isRefTable should be TRUE.
      	private function insertDb($tableName, $tuple, $filter, $isRefTable){

			if(!empty($tuple)){

				$valueString;
				$attributeString;

				$filterCount=count($filter);

	      		//build INSERT and WHERE clause
	      		// loop through tuple 
	      		foreach($tuple as $attribute=>$data){

	      			// reset the flag
	      			$isFilterAttribute = FALSE;

	      			//loop tuple element through filters array
	      			// if tuple key a.k.a. attribute is a filter,  set the flag.
	      			for($i=0;$i<$filterCount; $i++){
	      				if($filter[$i]==$attribute){
	      					$isFilterAttribute = TRUE;
	      				}
	      			}

	      			// if flag is not set, fix-up the element and
	      			// add it to $where string
	      			if($isFilterAttribute!=TRUE){

	      				$attributeString.= $attribute. ', ';

						if($data != NULL ){
				            $valueString .= '\''.$data. '\', ';
				        }
				        else if($data == NULL) {
				        	$valueString .= 'NULL, ';
				        }
	      			}
	      		}

	      		// trim the last ', ' 
      			$valueString = substr($valueString, 0, -2);
      			$attributeString = substr($attributeString, 0, -2);


      			if(!$isRefTable){

		      		$query = 'INSERT INTO ' .$tableName. ' ( '.$this->PKattributes[$tableName]. ', ' .$attributeString. ')
		      				  VALUES ( NULL, '.$valueString.' )';

				}else {

					$query = 'INSERT INTO ' .$tableName. ' ( ' .$attributeString. ')
		      				  VALUES ( '.$valueString.' )';

		      				  echo $query. "<br><br><br>";

				}
				// insert() returns the PK of the last inserted object.
				$id = insert($query);

				return $id;
			}
		}
	}
?>