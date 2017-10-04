<?php

$to      = 'nicolaiarty@gmail.com';
$subject = 'Website ';
$emailMessage = '<h3>Email from ';
$webMessage = '<div id="returnForm">' ."\r\n";
$typeOfForm = '';
$headers = 'From: website@nbmaa.org' . "\r\n" .
    'Reply-To: artymiakn@nbmaa.org' . "\r\n" .
    'Content-type: text/html; charset=iso-8859-1'.
    'X-Mailer: PHP/' . phpversion();

$typeOfForm = $_POST["formType"];
$subject .= $typeOfForm ;
$emailMessage .= $typeOfForm. "</h3>"; 

for($i = 0; $i<count($_POST["data"]) - 2; $i++) {
	$webMessage .= "<span class=\"returnData\">" .$_POST["data"][$i]. "</span>\r\n";
	$emailMessage .= $_POST["data"][$i]. "<br>\r\n";
}

echo "<h2>Thank you!</h2> <p>The following information has been submitted to our Educuation Department:</p>\r\n";
echo $webMessage;
echo "</div>\r\n";

mail($to, $subject, $emailMessage, $headers);

?>