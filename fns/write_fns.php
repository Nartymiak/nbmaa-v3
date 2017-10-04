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
		}
	}
?>