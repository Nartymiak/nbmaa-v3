<?php
// connects to the released db
function db_connect() {
   $result = new mysqli('localhost', 'spaceman', 'spiff', 'mars');
   if (!$result) {
      return false;
   }
   $result->set_charset("utf8");
   $result->autocommit(TRUE);
   return $result;
}

// connects to the draft db
function db_connect_draft() {
   $result = new mysqli('localhost', 'spaceman', 'spiff', 'mars');
   if (!$result) {
      return false;
   }
   $result->set_charset("utf8");
   $result->autocommit(TRUE);
   return $result;
}

// connects to the released db
function pdo_connect() {

   try {
      $connection = new PDO('mysql:dbname=nbmaa_nbmaa3;host=localhost', 'spaceman', 'spiff', 
                            array(
                                    PDO::ATTR_PERSISTENT => TRUE, 
                                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                                 )
                           );
   }

   catch (PDOException $e){
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
   }

   
   return $connection;
}

// connects to the draft db
function pdo_connect_draft() {

   try {
      $connection = new PDO('mysql:dbname=nbmaa_nbmaa3_draft;host=localhost', 'spaceman', 'spiff', 
                            array(
                                    PDO::ATTR_PERSISTENT => TRUE, 
                                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                                 )
                           );
   }

   catch (PDOException $e){
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
   }

   
   return $connection;
}



function db_result_to_array($result) {
   $res_array = array();

   for ($count=0; $row = $result->fetch_assoc(); $count++) {
     $res_array[$count] = $row;
   }

   return $res_array;
}
?>
