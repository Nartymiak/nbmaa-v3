<?php

function do_html_header($title = '') {
  // print an HTML header
?>
  <html>
  <!DOCTYPE html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <title><?php echo $title; ?></title>
    <link href="" rel="stylesheet" type="text/css" />
  
     <script language="Javascript">
     <!--
     //-->
     </script>
  
  </head>
  <body>
<?php  
}

// displays a table given any result of any size from a MySQL Query

function display_table($table_array) {
    if (!is_array($table_array)) {
     echo "<p>no relations retrieved</p>";
     return;
  }

  //sets up html
  echo "<div class=\"table\">";
  echo "<div class=\"row\">";

  //stores the attributes in an array
  foreach ($table_array as $row) {
    $count++;
    foreach($row as $key => $value){
      $attributes[]=$key;
    }
  }

  // prints out the attributes - divides by count so only prints once
  for($i=0;$i<sizeof($attributes)/$count;$i++){
    echo "<div class=\"attribute\">".$attributes[$i]."</div>";
}

  // print out the table elements
  echo "</div>";
  foreach ($table_array as $row)  {
    echo "<div class=\"row\">";
    foreach ($row as $element){
      echo "<div class=\"element\">".$element."</div>";
    }
    echo "</div>";
  }
  echo "</div>";
}

?>