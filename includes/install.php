<?php
/**
 * Create the database table to store the subscriptions.
 */
function sss_activate_plugin() {
  global $wpdb;

  // Set table name.
  $table_name = $wpdb->prefix . "super_simple_subscriptions";

  // Set db charset.
  $charset_collate = $wpdb->get_charset_collate();

  // Create schema
  $sql = "CREATE TABLE " . $table_name . "(
    sss_id int(11) NOT NULL AUTO_INCREMENT,
    event_id int(11) NOT NULL,
    email varchar(255) NOT NULL,
    firstname varchar(255) NOT NULL,
    lastname varchar(255) NOT NULL,
    uid int(11) NOT NULL,
    added TIMESTAMP NOT NULL,
    PRIMARY KEY (sss_id),
    KEY event_id_email (event_id,email(191))
  )" . $charset_collate;

  // Create the database table.
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}