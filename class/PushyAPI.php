<?php
class PushyAPI
{
    static public function sendPushNotification($data, $ids) {
        // Your Pushy API key
        $apiKey = 'a169efee9245359ebd227cd9d9c2d28868d9aaa64ddcc4408f48b8e0440ba8c4';

        // Define URL to Pushy endpoint
        $url = 'https://api.pushy.me/push?api_key=' . $apiKey;

        // Set post variables
        $post = array (
            'registration_ids'  => $ids,
            'data'              => $data,
        );

        // Set Content-Type since we're sending JSON
        $headers = array (
            'Content-Type: application/json'
        );

        // Initialize curl handle
        $ch = curl_init();

        // Set URL to Pushy endpoint
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set request method to POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Set our custom headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Get the response back as string instead of printing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set post data as JSON
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));

        // Actually send the push
        $result = curl_exec($ch);

        // Display errors
        if (curl_errno($ch)) {
            echo curl_error($ch);
        }

        // Close curl handle
        curl_close($ch);

        // Debug API response
        //echo $result;
    }
}
?>