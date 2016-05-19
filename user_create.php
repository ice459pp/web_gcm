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