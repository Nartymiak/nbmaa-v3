<?php
	function writeEvent() {


		if(!$_POST) {
			// handle error
		} else {

			$conn = pdo_connect();

			$sql = 'UPDATE 	EVENT
					SET 	Title = :title, ImgFilePath = :imgFilePath, Description = :description, AdmissionCharge = :admissionCharge
					WHERE 	EventID = :eventID';

			// prepare the statement object
			$statement = $conn->prepare($sql);

			$statement->bindValue(":title", $_POST['Title'], PDO::PARAM_STR);
			$statement->bindValue(":imgFilePath", $_POST['ImgFilePath'], PDO::PARAM_STR);
			$statement->bindValue(":description", $_POST['Description'], PDO::PARAM_STR);
			$statement->bindValue(":admissionCharge", $_POST['AdmissionCharge'], PDO::PARAM_STR);
			$statement->bindValue(":eventID", $_POST['EventID'], PDO::PARAM_STR);


			$statement->execute();

			if($_POST['dateTimes']) {

				$sql = 'UPDATE 	EVENT_DATE_TIMES
						SET 	StartDate = :startDate, EndDate = :endDate, StartTime = :startTime, EndTime = :endTime
						WHERE 	EventID = :eventID AND StartDate <=> :oldStartDate AND StartTime <=> :oldStartTime AND EndTime <=> :oldEndTime';

				// prepare the statement object
				$statement = $conn->prepare($sql);

				foreach($_POST['dateTimes'] as $dateTime) {				

					$statement->bindValue(":startDate", $dateTime['StartDate'], PDO::PARAM_STR);
					$statement->bindValue(":endDate", $dateTime['EndDate'], PDO::PARAM_STR);
					$statement->bindValue(":startTime", $dateTime['StartTime'], PDO::PARAM_STR);
					$statement->bindValue(":endTime", $dateTime['EndTime'], PDO::PARAM_STR);
					$statement->bindValue(":eventID", $_POST['EventID'], PDO::PARAM_STR);
					
					if($dateTime['oldStartDate'] != '') { $statement->bindValue(":oldStartDate", $dateTime['oldStartDate'], PDO::PARAM_STR); }
					else { 	$statement->bindValue(":oldStartDate", NULL, PDO::PARAM_INT); }
					
					if($dateTime['oldStartTime'] != '') { $statement->bindValue(":oldStartTime", $dateTime['oldStartTime'], PDO::PARAM_STR); }
					else {	$statement->bindValue(":oldStartTime", NULL, PDO::PARAM_INT); }
					
					if($dateTime['oldEndTime'] != '') {$statement->bindValue(":oldEndTime", $dateTime['oldEndTime'], PDO::PARAM_STR); }
					else { 	$statement->bindValue(":oldEndTime", NULL, PDO::PARAM_INT); }

					$statement->execute();
				}
			}

			if($_POST['add'])	{

				$sql = 'INSERT INTO 	EVENT_DATE_TIMES (EventID, StartDate, EndDate, StartTime, EndTime)
						VALUES 			(:eventID, :startDate, :endDate, :startTime, :endTime)';

				// prepare the statement object
				$statement = $conn->prepare($sql);

				foreach($_POST['add'] as $dateTime) {				

					$statement->bindValue(":eventID", $_POST['EventID'], PDO::PARAM_STR);
					$statement->bindValue(":startDate", $dateTime['StartDate'], PDO::PARAM_STR);
					$statement->bindValue(":endDate", $dateTime['EndDate'], PDO::PARAM_STR);
					$statement->bindValue(":startTime", $dateTime['StartTime'], PDO::PARAM_STR);
					$statement->bindValue(":endTime", $dateTime['EndTime'], PDO::PARAM_STR);
					
					$statement->execute();
				}

			}

			if($_POST['delete']) {

				$sql = 'DELETE
						FROM 	EVENT_DATE_TIMES
						WHERE 	EventID = :eventID AND StartDate <=> :oldStartDate AND StartTime <=> :oldStartTime AND EndTime <=> :oldEndTime';

				// prepare the statement object
				$statement = $conn->prepare($sql);

				foreach($_POST['delete'] as $key=>$el) {

					$statement->bindValue(":eventID", $_POST['EventID'], PDO::PARAM_STR);

					if($_POST['dateTimes'][$key]['oldStartDate'] != '') { $statement->bindValue(":oldStartDate", $_POST['dateTimes'][$key]['oldStartDate'], PDO::PARAM_STR); }
					else { 	$statement->bindValue(":oldStartDate", NULL, PDO::PARAM_INT); }
					
					if($_POST['dateTimes'][$key]['oldStartTime'] != '') { $statement->bindValue(":oldStartTime",$_POST['dateTimes'][$key]['oldStartTime'], PDO::PARAM_STR); }
					else {	$statement->bindValue(":oldStartTime", NULL, PDO::PARAM_INT); }
					
					if($_POST['dateTimes'][$key]['oldEndTime'] != '') {$statement->bindValue(":oldEndTime", $_POST['dateTimes'][$key]['oldEndTime'], PDO::PARAM_STR); }
					else { 	$statement->bindValue(":oldEndTime", NULL, PDO::PARAM_INT); }

					$statement->execute();

				}

			}		
			$conn = null;
		}
	}

function writeStaticPage() {


		if(!$_POST) {
			// handle error
		} else {

			$conn = pdo_connect();

			$sql = 'UPDATE 	STATIC_PAGE
					SET 	Title = :title, ImgFilePath = :imgFilePath, Body = :body
					WHERE 	StaticPageID = :staticPageID';

			// prepare the statement object
			$statement = $conn->prepare($sql);

			$statement->bindValue(":title", $_POST['Title'], PDO::PARAM_STR);
			$statement->bindValue(":imgFilePath", $_POST['ImgFilePath'], PDO::PARAM_STR);
			$statement->bindValue(":body", $_POST['Body'], PDO::PARAM_STR);
			$statement->bindValue(":staticPageID", $_POST['StaticPageID'], PDO::PARAM_STR);


			$statement->execute();


			if($_POST['delete']) {

				$sql = 'DELETE
						FROM 	STATIC_PAGE
						WHERE 	StaticPageID = :StaticPageID';

				// prepare the statement object
				$statement = $conn->prepare($sql);

				foreach($_POST['delete'] as $key=>$el) {

					$statement->bindValue(":eventID", $_POST['EventID'], PDO::PARAM_STR);

					if($_POST['dateTimes'][$key]['oldStartDate'] != '') { $statement->bindValue(":oldStartDate", $_POST['dateTimes'][$key]['oldStartDate'], PDO::PARAM_STR); }
					else { 	$statement->bindValue(":oldStartDate", NULL, PDO::PARAM_INT); }
					
					if($_POST['dateTimes'][$key]['oldStartTime'] != '') { $statement->bindValue(":oldStartTime",$_POST['dateTimes'][$key]['oldStartTime'], PDO::PARAM_STR); }
					else {	$statement->bindValue(":oldStartTime", NULL, PDO::PARAM_INT); }
					
					if($_POST['dateTimes'][$key]['oldEndTime'] != '') {$statement->bindValue(":oldEndTime", $_POST['dateTimes'][$key]['oldEndTime'], PDO::PARAM_STR); }
					else { 	$statement->bindValue(":oldEndTime", NULL, PDO::PARAM_INT); }

					$statement->execute();


				}

			}
			$conn = null;
		}
	}
?>