<?php
function sss_show_subscription_form($content) {

  // Show subscriptions only on single pages.
  if (!is_single()) {
    return $content;
  }

  // Check for the correct post type.
  $post = get_post( get_the_ID());
  if ($post->post_type != 'super-simple-events') {
    return $content;
  }

  $show_subcription_form = TRUE;
  $show_subcription_form = apply_filters('super_simple_subscriptions_show_subscription_form', get_the_ID());

  // First check if event subscription is valid.
  if (sss_subscription_enabled() && $show_subcription_form) {

    // Default values.
    $firstname = '';
    $lastname = '';
    $email = '';

    // Check for post data.
    if (!empty($_POST)) {

      // Check nonce field.
      if (isset($_POST['sss_subscription_form']) && wp_verify_nonce($_POST['sss_subscription_form'], 'sss_subscription_form')) {

        $error = array();

        // Post is ok, validate user input.
        // Empty firstname.
        if (empty($_POST['firstname'])) {
          $error[] = __('Please enter your firstname.', 'super_simple_subscriptions');
        }

        // Empty lastname.
        if (empty($_POST['lastname'])) {
          $error[] = __('Please enter your lastname.', 'super_simple_subscriptions');
        }

        // Empty email address
        if (empty($_POST['email'])) {
          $error[] = __('Please enter your email address.', 'super_simple_subscriptions');
        }

        // Validate email address.
        if (empty($_POST['email'])) {
          $error[] = __('Please enter a valid email address.', 'super_simple_subscriptions');
        }

        // Check for errors.
        if (empty($error)) {

          // No errors, insert / delete subscription.
          global $wpdb;

          // Prepare data.
          $firstname = sanitize_text_field($_POST['firstname']);
          $lastname = sanitize_text_field($_POST['lastname']);
          $email = sanitize_email($_POST['email']);
          $post_id = get_the_ID();
          $subscription_type = $_POST['type'];

          // db tablename
          $table_name = $wpdb->prefix . "super_simple_subscriptions";

          // Check if this subscription already exists.
          $existing_subscription = $wpdb->get_row("
            SELECT sss_id
            FROM " . $table_name . "
            WHERE email = '" . $email . "'
            AND event_id = " . $post_id . "
          ");

          // Prepare subscription.
          $subscription = array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'event_id' => $post_id,
            'added' => date('Y-m-d H:i:s', time()),
          );

          // If subscription is selected.
          if ($subscription_type) {

            // Check if subscription already exists.
            if (empty($existing_subscription)) {

              // Create filter before insert.
              $filtered_subscription = apply_filters('super_simple_subscriptions_prepare', $subscription, 'subscribe');

              // Only insert if we have some result.
              if (!empty($filtered_subscription)) {
                $wpdb->insert($table_name, $subscription);
                $msg = __('&#10003; Your subscribted to this event.', 'super_simple_subscriptions');

                // Set action hook (you can hook into it for sending a email notification for example).
                do_action('super_simple_subscriptions_saved', $subscription, 'unsubscriped');
              }

            }
            else {
              // Already subscribed.
              $errors[] =  __('You already subscribed to this event.', 'super_simple_subscriptions');
            }
          }
          else {
            // Unsubscribe selected.
            if (!empty($existing_subscription)) {

              // This is not the real added date so remote it.
              unset($subscription['added']);

              // Create filter before insert.
              $filtered_subscription = apply_filters('super_simple_subscriptions_prepare', $subscription, 'unsubscribe');

              // Check if we have some results.
              if (!empty($filtered_subscription)) {
                // Delete subscription
                $wpdb->delete($table_name, array('sss_id' => $existing_subscription->sss_id));
                $msg = __('&#10003; You are no longer subscribed for this event.', 'super_simple_subscriptions');

                // Set action hook (you can hook into it for sending a email notification for example).
                do_action('super_simple_subscriptions_saved', $subscription, 'unsubscriped');

              }
            }
            else {
              // Already subscribed.
              $errors[] =  __('No subscription found.', 'super_simple_subscriptions');
            }
          }
        }
      }
    }

    // Add css file.
    wp_enqueue_style('super_simple_subscriptions', plugins_url( 'assets/css/screen.css', SSS_PLUGIN_PATH));

    // Start building form subscription box.
    $content .= '<div class="event-subscription-container" id="event-subscription">';

    // Check for errors.
    if (!empty($errors)) {
      $content .= '<div class="error notice"><ul>';
      foreach ($errors as $error) {
        $content .= '<li>' . $error . '</li>';
      }
      $content .= '</ul></div>';
    }
    elseif (isset($msg)) {
      // Show update message.
      $content .= '<div class="updated notice">' . $msg . '</div>';
    }

    // Build subscription form.
    $content .= '<h2>' . __('Subscribe to this event', 'super_simple_subscriptions') . '</h2>';
    $content .= '<form method="POST">';

    // Firstname.
    $content .= '<label class="input">';
    $content .= '<input required type="textfield" name="firstname" placeholder="' . __('Firstname', 'super_simple_subscriptions') . '" value="' . $firstname .'">';
    $content .= '</label>';

    // Lastname.
    $content .= '<label class="input">';
    $content .= '<input required type="textfield" name="lastname" placeholder="' . __('Lastname', 'super_simple_subscriptions') . '" value="' . $lastname .'">';
    $content .= '</label>';

    // Email address.
    $content .= '<label class="input">';
    $content .= '<input required type="email" name="email" placeholder="' . __('Your email address', 'super_simple_subscriptions') . '" value="' . $email .'">';
    $content .= '</label>';

    // Subscribe / unsubscribe.
    $content .= '<label class="input">';
    $content .= '<input type="radio" name="type" value="1" CHECKED />&nbsp;<span>' . __('Subscribe', 'super_simple_subscriptions') . '</span>';
    $content .= '</label>';
    $content .= '<label class="input">';
    $content .= '<input type="radio" name="type" value="0" />&nbsp;<span>' . __('Unsubscribe', 'super_simple_subscriptions') . '</span>';
    $content .= '</label>';

    // Create nonce field.
    $content .= '<input type="hidden" name="sss_subscription_form" value="' . wp_create_nonce('sss_subscription_form') . '">';

    // Submit button.
    $content .= '<input type="submit" name="nam" value="' . __('Submit', 'super_simple_subscriptions'). '">';
    $content .= '</form>';
    $content .= '</div>';

    // Add filter to change this form.
    $content = apply_filters('super_simple_subscriptions_form', $content);

  }
  return $content;
}