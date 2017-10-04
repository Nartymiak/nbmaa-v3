<?php
	include_once('../fns/functions.php');

	//varibles
	$number;
	$expire;
	$result;
	$memberInfo = array();

 if ($_POST['emails'] != NULL){

		echo "<h3>Thanks!</h3>\r\n";
		echo "<p id=\"thanksSuccess\">Get ready to be notified of upcoming exhibitions, Members-only previews, unique programs, special events, and more!</p>\r\n";

		updateMemberEmail($_POST['emails']);

	} else if(empty($_POST['expire']) || empty($_POST['number']) ) {

		// handle error

	} else {

		$number = $_POST['number'];
		$expire = $_POST['expire'];

		$result = queryMemberCheck($number, $expire);

		if(!empty($result)){

			foreach($result as $record){
				array_push($memberInfo, array("email" => $record['MemberEmail'], "memberID" => $record['MemberID']));
			}

			if(sizeof($memberInfo) > 1){

				echo "<h3>Found you!</h3>\r\n";
				echo "<p>Are they correct? If not you can change them here:</p>\r\n";
			
			} else if(sizeof($memberInfo) == 1 && $memberInfo[0]['email'] != NULL){

				echo "<h3>Found you!</h3>\r\n";
				echo "<p>Does this look right? If not go ahead and change it here:</p>\r\n";
				
			} else {

				echo "<h3>Found you!</h3>\r\n";
				echo "<p>But we dont have your email address on file. Add one to our records to get notfications about upcoming programs and events!</p>\r\n";
			}

			echo "<div id=\"changeEmail\">\r\n";
			echo "<form>\r\n";

			foreach($memberInfo as $el){
				echo "<p><input class=\"updateEmail\" type=\"text\" data=\"" .$el['memberID']. "\" value=\"" .$el['email']. "\"/></p>\r\n";
			}

			echo "<p style=\"text-align:right;\"><span id=\"changeEmailSubmit\">OK</span></p>\r\n";
			echo "<p id=\"error\">&nbsp;</p>\r\n";
			echo "</div>\r\n";

		} else {
			// html for can't find
			?>

			<h3>Sorry!</h3>
			<p>We can't find you in our records. You can try again or contact Jenna Lucas by phone at (860) 229-0257 ext 221 or by email at <a href="mailto:LucasJ@nbmaa.org">LucasJ@nbmaa.org</a> with any questions.</p>

			<h3>Sign up for our eBlast anyway?</h3>
			<p><a class="emailUpdateButton" href="http://nbmaa.us10.list-manage.com/subscribe?u=2ec02a434f13395267495f67b&id=3d36eae573">Yes! Take me to the form!</a></p>
			<h3>Need to renew or start a new membership?</h3>
			<p><a class="emailUpdateButton" href="http://www.nbmaa.org/museum-of-american-art/individual-members">Indeed! Show me how!</a></p>

			<?php
		}

	}



?>