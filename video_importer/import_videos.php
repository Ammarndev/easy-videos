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

                    // RETRIEVE CATEGORY NAME AND INSERTION OF CATEGORY
                    processPost($categoryId, $new_post, $apiKey);
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

// CATEGORY NAME API
function processPost($category_id, $video_id, $apiKey) {
    if($category_id != '') {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL             => 'https://www.googleapis.com/youtube/v3/videoCategories?part=snippet&id='. $category_id .'&key=' . $apiKey,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => "",
        CURLOPT_MAXREDIRS       => 10,
        CURLOPT_TIMEOUT         => 30,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST   => "GET",
        CURLOPT_HTTPHEADER      => array(
            "cache-control: no-cache",
            "postman-token: 5ef4ffed-48c8-c2f7-560e-d1e6bb00c05b"
            ),
        ));

        $response = curl_exec($curl);   // API EXECUTE
        curl_close($curl);              // API REQUEST CLOSE
        $res = json_decode($response);  // API RESPONSE DECODE

        // CHECK RESPONSE
        if($res) {
            // GETTING CATEGORY NAME IN API RESPONSE
            foreach($res->items as $item) {
                $category_id     = $item->id;
                $category_name   = $item->snippet->title;

                // GETTING ALL CATEGORIES
                $categories = get_terms([
                    'taxonomy' => 'vid_cat',
                    'hide_empty' => false,
                ]);

                $found  = 0;
                $cat_id = 0;

                // CHECK IF CATEGORY ALREADY EXIST
                foreach( $categories as $category ) {
                    if(trim($category->description) == $category_id) {
                        $found  = 1;
                        $cat_id = $category->term_id;
                    }
                }

                if($found) {
                    // set category to new post
                    wp_set_post_terms( $video_id, $cat_id, 'vid_cat');

                } else {
                    require_once( ABSPATH . '/wp-admin/includes/taxonomy.php');
                    $category = array(
                        'cat_name'              => $category_name,
                        'category_description'  => $category_id,
                        'category_nicename'     => $category_name,
                        'category_parent'       => '',
                        'taxonomy'              => 'vid_cat'
                    );

                    // Create the category
                    $new_category = wp_insert_category($category);

                    // set category to new post
                    wp_set_post_terms( $video_id, $new_category, 'vid_cat');
                }
            }
        }
    }
}
?>