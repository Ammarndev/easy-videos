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
?>