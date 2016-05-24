<?php
require_once('../db/db_config.php');

/*
post: userpwd, email, type, device_id
*/
$email = isset($_POST['email'])? trim($_POST['email']): '';
$userpwd = isset($_POST['userpwd'])? trim($_POST['userpwd']): '';
// type: user, consultant, clerk
$type = isset($_POST['type'])? trim($_POST['type']): '';
$device_id = isset($_POST['device_id'])? trim($_POST['device_id']): '';

$result = array();
try {
	if (!empty($email) && !empty($userpwd) && !empty($type)) {

		// check the email whether has been used.
		$s = $db->Query("SELECT * FROM users WHERE email = '" . $email . "' AND type = '" . $type . "' LIMIT 1");
		if ($db->No($s) > 0) {
			$user = $db->fetch($s, MYSQL_ASSOC);
			$result = array(
				'user_id' => $user['user_id'], 
				'username' => $user['name'],
				'email' => $user['email'], 
			);

			$updateArr = array(
				'device_id' => $device_id,
			);

			$db->update("users", $updateArr, "user_id", $user['user_id']);
			
			$jsonArr = array(
				'status' => 'OK', 
				'result' => $result,
				'error' => '',
			);
			echo json_encode($jsonArr);

		} else {
			throw new Exception("This user does not exist.");
		}

	} else {
		throw new Exception("Parameter error");
	}	
} catch (Exception $e) {
	$result = array(
		'user_id' => '',
		'username' => '',
		'email' => '', 
	);
	$jsonArr = array(
		'status' => 'ERROR',
		'result' => $result,
		'error' => $e->getMessage(),
	);

	echo json_encode($jsonArr);
}
?>