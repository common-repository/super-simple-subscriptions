<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Super_Simple_Subscriptions
 * @author    Marcel van Doornen <marcel@webbouwplus.nl>
 * @license   GPL-2.0+
 * @link      http://www.webbouwplus.nl
 * @copyright 2016  Marcel-NL
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

flush_rewrite_rules();
