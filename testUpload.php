<?php

if(isset($_FILES['file'])) {
	if($_FILES['file']['error'] == 0) {
		$uploadFileName = date("YmdHis");
		$folder = "uploads/";

		$fileNameComponent = explode('.', $_FILES['file']['name']);
		$newFilePath = $folder.$uploadFileName.".".$fileNameComponent[1];

		move_uploaded_file($_FILES['file']['tmp_name'], $newFilePath);

		$array = array(
			"status" => "OK",
			"fileURL" => "http://twebdesign.appluco.com/".$newFilePath,
		);
	} else {
		$array = array(
			"status" => "ERROR"
		);
	}

	echo json_encode($array);
	exit;
}

?>