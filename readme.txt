=== Plugin Name ===
Contributors: marcel-nl
Donate link: http://webbouwplus.nl
Tags: events, events subscriptions
Requires at least: 3.8
Tested up to: 4.4.2
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extension for Super Simple Events (https://wordpress.org/plugins/super-simple-events/) to subscribe to events.

== Description ==

Extension for Super Simple Events (https://nl.wordpress.org/plugins/super-simple-events/) to subscribe to events.

- Enable/disable subscriptions for an event
- Subscribe or unsubscribe to an event
- View and delete all subscriptions (in the admin section)
- View / delete subscriptions for each event (in the admin section)
- Download event subscriptions

= How to use =
Just install the plugin. (see installation section).
Create or edit an event. Enable the checkbox 'Enable subscriptions for this event' to show the event subscription form.

To view all the subscriptions, go to www.your-site.com/wp-admin/admin.php?page=wp_sss
Wanted to view the subscriptions for one event? click on the event title on that page.

= Download event subscriptions =
Go to the admin event subscriptions overview (www.your-site.com/wp-admin/admin.php?page=wp_sss)
Click on an event title in the table. On the next page you see a 'download subscriptions' link below the page title.

= Actions and filters =

- Filter before inserting/remove a subscription
- Action after a subscription is saved or deleted
- Filter before subscription is deleted (admin section)
- Filter before showing the subscription form
- Filter to change the subscription form

`function test_filter($subscription, $type) {
  var_dump('Prepare subscription');
  return $subscription;
}
add_filter('super_simple_subscriptions_prepare', 'test_filter', 10, 2);`

`function test_action($subscription, $type) {
  var_dump('Action after subscription is saved or deleted');
}
add_action('super_simple_subscriptions_saved', 'test_action', 10, 2);`

`function test_delete($subscription_id) {
  var_dump('Delete subscription');
  return $subscription_id;
}
add_filter('super_simple_subscriptions_delete', 'test_delete');`

`function test_show_subscription_form($post_id) {
  var_dump('Do some custom checks and return true or false');
  return TRUE;
}
add_filter('super_simple_subscriptions_show_subscription_form', 'test_show_subscription_form');`

`function test_super_simple_subscriptions_form($content) {
  var_dump('Change the subscription form');
  return $content;
}
add_filter('super_simple_subscriptions_form', 'test_super_simple_subscriptions_form');`

= Possible feature updates =
- Limit subscriptions for an event
- Email confirmation
- Subscriptions only for members (or certain roles)

== Frequently Asked Questions ==

= I miss a functionality =
Contact me :)

== Installation ==

This section describes how to install the plugin and get it working.

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'super_simple_subscriptions'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `super_simple_subscriptions.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `super-simple-subscriptions.zip`
2. Extract the `super_simple_subscriptions` directory to your computer
3. Upload the `super_simple_subscriptions` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Screenshots ==

1. Enable or disable subscriptions for an event.
2. Subscribe or unsubscribe for an event.
3. View/delete subscriptions (admin section).

== Changelog ==

= 1.1.0 =
* Add option to download subscriptions for an event to a csv file.

= 1.0.0 =
* First Release

== Upgrade Notice ==

= 1.1.0 =
* Add option to download subscriptions for an event to a csv file.

= 1.0 =
No upgrade yet, this is the first release.
