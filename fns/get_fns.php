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
    $statement = $conn->prepare("SELECT * FROM EVENT WHERE Link = :link");
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
    //echo $sql;

    $statement = $conn->prepare($sql);

    $statement->bindValue(":value", $value, PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.
    
    return $result;
  }

  function queryCalendarByRange($table, $attribute, $firstDate, $lastDate){

    $conn = pdo_connect();

    $sql = 'SELECT * FROM '.$table. ' WHERE '.$attribute. ' BETWEEN :firstDate AND :lastDate ORDER BY '.$attribute;
    //echo $sql;

    $statement = $conn->prepare($sql);

    $statement->bindValue(":firstDate", $firstDate, PDO::PARAM_STR);
    $statement->bindValue(":lastDate", $lastDate, PDO::PARAM_STR);

    $statement->execute();

    //Fetch all of the results.
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    //$result now contains the entire resultset from the query.
    
    return $result;

  }



?>