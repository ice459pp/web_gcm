<?php
require_once('../db/db_config.php');

/*
post: email
*/

$email = isset($_POST['email'])? trim($_POST['email']): '';
$device_id = isset($_POST['device_id'])? trim($_POST['device_id']): '';
$result = array();

try {
	if (empty($email) || empty($device_id)) {
		throw new Exception("Parameter error");
	}

	// check the from_email exist in users table.
	$s = $db->Query("SELECT user_id FROM users WHERE email = ? LIMIT 1", $email);
	if ($db->No($s) > 0) {
		$r = $db->fetch($s);
		$user_id = $r['user_id'];
		$updateArr = array(
			'device_id' => $device_id,
		);

		$db->update('users', $updateArr, 'user_id', $user_id);

		$insertRecord = array(
			'user_id' => $user_id,
		);

		$db->insert('rid_update_record', $insertRecord);
		
		$json = array(
			'status' => 'OK', 
			'result' => $result,
			'error' => '',
		);

		echo json_encode($json);

	} else {
		throw new Exception("The user of email does not exist");
	}
} catch (Exception $e) {
	$json = array(
		'status' => 'ERROR', 
		'result' => $result,
		'error' => $e->getMessage(),
	);
	echo json_encode($json);
}

?>