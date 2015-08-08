<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Api
 *
 * @author Jérémie
 */
class Islamine_Api {
    public static function sendGoogleCloudMessage($data, $ids) {
            //------------------------------
            // Replace with real GCM API 
            // key from Google APIs Console
            // 
            // https://code.google.com/apis/console/
            //------------------------------
            $apiKey = 'AIzaSyCTK-inmTc2WII188M1hRyVia52Ptm1dNA';

            //------------------------------
            // Define URL to GCM endpoint
            //------------------------------
            $url = 'https://android.googleapis.com/gcm/send';

            //------------------------------
            // Set GCM post variables
            // (Device IDs and push payload)
            //------------------------------
            $post = array(
                'registration_ids' => $ids,
                'data' => $data,
            );

            //------------------------------
            // Set CURL request headers
            // (Authentication and type)
            //------------------------------
            $headers = array(
                'Authorization: key=' . $apiKey,
                'Content-Type: application/json'
            );

            //------------------------------
            // Initialize curl handle
            //------------------------------
            $ch = curl_init();
            
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);

            //------------------------------
            // Set URL to GCM endpoint
            //------------------------------
            curl_setopt($ch, CURLOPT_URL, $url);

            //------------------------------
            // Set request method to POST
            //------------------------------
            curl_setopt($ch, CURLOPT_POST, true);

            //------------------------------
            // Set our custom headers
            //------------------------------
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            //------------------------------
            // Get the response back as 
            // string instead of printing it
            //------------------------------
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //------------------------------
            // Set post data as JSON
            //------------------------------
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));

            //------------------------------
            // Actually send the push!
            //------------------------------
            $result = curl_exec($ch);

            //------------------------------
            // Error? Display it!
            //------------------------------
            if (curl_errno($ch)) {
                echo 'GCM error: ' . curl_error($ch);
            }

            //------------------------------
            // Close curl handle
            //------------------------------
            curl_close($ch);

            //------------------------------
            // Debug GCM response
            //------------------------------
            echo $result;
        }
}