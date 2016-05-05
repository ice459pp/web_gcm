<?php
require_once('db/db_config.php');
require_once('libs/gcm/gcm.php');
require_once('libs/gcm/push.php');

/*
post: userpwd, email, gcm_registration_id
*/
$email = isset($_POST['email'])? trim($_POST['email']): '';
$userpwd = isset($_POST['userpwd'])? trim($_POST['userpwd']): '';
$gcm_registration_id = isset($_POST['gcm_registration_id'])? trim($_POST['gcm_registration_id']): '';
$result = array();
try {
	if (!empty($email) && !empty($userpwd) && !empty($gcm_registration_id)) {

		// check the email whether has been used.
		$s = $db->Query("SELECT * FROM users WHERE email = ? LIMIT 1", $email);
		if ($db->No($s) > 0) {
			$user = $db->fetch($s, MYSQL_ASSOC);
			$result = array(
				'username' => $user['name'],
				'email' => $user['email'], 
			);
			
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
	$jsonArr = array(
		'status' => 'ERROR',
		'result' => $result,
		'error' => $e->getMessage(),
	);

	echo json_encode($jsonArr);
}
?>