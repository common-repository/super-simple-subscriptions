<?php
/*
Plugin Name: Super Simple Subscriptions
Description: Subscribe to an event created with the Super Simple Events plugin
Version:     1.1.0
Contributors Marcel-NL
Author:      Marcel van Doornen
Author URI:  http://www.webbouwplus.nl
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: super_simple_subscriptions
*/

// @todo plugin logo @link https://make.wordpress.org/core/2014/08/21/introducing-plugin-icons-in-the-plugin-installer/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define('SSS_PLUGIN_PATH', __FILE__);

// Set translation path.
function super_simple_subscription_init() {
  load_plugin_textdomain( 'super_simple_subscriptions', false, 'super-simple-subscriptions/languages' );
}
add_action('init', 'super_simple_subscription_init');

// Load helper functions.
require_once( plugin_dir_path( __FILE__ ) . 'includes/helpers.php' );


// Installation ----------------------------------------------------------------
require_once( plugin_dir_path( __FILE__ ) . 'includes/install.php' );

register_activation_hook( __FILE__ , 'sss_activate_plugin');

// End installation ------------------------------------------------------------


// Enable/disable event subscriptions ------------------------------------------
require_once( plugin_dir_path( __FILE__ ) . 'includes/admin.post.php' );

// Add option to subscribe for an event.
add_action( 'add_meta_boxes', 'sss_add_event_metabox' );
add_action( 'save_post', 'sss_save_meta_box_data' );

// End Enable/disable event subscriptions --------------------------------------


// Subscription form (front) ---------------------------------------------------
require_once( plugin_dir_path( __FILE__ ) . 'includes/subscriptionbox.php' );

add_action( 'the_content', 'sss_show_subscription_form' );

// End Subscription form (front) -----------------------------------------------


// Manage event subscriptions (admin) ------------------------------------------
require_once( plugin_dir_path( __FILE__ ) . 'includes/admin.subscriptions.php' );

add_action( 'plugins_loaded', function () {
  SSS_Plugin::get_instance();
} );

// End Manage event subscriptions (admin) --------------------------------------

// Download subscriptions for an event -----------------------------------------
add_action('admin_init','download_subscriptions');
