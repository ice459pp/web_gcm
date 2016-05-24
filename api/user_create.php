<?php
require_once('functions.php');
require_once('../db/db_config.php');

/*
post: username, userpwd, email
*/
$username = isset($_POST['username'])? trim($_POST['username']): '';
$userpwd = isset($_POST['userpwd'])? trim($_POST['userpwd']): '';
$email = isset($_POST['email'])? trim($_POST['email']): '';
$type = isset($_POST['type'])? trim($_POST['type']): '';
$result = array();
try {
	if (!empty($username) && !empty($userpwd) && !empty($email) && !empty($type)) {

		if (!isEmailValid($email)) {
			throw new Exception("Email invalid");
		}

		switch ($type) {
			case 'user':
			case 'consultant':
			case 'clerk':
				break;
			
			default:
				throw new Exception("Type invalid");
				break;
		}

		// check the email whether has been used.
		$s = $db->Query("SELECT * FROM users WHERE email = ?", $email);
		if ($db->No($s) > 0) {
			throw new Exception("This email has been existed");
		} 

		$insertArr = array(
			'name' => $username,
			'pwd' => $userpwd, 
			'email' => $email,
			'type' => $type, 
			'device_id' => 'initial', 
		);
		
		$db->Insert('users', $insertArr);

		$last_user_id = $db->last_id();

		// the user of the to_email add the user of from_email as friend
		$s_rt = $db->Query("SELECT * FROM users WHERE user_id <> '" . $last_user_id . "'");
		if ($db->No($s_rt) > 0) {
			while($r_rt = $db->fetch($s_rt)) {
				$insertArr1 = array(
					'user_id' => $last_user_id, 
					'friend_user_id' => $r_rt['user_id'],
				);
				$db->Insert('roster', $insertArr1);

				$insertArr2 = array(
					'user_id' => $r_rt['user_id'], 
					'friend_user_id' => $last_user_id,
				);
				$db->Insert('roster', $insertArr2);
			}
		}

		$jsonArr = array(
			'status' => 'OK', 
			'result' => $result,
			'error' => '',
		);

		echo json_encode($jsonArr);

	} else {
		throw new Exception("Parameter error");
	}	
} catch (Exception $e) {
	$jsonArr = array(
		'status' => 'ERROR',
		'result' => $result,
		'error' => $e->getMessage(),
	);

	echo json_encode($jsonArr);
}
?>