<?php

  function get_table($query) {

    $conn = db_connect();
       $result = @$conn->query($query);
     if (!$result) {
       return false;
     }
     $num_cats = @$result->num_rows;
     if ($num_cats == 0) {
        return false;
     }
     $result = db_result_to_array($result);
     return $result;
   }

  // returns the schema of the database
  function QueryTablesAndAttributes() {

    $query = '  select table_name,column_name
                from information_schema.columns
                where table_schema = \'nbmaa_nbmaa3\' 
                order by table_name,ordinal_position';
           
    $conn = db_connect();

    $result = $conn->query($query);
    $num_items = $result->num_rows;

    if (!$result) {
      return false;
    }
           
    if ($num_items == 0) {
      return false;
    }

    else {
      $result = db_result_to_array($result);
      return $result;
    }
  }

  function insert($query) {
    
    $conn = db_connect();
    $result = @$conn->query($query);

    if($result){
      $result = $conn->insert_id;
    }     
    return $result;
  }

  function queryExhibitionPage($url){

    $conn = pdo_connect();

    //Prepare the statement.
    $statement = $conn->prepare("SELECT * FROM EXHIBITION WHERE Link = :link");
    //Bind the Value, binding parameters should be used when the same query is run repeatedly with different parameters.
    $statement->bindValue(":link", $url, PDO::PARAM_STR);
    //Execute the query
    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.

    //$result contain
    $result = $result[0];

    return $result;
  
  }


  function queryEventPage($url){

    $conn = pdo_connect();

    //Prepare the statement.
    $statement = $conn->prepare(" SELECT  E.EventID, E.Title, E.Description, E.Blurb, E.AdmissionCharge, E.RegistrationBeginDate, 
                                          E. RegistrationEndDate, E.eventTypeID, E.Canceled, E.RegistrationFull, E.ImgFilePath, 
                                          E.ImgCaption, E.Link
                                  FROM    EVENT E
                                  WHERE   E.Link = :link");
    //Bind the Value, binding parameters should be used when the same query is run repeatedly with different parameters.
    $statement->bindValue(":link", $url, PDO::PARAM_STR);
    //Execute the query
    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.

    //$result contain
    $result = $result[0];

    return $result;
  
  }

  // VIP! Do not use when accepting parameters from a user <form> or url
  // returns the tuple from specified table and value
  function queryReference($table, $attribute, $value){
    
    $conn = pdo_connect();

    $sql = 'SELECT * FROM '.$table. ' WHERE '.$attribute. ' = :value';

    $statement = $conn->prepare($sql);

    $statement->bindValue(":value", $value, PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.
    
    return $result;
  }

  // VIP! Do not use when accepting parameters from a user <form> or url
  // returns the tuple from specified table and value
  function queryReferenceWithLimit($table, $attribute, $value, $limit){
    
    $conn = pdo_connect();

    $sql = 'SELECT  * 
            FROM    '.$table. ' 
            WHERE '.$attribute. ' = :value
            LIMIT :limit';

    $statement = $conn->prepare($sql);

    $statement->bindValue(":value", $value, PDO::PARAM_STR);
    $statement->bindValue(":limit", $limit, PDO::PARAM_INT);

    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.
    
    return $result;
  }

  /** 
  *Queries the database according to the user's input from the calendar
  *@param None, but function uses global $_POST value ( if $_POST is null, runs a default query)
  *@return associative query result
  **/
  function queryCalendarPageEvents(){

    $result = array();

    //if user selected a month or nothing only
    if($_POST['days']==null && $_POST['filter'] == null){
      
      $result = queryCalendarPageEventsByMonth();
    
    // if user selected days and not filters
    } else if($_POST['days']!=null && $_POST['filter'] == null) {

      $result = queryCalendarPageEventsByDays();
      
    // if user selected filters and not days
    } else if($_POST['days']==null && $_POST['filter'] != null){

      $result = queryCalendarPageEventsByFilters();

    //if user selects filters and days
    } else if($_POST['days']!=null && $_POST['filter'] != null) {

      $result = queryCalendarPageEventsByFilters();

      // now remove elements that were not selected by day by the user
      foreach($result as $key=>$tuple){

        $flag=false;

        foreach ($_POST['days'] as $day) {

          if($tuple['StartDate'] == $day) {
            $flag=true;
          }
        }
        
        if($flag==false) {

        //delete this particular object from the array
        unset($result[$key]);
        }
      }
    }
    return $result;
  }

  function queryCalendarPageExhibitions(){

    // today's date
    $today = date("Y-m-d");

    $conn = pdo_connect();

    $sql = 'SELECT * FROM EXHIBITION WHERE :today BETWEEN StartDate AND EndDate ORDER BY StartDate';

    $statement = $conn->prepare($sql);

    $statement->bindValue(":today", $today, PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.
    
    return $result;
  }

  function queryFilters(){
      $query = 'SELECT CategoryID, Title FROM CATEGORY ORDER BY DisplayOrder';
      $conn = db_connect();

      $result = @$conn->query($query);
      if (!$result) {
        return false;
      }
      
      $num_cats = @$result->num_rows;
      
      if ($num_cats == 0) {
        return false;
      }
      
      $result = db_result_to_array($result);
      
      return $result;    
  }

  function queryEventTypesByFilters(){

    $returnResult = array();
    
    $conn = db_connect();

    foreach ($_POST['filter'] as $filter){
      
      $query = 'SELECT EventTypeID FROM CATEGORY_EVENT_TYPE WHERE CategoryID = ' .$filter. '';
      
      $result = @$conn->query($query);
      if (!$result) {
        return false;
      }
      
      $num_cats = @$result->num_rows;
      
      if ($num_cats == 0) {
        return false;
      }
      
      foreach(db_result_to_array($result) as $tuple)
      array_push($returnResult, $tuple);
       
    }

    return $returnResult;

  }

  // helper function for queryCalendarPageEvents()
  function queryCalendarPageEventsByMonth() {

    $result = array();

    // create the connection
    $conn = pdo_connect();

    if($_POST['month'] != null) {

      List($m,$y) = explode("-",$_POST['month']);

      $dateRange[0] = $y. "-" .$m. "-01";
      $dateRange[1] = date("y-m-t", strtotime($y. "-" .$m. "-01"));

    } else {

      $dateRange = buildDateRange();
    }

    // write the generic statement
    $sql = '  SELECT  StartDate, EndDate, StartTime, EndTime, E.Title as EventTitle, E.Description, E.ImgFilePath, ImgCaption, Link, ET.Title as TypeTitle
              FROM    EVENT_DATE_TIMES ED, EVENT E, EVENT_TYPE ET
              WHERE   E.EventTypeID = ET.EventTypeID AND ED.EventID = E.EventID AND ED.StartDate BETWEEN :startDate AND :endDate ORDER BY ED.StartDate';        
              
    // prepare the statement object
    $statement = $conn->prepare($sql);

    $statement->bindValue(":startDate", $dateRange[0], PDO::PARAM_STR);
    $statement->bindValue(":endDate", $dateRange[1], PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $tuples = $statement->fetchAll(PDO::FETCH_ASSOC);

    //pre-organize result array by breaking out elements and push to result
    foreach ($tuples as $tuple) { array_push($result, $tuple); } 
    
    // sort result by date
    usort($result, 'date_compare');
    
    return $result;
  }

  // helper function for queryCalendarPageEvents()
  function queryCalendarPageEventsByDays() {

    $result = array();

    // create the connection
    $conn = pdo_connect();

    // write the generic statement
    $sql = 'SELECT  StartDate, EndDate, StartTime, EndTime, E.Title as EventTitle, E.Description, E.ImgFilePath, ImgCaption, Link, ET.Title as TypeTitle
            FROM    EVENT_DATE_TIMES ED, EVENT E, EVENT_TYPE ET
            WHERE   E.EventTypeID = ET.EventTypeID AND ED.EventID = E.EventID AND ED.StartDate = :startDate ORDER BY ED.StartDate';

        
    // for each day selected by the user, bind $_POST value to the statement, excecute the statement and build the result
    foreach($_POST['days'] as $day){

      // prepare the statement object
      $statement = $conn->prepare($sql);

      $statement->bindValue(":startDate", $day, PDO::PARAM_STR);

      $statement->execute();

      //Fetch all of the results.
      $tuples = $statement->fetchAll(PDO::FETCH_ASSOC);

      //pre-organize result array by breaking out elements and push to result
      foreach ($tuples as $tuple) { array_push($result, $tuple); }
    }
  
    return $result;
  }

  // helper function for queryCalendarPageEvents()
  function queryCalendarPageEventsByFilters() {

    $result = array();
    
    // create the connection
    $conn = pdo_connect();

    if($_POST['month'] != null) {
      List($m,$y) = explode("-",$_POST['month']);
        
      $dateRange[0] = $y. "-" .$m. "-01";

      
      $dateRange[1] = date("y-m-t", strtotime($y. "-" .$m. "-01"));

    } else {

      $dateRange = buildDateRange();

    }

    $eventTypes = queryEventTypesByFilters();

    if( $eventTypes != null){

      foreach ($eventTypes as $eventType) {

        if( $eventType == 'Exhibition') {

          // write the generic statement
          //$sql = '  SELECT StartDate, EndDate, StartTime, EndTime, E.Title as EventTitle, E.Description, E.ImgFilePath, ImgCaption, Link, ET.Title as TypeTitle
          //          FROM EVENT_DATE_TIMES ED, EVENT E, EVENT_TYPE ET
          //          WHERE E.EventTypeID = :eventType AND E.EventTypeID = ET.EventTypeID AND ED.EventID = E.EventID AND ED.StartDate BETWEEN :startDate AND :endDate ORDER BY ED.StartTime';    

        }

        else {

          // write the generic statement
          $sql = '  SELECT StartDate, EndDate, StartTime, EndTime, E.Title as EventTitle, E.Description, E.ImgFilePath, ImgCaption, Link, ET.Title as TypeTitle
                    FROM EVENT_DATE_TIMES ED, EVENT E, EVENT_TYPE ET
                    WHERE E.EventTypeID = :eventType AND E.EventTypeID = ET.EventTypeID AND ED.EventID = E.EventID AND ED.StartDate BETWEEN :startDate AND :endDate ORDER BY ED.StartTime';        
        }    
        
        // prepare the statement object
        $statement = $conn->prepare($sql);

        $statement->bindValue(":eventType", $eventType['EventTypeID'], PDO::PARAM_STR);
        $statement->bindValue(":startDate", $dateRange[0], PDO::PARAM_STR);
        $statement->bindValue(":endDate", $dateRange[1], PDO::PARAM_STR);

        $statement->execute();

        //Fetch all of the results.
        $tuples = $statement->fetchAll(PDO::FETCH_ASSOC);

        //pre-organize result array by breaking out elements and push to result
        foreach ($tuples as $tuple) { array_push($result, $tuple); } 
      }
      // sort result by date
      usort($result, 'date_compare');
    }
        
    return $result;
  }

  function adminQuery() {

    $result = array();

    $rowLimit = 20;
    $rowOffset = 20;

    // create the connection
    $conn = pdo_connect();

    $sql = "SELECT    E.EventID as EventID, E.Link, E.Title as EventTitle, ET.Title as TypeTitle, MAX(StartDate)
            FROM      EVENT_DATE_TIMES ED, EVENT E, EVENT_TYPE ET
            WHERE     E.EventTypeID = ET.EventTypeID AND ED.EventID = E.EventID
            GROUP BY E.Title
            ORDER BY StartDate
            ";

    // prepare the statement object
    $statement = $conn->prepare($sql);


    $statement->execute();

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }


?>