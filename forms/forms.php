<?php
    include_once('../fns/db_fns.php');

    $fileName;
    $fileExtension;
    $filePath;
    $to = 'nicolaiarty@gmail.com';
    
    if(empty($_POST)){

        

    }else {

        if($_FILES['requestDonationFile']['size']>0){

            //var_dump($_FILES);

            $errorMessage = checkFile();

            echo $errorMessage;

            if(!$errorMessage){

                // get parts
                $temp = pathinfo($_FILES['file']['name']);
                //format filename
                $fileName = toLink(urldecode($temp['filename']));
                $fileExtension = strtolower($temp['extension']);
                $filePath = $fileName.'.'.$fileExtension;

                if($id = newEntry($filePath)){
                    $fileNameWithID = $fileName.'-'.$id.'.'.$fileExtension;;
                    move_uploaded_file($_FILES['file']['tmp_name'], '../uploads/' .$fileNameWithID);
                }


                echo '  <div style="padding: 8px;box-sizing: border-box;margin-bottom:8px;" class="row alert-success">
                            <p><strong>Thank you '.$_POST['ContactFname'].'!</strong><br>Your request is being processed and will be mailed within two weeks to:<br><br>';

                            if(!empty($_POST['OrgMailAddress1']) && !empty($_POST['OrgMailCity']) && !empty($_POST['OrgMailZip'])){
                                echo    $_POST['OrgName']. '<br>'.
                                        $_POST['OrgMailAddress1']. '<br>';
                                        if(!empty($_POST['OrgMailAddress2'])) {echo $_POST['OrgMailAddress2']. '<br>';}
                                echo    $_POST['OrgMailCity']. ', ' .$_POST['OrgMailState']. ' '.$_POST['OrgMailZip'];
                            } else {
                                echo    $_POST['OrgName']. '<br>'.
                                        $_POST['OrgAddress1']. '<br>';
                                        if(!empty($_POST['OrgAddress2'])) {echo $_POST['OrgAddress2']. '<br>';}
                                echo    $_POST['OrgCity']. ', ' .$_POST['OrgState']. ' '.$_POST['OrgZip'];
                            }

                echo '      </p>

                        </div>';

                mailMessageTo($to, $fileNameWithID);

            } else {
                echo $errorMessage;
            }

        }else{
            
            if($id = newEntry('nofile')){
                echo '  <div style="padding: 8px;box-sizing: border-box;margin-bottom:8px;" class="row alert-success">
                            <p><strong>Thank you '.$_POST['ContactFname'].'!</strong><br>Your request is being processed and will be mailed within two weeks to:<br><br>';

                            if(!empty($_POST['OrgMailAddress1']) && !empty($_POST['OrgMailCity']) && !empty($_POST['OrgMailZip'])){
                                echo    $_POST['OrgName']. '<br>'.
                                        $_POST['OrgMailAddress1']. '<br>';
                                        if(!empty($_POST['OrgMailAddress2'])) {echo $_POST['OrgMailAddress2']. '<br>';}
                                echo    $_POST['OrgMailCity']. ', ' .$_POST['OrgMailState']. ' '.$_POST['OrgMailZip'];
                            } else {
                                echo    $_POST['OrgName']. '<br>'.
                                        $_POST['OrgAddress1']. '<br>';
                                        if(!empty($_POST['OrgAddress2'])) { echo $_POST['OrgAddress2']. '<br>';}
                                echo    $_POST['OrgCity']. ', ' .$_POST['OrgState']. ' '.$_POST['OrgZip'];
                            }

                            echo '<br>';
                            //var_dump($_POST);

                echo '      </p>

                        </div>';

                mailMessageTo($to, 'nofile');
            }
        }
    }

    function newEntry($uploadFileName){

        $conn = pdo_connect();

        $sql = 'INSERT INTO REQUEST_FOR_DONATION
                VALUES (null, :OrgName, :NonProfit, :OrgPurpose, :OrgAddress1, :OrgAddress2, :OrgCity, :OrgState, :OrgZip, :OrgCountry,
                        :OrgMailAddress1, :OrgMailAddress2, :OrgMailCity, :OrgMailState, :OrgMailZip, :OrgMailCountry, :ContactFname, :ContactLname,
                        :ContactTitle, :ContactPhone, :ContactEmail, :EventTitle, :EventDate, :EventDescription, :SpecialRequests, :uploadFileName, null, null)';

        // prepare the statement object
        $statement = $conn->prepare($sql);

        $statement->bindValue(":OrgName", $_POST['OrgName'], PDO::PARAM_STR);
        $statement->bindValue(":NonProfit", $_POST['SchoolOrNonProfit'], PDO::PARAM_STR);
        $statement->bindValue(":OrgPurpose", $_POST['OrgPurpose'], PDO::PARAM_STR);
        $statement->bindValue(":OrgAddress1", $_POST['OrgAddress1'], PDO::PARAM_STR);
        $statement->bindValue(":OrgAddress2", $_POST['OrgAddress2'], PDO::PARAM_STR);
        $statement->bindValue(":OrgCity", $_POST['OrgCity'], PDO::PARAM_STR);
        $statement->bindValue(":OrgState", $_POST['OrgState'], PDO::PARAM_STR);
        $statement->bindValue(":OrgZip", $_POST['OrgZip'], PDO::PARAM_STR);
        $statement->bindValue(":OrgCountry", $_POST['OrgCountry'], PDO::PARAM_STR);
        $statement->bindValue(":OrgMailAddress1", $_POST['OrgMailAddress1'], PDO::PARAM_STR);
        $statement->bindValue(":OrgMailAddress2", $_POST['OrgMailAddress2'], PDO::PARAM_STR);
        $statement->bindValue(":OrgMailCity", $_POST['OrgMailCity'], PDO::PARAM_STR);
        $statement->bindValue(":OrgMailState", $_POST['OrgMailState'], PDO::PARAM_STR);
        $statement->bindValue(":OrgMailZip", $_POST['OrgMailZip'], PDO::PARAM_STR);
        $statement->bindValue(":OrgMailCountry", $_POST['OrgMailCountry'], PDO::PARAM_STR);
        $statement->bindValue(":ContactFname", $_POST['ContactFname'], PDO::PARAM_STR);
        $statement->bindValue(":ContactLname", $_POST['ContactLname'], PDO::PARAM_STR);
        $statement->bindValue(":ContactTitle", $_POST['ContactTitle'], PDO::PARAM_STR);
        $statement->bindValue(":ContactPhone", $_POST['ContactPhone'], PDO::PARAM_STR);
        $statement->bindValue(":ContactEmail", $_POST['ContactEmail'], PDO::PARAM_STR);
        $statement->bindValue(":EventTitle", $_POST['EventTitle'], PDO::PARAM_STR);
        $statement->bindValue(":EventDate", $_POST['EventDate'], PDO::PARAM_STR);
        $statement->bindValue(":EventDescription", $_POST['EventDescription'], PDO::PARAM_STR);
        $statement->bindValue(":SpecialRequests", $_POST['SpecialRequests'], PDO::PARAM_STR);
        $statement->bindValue(":uploadFileName", $uploadFileName, PDO::PARAM_STR);

        $statement->execute();
        $id = $conn->lastInsertId();

        $conn = null;
        return $id;

    }

    function checkFile(){

        // return value
        $errorMessage = false;

        $valid_mime_types = array("application/pdf");
        $valid_file_extensions = array(".pdf");
        $file_extension = strtolower(strrchr($_FILES["file"]["name"], "."));

        if ( 0 < $_FILES['file']['error'] ) {
            $errorMessage = "<p>There was a connection error while uploading. Please try again.</p>";
        
        }else if(!in_array($_FILES["file"]["type"], $valid_mime_types)) {
            $errorMessage = "<p>Sorry! Your file was the wrong type.</p>";
        
        } else if (!in_array($file_extension, $valid_file_extensions)) {
            $errorMessage = "<p>Sorry! Your file was the wrong type.</p>";
        
        }else if($_FILES['file']['size'] > 2000000){
            $errorMessage = "<p>Sorry! Your file was too large. Try to upload a file that is under 2MB.</p>";
        }

        return $errorMessage;

    }

    function toLink($string){

        // replace "&" with "and"
        $link = str_replace("&", "and", $string);
        // strip all but forward slashes, newlines, letters and numbers
        // make it all lowercase
        $link = strtolower ( preg_replace('/[^A-Za-z0-9\n\/ \-]/', '', $link));
        // replace spaces, new lines and forward slashes with dashes
        $link = str_replace(array(" ", "\n", "/"), "-", $link);
        // sometimes it makes a double slash so replace those with single slash
        $link = str_replace("--", "-", $link);
        // check if last character is a dash, if so, trim it off
        if(substr($link, -1, 1)=="-"){
            $link = substr($link, 0, -1);
        }

        return $link;
    }

    function mailMessageTo($to, $uploadFileName){

        date_default_timezone_set('America/New_York');
        $date = date("F jS, Y", time());

        $subject = "A Request for donation has been submitted";

        $message = '
        <html>
            <head>
                <title>Request for donation</title>
            </head>
            <body>
                <h3>Request for donation from<br>'.$_POST['OrgName'].'<br>
                made by '.$_POST['ContactFname'].' '.$_POST['ContactLname'].'</h3>

                <p> '.$date.'<br><br>
                    '.$_POST['OrgName'].'<br>
                    '.$_POST['OrgAddress1'].'<br>
                    '.$_POST['OrgAddress2'].'<br>
                    '.$_POST['OrgCity'].', '.$_POST['OrgState'].' '.$_POST['OrgZip'].'<br>
                    '.$_POST['OrgCountry'].'<br><br>
                    ';
                    if(!empty($_POST['OrgMailAddress1']) && !empty($_POST['OrgMailCity']) && !empty($_POST['OrgMailZip'])){
                        $message .= '<strong>Optional Mailing Address</strong><br>
                                    '.$_POST['OrgMailAddress1'].'<br>
                                    '.$_POST['OrgMailAddress2'].'<br>
                                    '.$_POST['OrgMailCity'].', '.$_POST['OrgMailState'].' '.$_POST['OrgMailZip'].'<br>
                                    '.$_POST['OrgMailCountry'].'<br><br>';
                    }
        $message.=  '<strong>Information</strong><br>
                    School or non profit? '.$_POST['SchoolOrNonProfit'].'<br>
                    Organization Purpose: '.$_POST['OrgPurpose'].'<br><br>
                    <strong>Contact</strong><br>
                    '.$_POST['ContactFname'].' '.$_POST['ContactLname'].'<br>
                    '.$_POST['ContactTitle'].'<br>
                    '.$_POST['ContactPhone'].'<br>
                    '.$_POST['ContactEmail'].'<br><br>
                    <strong>Event</strong><br>
                    '.$_POST['EventTitle'].'<br>
                    '.$_POST['EventDate'].'<br>
                    '.$_POST['EventDescription'].'<br>
                    '.$_POST['SpecialRequests'].'<br><br>';
                    if($uploadFileName != 'nofile'){
                        $message.= 'User provided a form: <a href="http://www.nbmaa.org/uploads/'.$uploadFileName.'">Download the file</a>';
                    }else{
                        $message.= 'User did not upload a form';
                    }
        $message.= '
                </p>
            </body>
        </html>
        ';

        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // More headers
        $headers .= 'From: <donationRequestForm@nbmaa.org>' . "\r\n";

        mail($to,$subject,$message,$headers);

    }


?>