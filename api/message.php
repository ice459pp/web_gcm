<?php

require_once('../db/db_config.php');

/*
post: to_email, from_email, content
*/

$from_email = isset($_POST['from_email'])? trim($_POST['from_email']): '';
$to_email = isset($_POST['to_email'])? trim($_POST['to_email']): '';
$content = isset($_POST['content'])? trim($_POST['content']): '';

// type : msg, img, audio
$message_type = isset($_POST['message_type'])? trim($_POST['message_type']): '';
$result = array();

try {
	$from_user_email = '';
	$to_user_device = array();
	$to_user_type = '';
	$to_user_id = '';

	if (empty($from_email) || empty($to_email) || empty($content)) {
		throw new Exception("Parameter error");
	}

	// check the from_email exist in users table.
	$s_u1 = $db->Query("SELECT email FROM users WHERE email = ? LIMIT 1", $from_email);
	if ($db->No($s_u1) == 0) {
		throw new Exception("The user of from_email does not exist");
	} else {
		$r_u1 = $db->fetch($s_u1);
		$from_user_email = $r_u1['email'];
	}

	// check the to_email exist in users table.
	$s_u2 = $db->Query("SELECT user_id, device_id, type FROM users WHERE email = '" . $to_email . "' LIMIT 1");
	if ($db->No($s_u2) == 0) {
		throw new Exception("The user of to_email does not exist");
	} else {
		$r_u2 = $db->fetch($s_u2);
		// For pushy api, this is an array parameter
		$to_user_device[] = $r_u2['device_id'];
		$to_user_type = $r_u2['type'];
		$to_user_id = $r_u2['user_id'];
	}

	// data will be sended to the app client end.
    $data = array();
    $data['content'] = $content;
    $data['message_type'] = $message_type;
    $data['from_email'] = $from_user_email;
    $data['created_at'] = date('Y-m-d G:i:s');

	if ($to_user_type == 'clerk') {
		SocketIO::sendPushNotification($data, $to_user_id);
	} else {
		// Send it via Pushy API
		PushyAPI::sendPushNotification($data, $to_user_device);
	}


    $msgInsert = array(
    	'device_id' => $to_user_device[0], 
    	'message_type' => 'msg', 
    	'content' => $content, 
    );
    $db->Insert('message', $msgInsert);
    $result = array(
    	'content' => $content,
    	'message_type' => $message_type, 
    	'from_email' => $from_email, 
    	'to_email' => $to_email, 
    	'created_at' => date('Y-m-d G:i:s'), 
    );

	$json = array(
		'status' => 'OK', 
		'result' => $result,
		'error' => '',
	);

	echo json_encode($json);


} catch (Exception $e) {
	$json = array(
		'status' => 'ERROR', 
		'result' => $result,
		'error' => $e->getMessage(),
	);
	echo json_encode($json);
}
?>