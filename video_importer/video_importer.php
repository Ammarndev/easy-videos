<?php
/*
Plugin Name: Easy Videos

Description: This plugin is made for client against there custom requirements which import youtube videos to wordpress database.

Author: Ammar

Text Domain: easyvideos
*/

// Pluign activation and deactivation hooks
register_activation_hook(__FILE__, 'video_importer_activation');
register_deactivation_hook(__FILE__, 'video_importer_deactivation');

// Pluign activation function
function video_importer_activation() {
}

// Pluign deactivation function
function video_importer_deactivation() {
}

// Add plugin in side menu bar
add_action('admin_menu', 'video_importer_menu');

// Add plugin in side menu bar function
function video_importer_menu() {
    add_menu_page('Easy Videos','Easy Videos Options', 'administrator', __FILE__, 'video_page');
}

// Include file
function video_page() {
    include( plugin_dir_path( __FILE__ ) . 'welcome.php');
}


// Register Custom Post Type
function videos_post_type() {

	$labels = array(
		'name'                  => _x( 'Videos', 'Videos General Name', 'text_domain' ),
		'singular_name'         => _x( 'Video', 'Video Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Videos', 'text_domain' ),
		'name_admin_bar'        => __( 'Video', 'text_domain' ),
		'archives'              => __( 'Video Archives', 'text_domain' ),
		'attributes'            => __( 'Video Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Video:', 'text_domain' ),
		'all_items'             => __( 'All Videos', 'text_domain' ),
		'add_new_item'          => __( 'Add New Video', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Video', 'text_domain' ),
		'edit_item'             => __( 'Edit Video', 'text_domain' ),
		'update_item'           => __( 'Update Video', 'text_domain' ),
		'view_item'             => __( 'View Video', 'text_domain' ),
		'view_items'            => __( 'View Videos', 'text_domain' ),
		'search_items'          => __( 'Search Video', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into video', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this video', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Video', 'text_domain' ),
		'description'           => __( 'Video Description', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor' ),
		'taxonomies'            => array( 'vid_cat' ),
		'menu_icon' 			=> 'dashicons-media-video',
		'show_in_rest' 			=> true,
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'videos', $args );
}

add_action( 'init', 'videos_post_type', 0 );

// CUSTOM TAXONOMY REGISTRATION
function add_video_taxonomies() {

	register_taxonomy('vid_cat', ['videos'], [
		'label' => __('Video Categories', 'txtdomain'),
		'hierarchical' => true,
		'rewrite' => ['slug' => 'vid_cat'],
		'show_admin_column' => true,
		'show_in_rest' => true,
		'labels' => [
			'singular_name' => __('Video', 'txtdomain'),
			'all_items' => __('All Video Categories', 'txtdomain'),
			'edit_item' => __('Edit Video', 'txtdomain'),
			'view_item' => __('View Video', 'txtdomain'),
			'update_item' => __('Update Video', 'txtdomain'),
			'add_new_item' => __('Add New Video', 'txtdomain'),
			'new_item_name' => __('New Video Name', 'txtdomain'),
			'search_items' => __('Search Video Categories', 'txtdomain'),
			'parent_item' => __('Parent Video', 'txtdomain'),
			'parent_item_colon' => __('Parent Video:', 'txtdomain'),
			'not_found' => __('No Video Categories found', 'txtdomain'),
		]
	]);

	register_taxonomy_for_object_type('vid_cat', 'videos');
}

add_action( 'init', 'add_video_taxonomies', 0 );

// SHORT CODE FOR DISPLAYING VIDEOS ON PAGE
function get_imported_video( ) {
	$paged 		= (get_query_var('paged')) ? get_query_var('paged') : 1;
	$args 		= array( 
		'posts_per_page' 	=> 8, 
		'paged' 			=> $paged,
		'post_type' 		=> 'videos',
		'post_status' 	 	=> 'publish',
		'order' 		 	=> 'ASC',
	);

		// GETTING ALL VIDEOS
		$postslist = new WP_Query( $args );

		if ( $postslist->have_posts() ) :
			while ( $postslist->have_posts() ) : $postslist->the_post(); 
			if(isset(get_the_title())) {
				$title = get_the_title();
			} else {
				$title = "The Title";
			} ?>
				<iframe class="video-frame" src="https://www.youtube.com/embed/<?php echo get_the_excerpt(); ?>" frameborder="0" allowfullscreen title="project_frame"><?php echo $title ?></iframe> 
			<?php endwhile; ?>

			<div class="row">
				<p class="next-button">
					<?php next_posts_link( 'Older Videos', $postslist->max_num_pages ); ?> 
				</p>
				<p class="previous-button">
					<?php previous_posts_link( 'Next Videos &raquo;' );  ?>
				</p>
			</div>

			<!-- CUSTOM STYLING FOR SHORT CODE -->
			<style type=text/css>
				.next-button {
					float: left;
					text-decoration: none;
				}
				.previous-button {
					float: right;
					text-decoration: none;
				}
			</style>
		<?php wp_reset_postdata();
	endif;
}

add_shortcode( 'get_videos', 'get_imported_video' );
?>