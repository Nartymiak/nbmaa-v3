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

  function queryStaticPage($url){

    $conn = pdo_connect();

    //Prepare the statement.
    $statement = $conn->prepare(" SELECT  S.StaticPageID, S.Title, S.Body, S.ImgFilePath, S.ImgCaption, S.Link
                                  FROM    STATIC_PAGE S
                                  WHERE   S.Link = :link");
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
  function queryReferencePortion($table, $attribute, $value, $order, $start, $limit){
    
    $conn = pdo_connect();

    if($order != null){

      $sql = 'SELECT  * 
              FROM      '.$table. ' 
              WHERE     '.$attribute. ' = :value
              ORDER BY  '.$order. '
              LIMIT     :start, :limit';
    
    } else {

      $sql = 'SELECT  * 
              FROM      '.$table. ' 
              WHERE     '.$attribute. ' = :value
              LIMIT     :start, :limit';

    }

    $statement = $conn->prepare($sql);

    $statement->bindValue(":value", $value, PDO::PARAM_STR);
    $statement->bindValue(":start", $start, PDO::PARAM_INT);
    $statement->bindValue(":limit", $limit, PDO::PARAM_INT);

    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.
    
    $conn = null;
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
    if($_POST['days']==null && $_POST['keyword'] == null){
      
      $result = queryCalendarPageEventsByMonth();
    
    // if user selected days and not keywords
    } else if($_POST['days']!=null && $_POST['keyword'] == null) {

      $result = queryCalendarPageEventsByDays();
      
    // if user selected keywords and not days
    } else if($_POST['days']==null && $_POST['keyword'] != null){

      $result = queryCalendarPageEventsByKeywords();

    //if user selects keywords and days
    } else if($_POST['days']!=null && $_POST['keyword'] != null) {

      $result = queryCalendarPageEventsByKeywords();

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

    $sql = 'SELECT * FROM EXHIBITION WHERE :today BETWEEN StartDate AND EndDate ORDER BY Rank';

    $statement = $conn->prepare($sql);

    $statement->bindValue(":today", $today, PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.
    
    return $result;
  }

  function queryKeywords(){
      $query = 'SELECT KeywordID, Word FROM KEYWORD ORDER BY DisplayOrder';
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

  function queryEventTypesByKeywords(){

    $returnResult = array();
    
    $conn = db_connect();

    foreach ($_POST['keyword'] as $keyword){
      
      $query = 'SELECT EventTypeID FROM KEYWORD_EVENT_TYPE WHERE KeywordID = ' .$keyword. '';
      
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
  function queryCalendarPageEventsByKeywords() {

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

    $eventTypes = queryEventTypesByKeywords();

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

  function adminQuery($type) {

    $result = array();

    $rowLimit = 20;
    $rowOffset = 20;

    // create the connection
    $conn = pdo_connect();

    
    switch ($type) {

      case "event":
        $sql = "SELECT    E.EventID as EventID, E.Link, E.Title as EventTitle, ET.Title as TypeTitle, MAX(StartDate)
                FROM      EVENT_DATE_TIMES ED, EVENT E, EVENT_TYPE ET
                WHERE     E.EventTypeID = ET.EventTypeID AND ED.EventID = E.EventID
                GROUP BY  E.Title
                ORDER BY  StartDate
                ";
      break;

      case "static":
        $sql = "SELECT    StaticPageID, Title, CreatedOn, ChangedOn, Link
                FROM      STATIC_PAGE
                ";
      break;
    }

    // prepare the statement object
    $statement = $conn->prepare($sql);

    $statement->execute();

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  function queryNav(){
      $query = 'SELECT Title FROM NAV';
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


  function querySubNav($link){

    $fromFooter = FALSE;

    // create the connection
    $conn = pdo_connect();

    $sql = 'SELECT    Title, Link, NavCategoryLinkID
              FROM    NAV_CATEGORY_LINK
              WHERE   Link = :link';

    // prepare the statement object
    $statement = $conn->prepare($sql);

    $statement->bindValue(":link", $link, PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $navCatResult = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.

    if(sizeof($navCatResult) == 0){

      $sql = 'SELECT  Title, Link, NavCategoryLinkID
              FROM    SUBNAV_LINK
              WHERE   Link = :link';

      // prepare the statement object
      $statement = $conn->prepare($sql);

      $statement->bindValue(":link", $link, PDO::PARAM_STR);

      $statement->execute();

      //Fetch all of the results.
      $subNavResult = $statement->fetchAll(PDO::FETCH_ASSOC);
      //$result now contains the entire resultset from the query.
    
      $sql = '  SELECT  Title, Link
                FROM    SUBNAV_LINK
                WHERE   NavCategoryLinkID =' .$subNavResult[0]['NavCategoryLinkID'];

      // *HACK* check size because subNavResult contains nothing if a link with no subNav links from the footer is using this method
      if(sizeof($subNavResult) == 0){ $fromFooter = TRUE; }

    } else {

      $sql = '  SELECT  Title, Link
                FROM    SUBNAV_LINK
                WHERE   NavCategoryLinkID =' .$navCatResult[0]['NavCategoryLinkID'];
    }

    // prepare the statement object
    $statement = $conn->prepare($sql);

    if($fromFooter == FALSE){   $statement->execute(); }

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.

    
    return $result;
  }




?>