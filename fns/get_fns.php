<?php

  function get_table($query) {

    $conn = db_connect();
       $result = @$conn->query($query);
     if (!$result) {
        mysqli_close($conn);
       return false;
     }
     $num_cats = @$result->num_rows;
     if ($num_cats == 0) {
        mysqli_close($conn);
        return false;
     }
     $result = db_result_to_array($result);
   
    mysqli_close($conn);
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
      mysqli_close($conn);
      return false;
    }
           
    if ($num_items == 0) {
      mysqli_close($conn);
      return false;
    }

    else {
      $result = db_result_to_array($result);
      mysqli_close($conn);
      return $result;
    }
  }

  function insert($query) {
    
    $conn = db_connect();
    $result = @$conn->query($query);

    if($result){
      $result = $conn->insert_id;
    }
    mysqli_close($conn);     
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
    $conn = null;
    return $result;
  
  }


  function queryEventPage($url){

    $conn = pdo_connect();

    //Prepare the statement.
    $statement = $conn->prepare(" SELECT  E.EventID, E.Title, E.Description, E.Blurb, E.AdmissionCharge, E.RegistrationBeginDate, 
                                          E. RegistrationEndDate, E.EventTypeID, E.Canceled, E.RegistrationFull, E.ImgFilePath, 
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
    $conn = null;
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
    $conn = null;
    return $result;

  }

  function queryClassroomPage($url){

    $conn = pdo_connect();
    $type;
    $result = array();

    // if the url is not a keyword id# and is a classroom page link string
    if(!is_numeric($url)){

    //Prepare the statement.
    $statement = $conn->prepare(" SELECT  C.ClassroomPageID, C.Title, C.Body, C.ImgFilePath, C.ImgCaption, C.Link
                                  FROM    CLASSROOM_PAGE C
                                  WHERE   C.Link = :link");
    $type = "main-page";

    //Bind the Value, binding parameters should be used when the same query is run repeatedly with different parameters.
    $statement->bindValue(":link", $url, PDO::PARAM_STR);
    //Execute the query
    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.

    $conn = null;

    $result = $result[0];

    if($result){ array_unshift($result, $type); }

    return $result;


    } else {

    $eventsResult;
    $classroomPageIDAndDescriptionResult;

    // prepare the type for the result
    $type = "events";
    $dateRange = buildThreeMonthDateRange();


    //Prepare the statement.
    $statement = $conn->prepare(" SELECT    StartDate, EndDate, StartTime, EndTime, E.Title as EventTitle, E.Description, E.RegistrationEndDate,
                                            E.ImgFilePath, ImgCaption, E.Link, K.Word as TypeTitle, E.OutsideLink
                                  FROM      EVENT_DATE_TIMES ED, EVENT E, KEYWORD K
                                  WHERE     E.EventTypeID = :link AND E.EventTypeID = K.KeywordID AND 
                                            ED.EventID = E.EventID AND ED.StartDate > :startDate AND E.Canceled IS NOT TRUE
                                  GROUP BY  E.Title ORDER BY ED.StartDate"); 

    $statement->bindValue(":link", $url, PDO::PARAM_STR);
    $statement->bindValue(":startDate", $dateRange[0], PDO::PARAM_STR);
    //Execute the query
    $statement->execute();

    //Fetch all of the results.
    $eventsResult = $statement->fetchAll(PDO::FETCH_ASSOC);

    // find the classroomPageID;
    //Prepare the statement.
    $statement = $conn->prepare(" SELECT  C.ClassroomPageID, K.Description, K.Word
                                  FROM    CLASSROOM_PAGE_KEYWORD C, KEYWORD K
                                  WHERE   C.KeywordID = :link AND K.KeywordID = :link "); 


    //Bind the Value, binding parameters should be used when the same query is run repeatedly with different parameters.
    $statement->bindValue(":link", $url, PDO::PARAM_STR);
    //Execute the query
    $statement->execute();

    //Fetch all of the results.
    $classroomPageIDAndDescriptionResult = $statement->fetchAll(PDO::FETCH_ASSOC);

    $conn = null;

    // prepend the result array with type and classroomPageID
    array_push($result, $type, $classroomPageIDAndDescriptionResult[0], $eventsResult);

    return $result;

    }

  }

  function queryClassRoomKeywords($classroomPageID){

    $keywordIDs = array();
    $result = array();
    $mainPageLink;
    
    $conn = pdo_connect();

    $sql = '  SELECT  KeywordID
              FROM    CLASSROOM_PAGE_KEYWORD
              WHERE   ClassroomPageID = :classroomPageID';

    $statement = $conn->prepare($sql);

    $statement->bindValue(":classroomPageID", $classroomPageID, PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $keywordIDs = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.

    if(empty($keywordIDs)){
      //handle error

    } else {

      $sql = '  SELECT  K.KeywordID, K.Word, CP.Link, CP.Title
                FROM    KEYWORD AS K, CLASSROOM_PAGE AS CP, CLASSROOM_PAGE_KEYWORD AS CPK
                WHERE   K.KeywordID = :keywordID AND CPK.KeywordID = K.KeywordID AND CP.ClassroomPageID = CPK.classroomPageID';

      $statement = $conn->prepare($sql);

      foreach($keywordIDs as $keywordID){

        $statement->bindValue(":keywordID", $keywordID['KeywordID'], PDO::PARAM_STR);

        $statement->execute();

        //Fetch all of the results.
        array_push($result, $statement->fetchAll(PDO::FETCH_ASSOC));
        //$result now contains the entire resultset from the query.

      }

    }

    $conn = null;
    return $result;
  }


  // called only on LobbyPage Class, only for exhibitions
  function queryLobbyPage($url){

    switch($url) {
      
      case "current":
        $sql = 'SELECT * FROM EXHIBITION WHERE :date BETWEEN StartDate AND EndDate ORDER BY Rank';
        break;

      case "upcoming":
        $sql = 'SELECT * FROM EXHIBITION WHERE StartDate > :date AND Publish = 1 ORDER BY Rank';
        break;
      
      case "recently-off-the-wall":
        $sql = 'SELECT * FROM EXHIBITION WHERE EndDate < :date ORDER BY EndDate DESC';
        break;
      
      default:
        $sql = 'SELECT * FROM EXHIBITION WHERE :date BETWEEN StartDate AND EndDate ORDER BY Rank';
    }

    // today's date
    $date = date("Y-m-d");

    $conn = pdo_connect();

    $statement = $conn->prepare($sql);

    $statement->bindValue(":date", $date, PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.
    $conn = null;
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
    $conn = null;
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

  function queryEventDateTimes($eventID){

    $conn = pdo_connect();
    // today's date
    $today = date("Y-m-d");
    // subtract one month, so it displays previous scheduled events as well
    $date = date("Y-m-d", strtotime($today));

    $sql = 'SELECT * FROM EVENT_DATE_TIMES WHERE EventID = :value AND StartDate >= :date ORDER BY StartDate ASC LIMIT 8';

    $statement = $conn->prepare($sql);

    $statement->bindValue(":value", $eventID, PDO::PARAM_STR);

    $statement->bindValue(":date", $date, PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    if($result == null){

      $sql = 'SELECT * FROM EVENT_DATE_TIMES WHERE EventID = :value ORDER BY StartDate DESC LIMIT 1';

      $statement = $conn->prepare($sql);

      $statement->bindValue(":value", $eventID, PDO::PARAM_STR);

      $statement->execute();

      //Fetch all of the results.
      $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    }
    //$result now contains the entire resultset from the query.
    $conn = null;
    return $result;
  }

  /** 
  *Queries the database according to the user's input from the calendar
  *@param None, but function uses global $_POST value ( if $_POST is null, runs a default query)
  *@return associative query result
  **/
  function ajaxQueryCalendarPageEvents($dates){

    if(empty($dates)){

      return queryCalendarPageEvents("Today");
    
    } else {

      $result = array();

      // create the connection
      $conn = pdo_connect();

      // write the generic statement
      $sql = 'SELECT  StartDate, EndDate, StartTime, EndTime, E.Title as EventTitle, E.Description, E.ImgFilePath, E.EventTypeID, E.AdmissionCharge, ImgCaption, E.Link, K.Word as TypeTitle, E.OutsideLink
              FROM    EVENT_DATE_TIMES ED, EVENT E, KEYWORD K
              WHERE   E.EventTypeID = K.KeywordID AND ED.EventID = E.EventID AND ED.StartDate = :startDate AND E.Canceled IS NOT TRUE ORDER BY ED.StartDate';
          
      // for each day selected by the user, bind $day value to the statement, excecute the statement and build the result
      foreach($dates as $day){

        // prepare the statement object
        $statement = $conn->prepare($sql);

        $statement->bindValue(":startDate", $day, PDO::PARAM_STR);

        $statement->execute();

        //Fetch all of the results.
        $tuples = $statement->fetchAll(PDO::FETCH_ASSOC);

        //pre-organize result array by breaking out elements and push to result
        foreach ($tuples as $tuple) { array_push($result, $tuple); }
      }

      // sort result by date
      usort($result, 'date_compare');
      $conn = null;
      return $result;
    }
  }

  /** 
  *Queries the database according to the user's input from the calendar
  *@param a string from the url
  *@return associative query result
  **/
  function queryCalendarPageEvents(){

    $result = array();

    $result = queryCalendarPageEventsToday();

    return $result;
  }

  /** 
  *Queries the database for events and according to today's date
  *@param None
  *@return associative array query result
  **/
  function queryCalendarPageEventsToday(){

    $result = array();

    $dateRange = buildThirtyDayDateRange();

    $conn = pdo_connect();

    // write the generic statement
    $sql = '  SELECT  StartDate, EndDate, StartTime, EndTime, E.Title as EventTitle, E.Description, E.ImgFilePath, E.EventTypeID, ImgCaption, E.Link, K.Word as TypeTitle, E.OutsideLink
              FROM    EVENT_DATE_TIMES ED, EVENT E, KEYWORD K
              WHERE   E.EventTypeID = K.KeywordID AND ED.EventID = E.EventID AND ED.StartDate BETWEEN :startDate AND :endDate AND E.Canceled IS NOT TRUE ORDER BY ED.StartDate';        
              
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
    $conn = null;
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
    $conn = null;
    return $result;
  }

  function queryKeywords(){
      $query = 'SELECT KeywordID, Word, ParentKeywordID FROM KEYWORD';
      $conn = db_connect();

      $result = @$conn->query($query);
      if (!$result) {
        mysqli_close($conn);
        return false;
      }
      
      $num_cats = @$result->num_rows;
      
      if ($num_cats == 0) {
        mysqli_close($conn);
        return false;
      }
      
      $result = db_result_to_array($result);
      mysqli_close($conn);
      return $result;    
  }

  function queryEventTypesByKeywords($keywords){

    $returnResult = array();
    
    $conn = db_connect();

    foreach ($keywords as $keyword){
      
      $query = 'SELECT EventTypeID FROM KEYWORD_EVENT_TYPE WHERE KeywordID = ' .$keyword. '';
      
      $result = @$conn->query($query);
      if (!$result) {
        mysqli_close($conn);
        return false;
      }
      
      $num_cats = @$result->num_rows;
      
      if ($num_cats == 0) {
        mysqli_close($conn);
        return false;
      }
      
      foreach(db_result_to_array($result) as $tuple)
      array_push($returnResult, $tuple);
       
    }
    mysqli_close($conn);
    return $returnResult;

  }

  function queryParentKeyword($parentKeywordID){

    $result;

    $conn = pdo_connect();

    //Prepare the statement.
    $statement = $conn->prepare(" SELECT  KeywordID
                                  FROM    KEYWORD 
                                  WHERE   ParentKeywordID = :parentKeywordID");

    //Bind the Value, binding parameters should be used when the same query is run repeatedly with different parameters.
    $statement->bindValue(":parentKeywordID", $parentKeywordID, PDO::PARAM_STR);
    //Execute the query
    $statement->execute();
    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    if(empty($result)){

      $result[0] = array("KeywordID" => $parentKeywordID);
      $conn = null;
      return $result;

    } else {
      array_push($result, array("KeywordID" => $parentKeywordID));
      $conn = null;
      return $result;
    }

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
    $sql = '  SELECT  StartDate, EndDate, StartTime, EndTime, E.Title as EventTitle, E.Description, E.ImgFilePath, ImgCaption, E.Link, K.Word as TypeTitle, E.OutsideLink
              FROM    EVENT_DATE_TIMES ED, EVENT E, KEYWORD K
              WHERE   E.EventTypeID = K.KeywordID AND ED.EventID = E.EventID AND ED.StartDate BETWEEN :startDate AND :endDate AND E.Canceled IS NOT TRUE ORDER BY ED.StartDate';        
              
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
    $conn = null;    
    return $result;
  }

  // helper function for queryCalendarPageEvents()
  function queryCalendarPageEventsByDays($url) {

    $result = array();

    // create the connection
    $conn = pdo_connect();

    // write the generic statement
    $sql = 'SELECT  StartDate, EndDate, StartTime, EndTime, E.Title as EventTitle, E.Description, E.ImgFilePath, ImgCaption, E.Link, K.Word as TypeTitle, E.OutsideLink
            FROM    EVENT_DATE_TIMES ED, EVENT E, KEYWORD K
            WHERE   E.EventTypeID = K.KeywordID AND ED.EventID = E.EventID AND ED.StartDate = :startDate AND E.Canceled IS NOT TRUE ORDER BY ED.StartDate';

    // prepare the statement object
    $statement = $conn->prepare($sql);

    $statement->bindValue(":startDate", $url, PDO::PARAM_STR);

    $statement->execute();

    //Fetch the result.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $conn = null;
    return $result;
  }

  // helper function for queryCalendarPageEvents()
  function queryCalendarPageEventsByKeyword($url) {

    $tuples = array();

    $result = array();
    
    // create the connection
    $conn = pdo_connect();

    $dateRange = buildThirtyDayDateRange();

    $keywordIDs = queryParentKeyword($url);

    // write the generic statement
    $sql = '  SELECT StartDate, EndDate, StartTime, EndTime, E.Title as EventTitle, E.Description, E.ImgFilePath, ImgCaption, E.Link, K.Word as TypeTitle, E.OutsideLink
              FROM EVENT_DATE_TIMES ED, EVENT E, KEYWORD K
              WHERE E.EventTypeID = :eventType AND E.EventTypeID = K.KeywordID AND ED.EventID = E.EventID AND ED.StartDate BETWEEN :startDate AND :endDate AND E.Canceled IS NOT TRUE ORDER BY ED.StartTime';            
        
    // prepare the statement object
    $statement = $conn->prepare($sql);

    foreach($keywordIDs as $key=>$el){

      $statement->bindValue(":eventType", $el['KeywordID'], PDO::PARAM_STR);
      $statement->bindValue(":startDate", $dateRange[0], PDO::PARAM_STR);
      $statement->bindValue(":endDate", $dateRange[1], PDO::PARAM_STR);

      $statement->execute();

      //Fetch all of the results.
      array_push($tuples, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    //pre-organize result array by breaking out elements and push to result
    foreach ($tuples as $tuple) { 
      if(!empty($tuple[0])){
        array_push($result, $tuple[0]);
      }
    } 
    
    // sort result by date
    usort($result, 'date_compare');
    $conn = null;
    return $result;
  }

  function ajaxQueryClasses($keywordID){

    $conn = pdo_connect();

    $dateRange = buildThreeMonthDateRange();

    // write the generic statement
    $sql = '  SELECT StartDate, EndDate, StartTime, EndTime, E.Title as EventTitle, E.Description, E.ImgFilePath, ImgCaption, E.Link, K.Word as TypeTitle, E.OutsideLink
              FROM EVENT_DATE_TIMES ED, EVENT E, KEYWORD K
              WHERE E.EventTypeID = :eventType AND E.EventTypeID = K.KeywordID AND ED.EventID = E.EventID AND ED.StartDate > :startDate AND E.Canceled IS NOT TRUE GROUP BY E.Title ORDER BY ED.StartDate';            
        
    // prepare the statement object
    $statement = $conn->prepare($sql);

    $statement->bindValue(":eventType", $keywordID, PDO::PARAM_STR);
    $statement->bindValue(":startDate", $dateRange[0], PDO::PARAM_STR);
    // commented out to allow for all events after today's date to be displayed
    //$statement->bindValue(":endDate", $dateRange[1], PDO::PARAM_STR);

    $statement->execute();

    //Fetch all the result
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $conn = null;
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
        $sql = "SELECT    E.EventID as EventID, E.Link, E.Title as EventTitle, K.Word as TypeTitle, MAX(StartDate), CreatedOn
                FROM      EVENT_DATE_TIMES ED, EVENT E, KEYWORD K
                WHERE     E.EventTypeID = K.KeywordID AND ED.EventID = E.EventID
                GROUP BY  E.Title
                ORDER BY  CreatedOn DESC
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
    $conn = null;
    return $result;
  }

  function queryNav(){
      $query = 'SELECT Title FROM NAV';
      $conn = db_connect();

      $result = @$conn->query($query);
      if (!$result) {
        mysqli_close($conn);
        return false;
      }
      
      $num_cats = @$result->num_rows;
      
      if ($num_cats == 0) {
        mysqli_close($conn);
        return false;
      }
      
      $result = db_result_to_array($result);
      mysqli_close($conn);
      return $result;    
  }

  function querySubNav($link){

    $fromFooter = FALSE;

    // create the connection
    $conn = pdo_connect();

    // first, check if the user clicked on a link from the main nav
    $sql = 'SELECT    Title, Link, NavCategoryLinkID
              FROM    NAV_CATEGORY_LINK
              WHERE   Link = :link';

    // prepare the statement object
    $statement = $conn->prepare($sql);

    $statement->bindValue(":link", $link, PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $navCatResult = $statement->fetchAll(PDO::FETCH_ASSOC);

    // second, if no result from link, check if the user clicked on a link from the sub nav
    // *HACK* add "|| $navCatResult[0]['NavCategoryLinkID'] == 31" because art-lab link in main nav is the only link in main nav that is not a parent nav.
    if(sizeof($navCatResult) == 0 || $navCatResult[0]['NavCategoryLinkID'] == 31 ){

      $sql = 'SELECT  Title, Link, NavCategoryLinkID
              FROM    SUBNAV_LINK
              WHERE   Link = :link ORDER BY DisplayOrder';

      // prepare the statement object
      $statement = $conn->prepare($sql);

      $statement->bindValue(":link", $link, PDO::PARAM_STR);

      $statement->execute();

      //Fetch all of the results.
      $subNavResult = $statement->fetchAll(PDO::FETCH_ASSOC);
    
      // if the user clicked on a sub nav link, use the nav category link id to get all the sub nav links to be used
      $sql = '  SELECT  Title, Link
                FROM    SUBNAV_LINK
                WHERE   NavCategoryLinkID =' .$subNavResult[0]['NavCategoryLinkID']. ' ORDER BY DisplayOrder';

      // *HACK* check size because subNavResult contains nothing if a link with no subNav links from the footer is using this method
      if(sizeof($subNavResult) == 0){ $fromFooter = TRUE; }

    } else {

      // if the user did click on a link from the main nav, get all the sub nav links that match the nav category link id of the link they clicked on  
      $sql = '  SELECT  Title, Link, OutsideLink
                FROM    SUBNAV_LINK
                WHERE   NavCategoryLinkID =' .$navCatResult[0]['NavCategoryLinkID']. ' ORDER BY DisplayOrder';
    }

    // prepare the statement object
    $statement = $conn->prepare($sql);

    if($fromFooter == FALSE){   $statement->execute();}

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.

    $conn = null;
    return $result;
  }

  function queryFrontPageArtwork(){

    $query = 'SELECT ArtworkID FROM FRONT_PAGE_ARTWORK';
    $conn = db_connect();

    $result = @$conn->query($query);
    if (!$result) {
      mysqli_close($conn);
      return false;
    }
      
    $num_cats = @$result->num_rows;
      
    if ($num_cats == 0) {
      mysqli_close($conn);
      return false;
    }
      
    $result = db_result_to_array($result);
    mysqli_close($conn);
    return $result;   

  }

  function queryFrontPageExhibitions(){

    // today's date
    $today = date("Y-m-d");

    $conn = pdo_connect();

    $sql = 'SELECT ArtworkReferenceNo as ArtworkID, Title, Link FROM EXHIBITION WHERE :today BETWEEN StartDate AND EndDate ORDER BY Rank';

    $statement = $conn->prepare($sql);

    $statement->bindValue(":today", $today, PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.
    $conn = null;
    return $result;
  }

  // VIP! Do not use when accepting parameters from a user <form> or url
  // returns the tuple from specified table and value
  function queryArtworkArtistInfo($art){
      $artworkQuery;
      $artistArtworkQuery;
      $artistNames;
      $artistQuery;
      $result;

      if(is_array($art)){
        // build main image artist name (by querying ARTWORK with artworkReferenceNo in the EXHIBITION table)
        if($artworkQuery = queryReference('ARTWORK', 'ArtworkID', $art['ArtworkID'])){

          // check if artist_artwork table gets referenced
          if($artistArtworkQuery = queryReference('ARTIST_ARTWORKS', 'ArtworkID', $artworkQuery[0]['ArtworkID'])){

            // build the artistNames string
            $artistNames ="";

            // loop through the artwork query
            foreach($artistArtworkQuery as $artist){

              // query the ARTIST table with corresponding ID
              if($artistsQuery = queryReference('ARTIST', 'ArtistID', $artist['ArtistID'])){

                // build name and concatenate each return string
                $artistNames .= buildArtistName($artistsQuery[0]);

              }
            }
          }
        }
      
        $result = array("artwork" => $artworkQuery, "artists" => $artistNames);
      }
      return $result;
  }

  /** 
  *Used with the subNav Calendar. Queries the database according to the month the user stops scroll
  *@param the month the user stopped scrolling on (yyyy-mm--dd)
  *@return associative query result
  **/
  function querySubNavCalendar($month){

    if(empty($month)){

      //handle error
    
    } else {

      $startDate = $month. "-01";
      $endDate = $month. "-31";

      $result = array();

      // create the connection
      $conn = pdo_connect();

      // write the generic statement
      $sql = 'SELECT  StartDate, EndDate, StartTime, EndTime, E.Title as EventTitle, E.Description, E.ImgFilePath, E.EventTypeID, E.AdmissionCharge, ImgCaption, E.Link, K.Word as TypeTitle, E.OutsideLink
              FROM    EVENT_DATE_TIMES ED, EVENT E, KEYWORD K
              WHERE   E.EventTypeID = K.KeywordID AND ED.EventID = E.EventID AND ED.StartDate BETWEEN :startDate AND :endDate AND E.Canceled IS NOT TRUE ORDER BY ED.StartDate';
          
      // prepare the statement object
      $statement = $conn->prepare($sql);

      $statement->bindValue(":startDate", $startDate, PDO::PARAM_STR);
       $statement->bindValue(":endDate", $endDate, PDO::PARAM_STR);

      $statement->execute();

      //Fetch all of the results.
      $result = $statement->fetchAll(PDO::FETCH_ASSOC);



      // sort result by date
      usort($result, 'date_compare');
      $conn = null;
      return $result;
    }
  }

  /** 
  *Retrieves keywords and their parents
  *@param None
  *@return associative query result
  **/
  function queryKeywordsAndsParents(){

    $result = array();
    $parentKeywords = array();
    $keywords = array();

    // create the connection
    $conn = pdo_connect();

    // write the generic statement
    $sql = 'SELECT  ParentKeywordID, Word
            FROM    PARENT_KEYWORD ORDER BY DisplayOrder';
          
    // prepare the statement object
    $statement = $conn->prepare($sql);

    $statement->execute();

    //Fetch all of the results.
    $parentKeywords = $statement->fetchAll(PDO::FETCH_ASSOC);

    // write the generic statement
    $sql = 'SELECT  K.KeywordID, K.Word, PKK.ParentKeywordID
            FROM    KEYWORD K, PARENT_KEYWORD_KEYWORD PKK
            WHERE   PKK.KeywordID = K.KeywordID ORDER BY K.Word ASC';

    // prepare the statement object
    $statement = $conn->prepare($sql);

    $statement->execute();

    $keywords = $statement->fetchAll(PDO::FETCH_ASSOC);

    $conn = null;

    $result[0] = $parentKeywords;
    $result[1] = $keywords;

    return $result;

   }

   function queryMemberCheck($number, $exp){

    $conn = pdo_connect();

    $sql = 'SELECT MemberID, MemberActualID, MemberEmail, ExpirationDate FROM MEMBER WHERE MemberActualID = :memberActualID AND ExpirationDate = :expirationDate ';

    $statement = $conn->prepare($sql);

    $statement->bindValue(":memberActualID", $number, PDO::PARAM_STR);
    $statement->bindValue(":expirationDate", $exp, PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result = $result[0];
    //$result now contains the entire resultset from the query.
    $conn = null;
    

    return $result;

   }

   // not a get function. Used in member update webpage
   function updateMemberEmail($emailString){

    $emails = array();
    $temp = array();

    // get an array ready for updating database
    $temp = explode(";", $emailString);
    // check if $temp array has an even number of elements (explode adds 1 empty string at end of array)
    if (sizeof($temp)%2 == 1){
      for($i = 0; $i < sizeof($temp); $i++){

        // since the string is set up like this: mem#, email, mem#, email, mem#, email....
        if($i%2==1){
          array_push($emails, array('MemberID' => $temp[$i-1], 'email' => $temp[$i]));
        }
      }
    
      $conn = pdo_connect();

      $sql = 'UPDATE  MEMBER
              SET     MemberEmail = :email, Updated = :datetime
              WHERE   MemberID = :memberID';

      // prepare the statement object
      $statement = $conn->prepare($sql);

      foreach($emails as $email){
        $statement->bindValue(":email", $email['email'], PDO::PARAM_STR);
        $statement->bindValue(":memberID", $email['MemberID'], PDO::PARAM_STR);
        $statement->bindValue(":datetime", date('Y-m-d H:i:s'), PDO::PARAM_STR);

        $statement->execute();

      }

      $conn = null;

      } else {
        // handle error
      }




   }


?>