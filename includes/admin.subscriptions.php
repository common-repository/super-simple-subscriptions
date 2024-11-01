<?php
// --- ADMIN overview
if ( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Event_Subscriptions extends WP_List_Table {

  /** Class constructor */
  public function __construct() {

    parent::__construct([
      'singular' => __('Event subscription', 'super_simple_subscriptions'),
      //singular name of the listed records
      'plural' => __('Event subscriptions', 'super_simple_subscriptions'),
      //plural name of the listed records
      'ajax' => FALSE
      //should this table support ajax?

    ]);
  }

  /**
   * Retrieve event subscriptions from the database
   *
   * @param int $per_page
   * @param int $page_number
   *
   * @return mixed
   */
  public static function get_subscriptions( $per_page = 5, $page_number = 1 ) {
    global $wpdb;

    // Set table names.
    $table_events = $wpdb->prefix . "posts";
    $table_subscriptions = $wpdb->prefix . "super_simple_subscriptions";

    // Check if we need to show only the subscriptions for an event.
    $where_event = '';
    if (isset($_GET['event'])) {
      $where_event = " WHERE s.event_id = " . wpcf7_sanitize_query_var($_GET['event']);
    }

    // Get event subscriptions (and the event id and name).
    $sql = "
      SELECT s.sss_id, s.firstname, s.lastname, s.email, e.post_title AS event, s.added, s.event_id
      FROM " . $table_subscriptions . " s
      INNER JOIN  " . $table_events . " e ON s.event_id = e.ID
    " . $where_event;

    // Order by.
    if ( ! empty( $_REQUEST['orderby'] ) ) {
      $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
      $sql .= ! empty( $_REQUEST['sss_id'] ) ? ' ' . esc_sql( $_REQUEST['sss_id'] ) : ' ASC';
    }
    else {
      $sql .= " ORDER BY added DESC";
    }

    // Limit per page.
    $sql .= " LIMIT $per_page";

    // Offset.
    $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

    // Get and return results.
    $result = $wpdb->get_results( $sql, 'ARRAY_A' );
    return $result;
  }

  /**
   * Returns the count of records in the database.
   *
   * @return null|string
   */
  public static function record_count() {
    global $wpdb;

    // Set table names.
    $table_events = $wpdb->prefix . "posts";
    $table_subscriptions = $wpdb->prefix . "super_simple_subscriptions";

    $where_event = '';
    if (isset($_GET['event'])) {
      $where_event = " WHERE s.event_id = " . wpcf7_sanitize_query_var($_GET['event']);
    }

    $sql = "
      SELECT COUNT(s.sss_id)
      FROM " . $table_subscriptions . " s
      INNER JOIN  " . $table_events . " e ON s.event_id = e.ID
    " . $where_event;

    return $wpdb->get_var( $sql );
  }

  /** Text displayed when no customer data is available */
  public function no_items() {
    _e('No subscriptions available.', 'super_simple_subscriptions');
  }


  /**
   * Method for name column
   *
   * @param array $item an array of DB data
   *
   * @return string
   */
  function column_firstname( $item ) {

    // create a nonce
    $delete_nonce = wp_create_nonce( 'sss_delete_subscriptions' );

    $title = '<strong> ' . $item['firstname'] . '</strong>';

    $actions = [
      'delete' => sprintf( '<a href="?page=%s&action=%s&sss_id=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['sss_id'] ), $delete_nonce )
    ];

    return $title . $this->row_actions( $actions );
  }

  /**
   * Render a column when no column specific method exists.
   *
   * @param array $item
   * @param string $column_name
   *
   * @return mixed
   */
  public function column_default( $item, $column_name ) {
    switch ( $column_name ) {
      case 'firstname':
      case 'lastname':
      case 'email':
        return $item[$column_name];
        break;
      case 'event':
        return '<a href="admin.php?page=wp_sss&event=' . $item['event_id'] . '">' . $item[$column_name] . '</a>';
        break;
      case 'added':
        return date_i18n( get_option( 'links_updated_date_format' ),  strtotime($item[$column_name]));
        break;
      default:
        return print_r( $item, true ); //Show the whole array for troubleshooting purposes
    }
  }

  /**
   * Render the bulk edit checkbox
   *
   * @param array $item
   *
   * @return string
   */
  function column_cb( $item ) {
    return sprintf(
      '&nbsp;&nbsp;<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['sss_id']
    );
  }

  /**
   * Delete a customer record.
   *
   * @param int $id customer ID
   */
  public static function delete_subscriptions( $id ) {
    global $wpdb;

    // Set filter (to hook into it to cancel or sening a notification email).
    // Return false to cancel for example.
    $subscription_id = apply_filters('super_simple_subscriptions_delete', $id);

    // Check if is numeric.
    if (is_numeric($subscription_id)) {
      // Set table name.
      $table_name = $wpdb->prefix . "super_simple_subscriptions";

      $wpdb->delete(
        $table_name,
        ['sss_id' => $id],
        ['%d']
      );
    }
  }


  /**
   *  Associative array of columns
   *
   * @return array
   */
  function get_columns() {
    $columns = [
      'cb'      => '<input type="checkbox" />',
      'firstname'   => __( 'Firstname', 'super_simple_subscriptions' ),
      'lastname'    => __( 'Lastname', 'super_simple_subscriptions' ),
      'email'       => __( 'Email', 'super_simple_subscriptions' ),
      'event'       => __( 'Event', 'super_simple_subscriptions' ),
      'added'       => __('Added', 'super_simple_subscriptions'),
    ];

    return $columns;
  }

  /**
   * Columns to make sortable.
   *
   * @return array
   */
  public function get_sortable_columns() {
    $sortable_columns = array(
      'firstname' => array( 'firstname', true ),
      'lastname' => array( 'lastname', true ),
      'email' => array( 'email', true ),
      'event' => array('event', true),
      'added' => array('added', true),
    );

    return $sortable_columns;
  }

  /**
   *
   * Returns an associative array containing the bulk action
   *
   * @return array
   */
  public function get_bulk_actions() {
    $actions = [
      'bulk-delete' => 'Delete'
    ];

    return $actions;
  }

  /**
   * Handles data query and filter, sorting, and pagination.
   */
  public function prepare_items() {

    $this->_column_headers = $this->get_column_info();

    /** Process bulk action */
    $this->process_bulk_action();

    $per_page     = $this->get_items_per_page( 'subscriptions_per_page', 25);
    $current_page = $this->get_pagenum();
    $total_items  = self::record_count();

    $this->set_pagination_args( [
      'total_items' => $total_items, //WE have to calculate the total number of items
      'per_page'    => $per_page //WE have to determine how many items to show on a page
    ] );

    $this->items = self::get_subscriptions( $per_page, $current_page );
  }

  public function process_bulk_action() {

    //Detect when a bulk action is being triggered...
    if ( 'delete' === $this->current_action() ) {

      // In our file that handles the request, verify the nonce.
      $nonce = esc_attr( $_REQUEST['_wpnonce'] );

      if ( ! wp_verify_nonce( $nonce, 'sss_delete_subscriptions' ) ) {
        die( 'Go get a life script kiddies' );
      }
      else {
        self::delete_subscriptions( absint( $_GET['sss_id'] ) );

        //wp_redirect( esc_url( add_query_arg() ) );
        //exit;
      }

    }

    // If the delete bulk action is triggered
    if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
      || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
    ) {

      $delete_ids = esc_sql( $_POST['bulk-delete'] );

      // loop over the array of record IDs and delete them
      foreach ( $delete_ids as $id ) {
        self::delete_subscriptions( $id );

      }


      //wp_redirect( esc_url( add_query_arg() ) );
      //exit;
    }
  }

}

class SSS_Plugin {

  // class instance
  static $instance;

  // customer WP_List_Table object
  public $sss_obj;

  // class constructor
  public function __construct() {
    //add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
    add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
  }


  public static function set_screen( $status, $option, $value ) {
    return $value;
  }

  public function plugin_menu() {

    $hook = add_menu_page(
      __('Events Subscriptions', 'super_simple_subscriptions'),
      __('Events Subscriptions', 'super_simple_subscriptions'),
      'manage_options',
      'wp_sss',
      [ $this, 'plugin_settings_page'],
      'dashicons-email',
      '6'
    );
    add_action( "load-$hook", [ $this, 'screen_option' ] );
    add_action( "load-$hook", [ $this, 'add_js' ] );
  }

  /**
   * Add js for delete confirm function.
   */
  public function add_js() {
    wp_enqueue_script( 'super_simple_subscriptions', plugins_url( 'assets/js/sss_delete_confirm.js', SSS_PLUGIN_PATH), array( 'jquery' ), null, true );
  }

  /**
   * Plugin settings page
   */
  public function plugin_settings_page() {
    ?>
    <div class="wrap">
      <h2><?php print __('Events Subscriptions', 'super_simple_subscriptions'); ?></h2>

      <?php
        // Download link if we have subscriptions
        if (isset($_GET['event'])) {
          $current_url = home_url(add_query_arg(array()));
          print '<a href="' . $current_url . '&download=true">' . __('Download subscriptions', 'super_simple_subscriptions') . '</a>';
        }
      ?>

      <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
          <div id="post-body-content">
            <div class="meta-box-sortables ui-sortable">
              <form method="post">
                <?php
                $this->sss_obj->prepare_items();
                $this->sss_obj->display(); ?>
              </form>
            </div>
          </div>
        </div>
        <br class="clear">
      </div>
    </div>
  <?php
  }

  /**
   * Screen options
   */
  public function screen_option() {

    $option = 'per_page';
    $args   = [
      'label'   =>  __('Subscriptions per page', 'super_simple_subsciptions'),
      'default' => 25,
      'option'  => 'customers_per_page'
    ];

    add_screen_option( $option, $args );

    $event_select = __('Event', 'super_simple_subsciptions');
    $args   = [
      'label'   =>  __('Event', 'super_simple_subsciptions'),
      'default' => 25,
      'option'  => 'customers_per_page'
    ];

    add_screen_option( $event_select, $args );

    $this->sss_obj = new Event_Subscriptions();
  }


  /** Singleton instance */
  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new self();
    }

    return self::$instance;
  }
}