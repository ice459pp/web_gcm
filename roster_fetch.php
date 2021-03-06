<?php
require_once('db/db_config.php');
require_once('libs/gcm/gcm.php');
require_once('libs/gcm/push.php');

/*
post: action, email
*/
$email = isset($_POST['email'])? trim($_POST['email']): '';
$result = array();
try {
	if (empty($email)) {
		throw new Exception("Parameter error");
	}

	$s = $db->Query("SELECT u1.* FROM roster 
				LEFT JOIN users AS u1 ON roster.friend_user_id = u1.user_id 
				LEFT JOIN users AS u2 ON roster.user_id = u2.user_id 
				WHERE u2.email = ?", $email);


	if ($db->No($s) > 0) {
		while($user = $db->fetch($s, MYSQL_ASSOC)) {
			$result[] = array(
				'username' => $user['name'],
				'email' => $user['email'],
			);
		}
	}

	$json = array(
		'status' => 'OK', 
		'result' => $result, 
		'error' => '', 
	);

	echo json_encode($json);

} catch (Exception $e) {
	$jsonArr = array(
		'status' => 'ERROR',
		'result' => $result,
		'error' => $e->getMessage(),
	);

	echo json_encode($jsonArr);
}
?>