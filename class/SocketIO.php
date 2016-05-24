<?php
class SocketIO
{
    static public function sendPushNotification($data, $user_id) {
        // 指明给谁推送，为空表示向所有在线用户推送
        $to_uid = $user_id;
        $content = json_encode($data);
        $push_api_url = "http://twebdesign.appluco.com:2121/";
        $post_data = array(
           'type' => 'publish',
           'content' => $content,
           'to' => $to_uid, 
        );
        $ch = curl_init ();
        curl_setopt ($ch, CURLOPT_URL, $push_api_url);
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_data);
        // Actually send the push
        $result = curl_exec($ch);
        // Display errors
        if (curl_errno($ch)) {
            echo curl_error($ch);
        }
        // Close curl handle
        curl_close( $ch );
    }
}
?>