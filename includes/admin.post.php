<?php

function sss_add_event_metabox($post) {

  add_meta_box(
    'sss-subscribe-meta-box',
    __( 'Enable event subscriptions' ),
    'sss_event_subscribe_meta_box',
    'super-simple-events',
    'normal',
    'default'
  );
}

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function sss_event_subscribe_meta_box( $post ) {
  global $wpdb;

  // Add a nonce field so we can check for it later.
  wp_nonce_field( 'sss_save_meta_box_data', 'sss_meta_box_nonce' );

  $checked = '';

  /*
   * Use get_post_meta() to retrieve an existing value
   * from the database and use the value for the form.
   */
  $value = get_post_meta( $post->ID, '_sss_event_subscribe', true );
  if ($value) {
    $checked = 'checked';
  }

  // Create checkbox to enable/disable subscription for this event.
  echo '<input ' . $checked . ' type="checkbox" id="sss_event_subscribe" name="sss_event_subscription" value="1" /> ';
  echo '<label for="sss_event_subscribe">';
  _e( 'Enable subscriptions for this event', 'super_simple_subscriptions' );
  echo '</label> ';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function sss_save_meta_box_data( $post_id ) {

  // Check if this a valid submit.
  if (wp_verify_nonce($_POST['sss_meta_box_nonce'], 'sss_save_meta_box_data')) {

    // Sanitize user input.
    $enable_subscriptions = 0;
    if (isset($_POST['sss_event_subscription'])) {
      $enable_subscriptions = 1;
    }

    // Update the meta field in the database.
    update_post_meta($post_id, '_sss_event_subscribe', $enable_subscriptions);
  }
}