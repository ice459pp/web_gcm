<?php
require_once('functions.php');
require_once('db/db_config.php');
require_once('libs/gcm/gcm.php');
require_once('libs/gcm/push.php');

/*
post: username, userpwd, email
*/
$username = isset($_POST['username'])? trim($_POST['username']): '';
$userpwd = isset($_POST['userpwd'])? trim($_POST['userpwd']): '';
$email = isset($_POST['email'])? trim($_POST['email']): '';
$result = array();
try {
	if (!empty($username) && !empty($userpwd) && !empty($email)) {

		if (!isEmailValid($email)) {
			throw new Exception("Email invalid");
		}

		// check the email whether has been used.
		$s = $db->Query("SELECT * FROM users WHERE email = ?", $email);
		if ($db->No($s) > 0) {
			throw new Exception("This email has been existed");
		} 

		// insert the user data and gcm registration id
		$insertArr = array(
			'name' => $username,
			'pwd' => $userpwd, 
			'email' => $email,
			'gcm_registration_id' => 'initial', 
		);
		
		$db->Insert('users', $insertArr);

		$last_user_id = $db->last_id();

		// the user of the to_email add the user of from_email as friend
		$s_rt1 = $db->Query("SELECT * FROM roster 
				WHERE user_id = '" . $last_user_id ."' AND friend_user_id = '1' LIMIT 1");
		
		if ($db->No($s_rt1) == 0) {
			$insertArr1 = array(
				'user_id' => $last_user_id, 
				'friend_user_id' => '1',
			);
			$db->Insert('roster', $insertArr1);
		}

		// the user of the from_email add the user of to_email as friend
		$s_rt2 = $db->Query("SELECT * FROM roster 
				WHERE user_id = '1' AND friend_user_id = '" . $last_user_id ."' LIMIT 1");
		if ($db->No($s_rt2) == 0) {
			$insertArr2 = array(
				'user_id' => '1', 
				'friend_user_id' => $last_user_id,
			);
			$db->Insert('roster', $insertArr2);
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