<?php

require_once('db/db_config.php');
require_once('libs/gcm/gcm.php');
require_once('libs/gcm/push.php');

/*
post: to_email, from_email, content
*/

$from_email = isset($_POST['from_email'])? trim($_POST['from_email']): '';
$content = isset($_POST['content'])? trim($_POST['content']): '';

// type : msg, img, audio
$message_type = isset($_POST['message_type'])? trim($_POST['message_type']): '';
$result = array();

try {
	$from_user_email = '';
	$to_user_rid = '';

	if (empty($from_email) || empty($content)) {
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
	$s_u2 = $db->Query("SELECT gcm_registration_id FROM users WHERE user_id = '1' LIMIT 1");
	if ($db->No($s_u2) == 0) {
		throw new Exception("The user of to_email does not exist");
	} else {
		$r_u2 = $db->fetch($s_u2);
		$to_user_rid = $r_u2['gcm_registration_id'];
	}

	$gcm = new GCM();
    $push = new Push();

    // data will be sended to the app client end.
    $data = array();
    $data['content'] = $content;
    $data['message_type'] = $message_type;
    $data['from_email'] = $from_user_email;
    $data['created_at'] = date('Y-m-d G:i:s');

    $push->setTitle("Google Cloud Messaging");
    $push->setIsBackground(FALSE);
    $push->setFlag(PUSH_FLAG_USER);
    $push->setData($data);


    // sending push message to single user
    $gcm->send($to_user_rid, $push->getPush());

    $result = array(
    	'content' => $content,
    	'message_type' => $message_type, 
    	'from_email' => $from_email, 
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