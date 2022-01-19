<?php
session_start();

    if(isset($_REQUEST)) {
        // GET DATA
        $data           = json_decode(json_encode($_REQUEST),true);
        $search_type    = $data['search_type'];
        $apiKey         = $data['api-key'];
        $channelId      = $data['channel-id'];
        $resultsNumber  = $data['max-result'];
        $playlist_id    = $data['playlist-id'];

        // CHECK SEARCH TYPE
        if($search_type == 1) { // SEARCH VIDEOS WITH CHANNEL ID
            $requestUrl     = 'https://www.googleapis.com/youtube/v3/search?key=' . $apiKey . '&channelId=' . $channelId . '&part=snippet&maxResults=' . $resultsNumber .'&order=date'; 

        } else { // SEARCH VIDEOS WITH PLAYLIST ID
            $requestUrl = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId='.$playlist_id.'&key='. $apiKey;
        }

        // Use cURL then...
        if( function_exists( 'curl_version' ) ) {
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_URL, $requestUrl );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, TRUE );
            $response = curl_exec( $curl );
            curl_close( $curl );
            $json_response = json_decode( $response, TRUE );
        } else {
            // Unable to get response if both file_get_contents and cURL fail
            $json_response = '';
        }

        echo json_encode(['result' => $json_response]);
        exit();
    }
?>