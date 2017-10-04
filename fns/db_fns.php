<?php

function db_connect() {
   $result = new mysqli('localhost', 'nbmaa_nbmaa3', 'JRR{iU}^i}tB', 'nbmaa_nbmaa3');
   if (!$result) {
      return false;
   }
   $result->set_charset("utf8");
   $result->autocommit(TRUE);
   return $result;
}

function pdo_connect() {

   try {
      $connection = new PDO('mysql:dbname=nbmaa_nbmaa3;host=localhost', 'nbmaa_nbmaa3', 'JRR{iU}^i}tB', 
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
