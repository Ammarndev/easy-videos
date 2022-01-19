<?php
session_start();

// INCLUDE CONFIG FILE
require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-config.php';

// CHECKING REQUEST
if(isset($_REQUEST)) {
    // GETTING AND DECODING DATA
    $data           = json_decode(json_encode($_REQUEST),true);
    $apiKey         = $data['api_key'];
    $new_post       = '';
    $video_ids      = '';

    // GETTING VIDEO ID'S FOR IMPORTING
    foreach($data as $ids) {
        foreach($ids as $v_id) {
            $video_ids .=  $v_id . ',';
        }
    }

    // retreive video categories id 
    if ($video_ids != '') {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL             => 'https://www.googleapis.com/youtube/v3/videos?part=snippet&id='. $video_ids .'&key=' . $apiKey,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => '',
        CURLOPT_MAXREDIRS       => 10,
        CURLOPT_TIMEOUT         => 0,
        CURLOPT_FOLLOWLOCATION  => true,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST   => 'GET',
        ));

        $categories_response = curl_exec($curl);    // API EXECUTION
        curl_close($curl);                          // API REQUEST CLOSE
        $res = json_decode($categories_response);   // GETTING API RESPONSE

        // CHECKING API RESPONSE
        if ($res) {
            foreach ($res->items as $item) {
                $video_id       = $item->id;
                $video_title    = $item->snippet->title;
                $categoryId     = $item->snippet->categoryId;

                $args = array(
                    'post_type' => 'videos',
                );

                // GETTING ALL POSTS
                $posts  = get_posts($args);
                $found  = 0;

                // CHECK IF VIDEO ALREADY EXIST
                foreach($posts as $post) {
                    if($post->post_content == $video_id) {
                        $found = 1;
                    }
                }

                // IF NOT FOUND IMPORT VIDEO
                if(!$found) {
                    $new_post = wp_insert_post(array(
                        'post_title'    =>  $video_title, 
                        'post_type'     =>  'videos', 
                        'post_status'   =>  'publish', 
                        'post_content'  =>  $video_id
                    ));
                }
            }
        }
    }

    if($new_post) { // IF VIDEOS IMPORTED SUCCESSFULLY
        echo json_encode(['success' => 'Videos Imported Successfully!']);
        exit();
    } else {        // IF VIDEOS NOT IMPORTED OR VIDEOS ALREADY EXIST
        echo json_encode(['error' => 'Videos Cant Import!']);
        exit();
    }
}


?>