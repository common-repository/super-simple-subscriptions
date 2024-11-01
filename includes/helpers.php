<?php

/**
 * Check if we can show subscriptions for an event.
 *
 * @return bool
 */
function sss_subscription_enabled() {

  $post_id = get_the_ID();

  // Check if subscription are enabled.
  $subscription_enable = get_post_meta($post_id, '_sss_event_subscribe', true);
  if (!$subscription_enable) {
    return FALSE;
  }

  // Try to get the event date.
  $event_date = get_post_meta($post_id, 'sse_start_date_alt', true);
  if (empty($event_date)) {
    return FALSE;
  }

  // Get the event time.
  $event_time = get_post_meta($post_id, 'sse_time', true);
  $event_timestamp = strtotime($event_date . ' ' .  $event_time);

  if ($event_timestamp < time()) {
    return FALSE;
  }

  return TRUE;
}

/**
 * Download all subscriptions for an event.
 */
function download_subscriptions() {
  global $wpdb;

  // First do some checks.
  if (isset($_GET['event']) && is_numeric($_GET['event']) && isset($_GET['download']) && $_GET['download']) {

    // Set table name.
    $table_subscriptions = $wpdb->prefix . "super_simple_subscriptions";

    $where_event = '';
    if (isset($_GET['event'])) {
      $where_event = " WHERE s.event_id = " . wpcf7_sanitize_query_var($_GET['event']);
    }

    $subscriptions = $wpdb->get_results("
        SELECT s.firstname, s.lastname, s.email, s.added
        FROM " . $table_subscriptions . " s
      " . $where_event . " ORDER BY s.firstname");

    if (!empty($subscriptions)) {

      // Get the event name, lowercase en prepare title as filename.
      $event_title = sanitize_file_name(strtolower(get_the_title(wpcf7_sanitize_query_var($_GET['event']))));

      $filename = $event_title . "_export_subscription_" . date("Y-m-d") . ".csv";
      $now = gmdate("D, d M Y H:i:s");
      header("Expires: 0");
      header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
      header("Last-Modified: {$now} GMT");

      // force download
      header("Content-Type: application/force-download");
      header("Content-Type: application/octet-stream");
      header("Content-Type: application/download");

      // disposition / encoding on response body
      header("Content-Disposition: attachment;filename={$filename}");
      header("Content-Transfer-Encoding: binary");

      $delimiter = ';';

      $df = fopen("php://output", 'w');
      foreach ($subscriptions as $subscription) {
        fputcsv($df, (array)$subscription, $delimiter);
      }

      fclose($df);
      die;
    }
  }
}