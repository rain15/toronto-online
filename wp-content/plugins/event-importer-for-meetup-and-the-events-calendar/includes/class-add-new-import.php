<?php
/**
 * Event Importer for Meetup and The Events Calendar Add New Import
 * @version 0.3.1
 * @package Event Importer for Meetup and The Events Calendar
 */

class TMI_Add_New_Import {
	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.2.0
	 */
	protected $plugin = null;

	/**
	 * Error messages to send back as admin notices
	 *
	 * @var   string
	 * @since 0.2.0
	 */
	protected $errors = array();

	/**
	 * Constructor
	 *
	 * @since  0.2.0
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function hooks() {
		if (isset($_POST['action']) && $_POST['action'] === "tec_meetup_add_meetup_recurring_import") {
			// Hook late to ensure that the Events Calendar taxonomy is set up
			add_action( 'init', array( $this, 'handle_form' ), 100 );
			add_action( 'admin_notices', array( $this, 'send_error_messages' ), 100 );
		}
	}

	/**
	 * Process the "new import" form
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function handle_form() {
		$nonce = $_POST['tec-meetup-add-meetup-recurring-import'];
		if (!wp_verify_nonce( $nonce, 'add-meetup-recurring-import' )) {
			return;
		}

		// Grab and sanitize form data
		$url = isset($_POST['tec-meetup-import-group-url']) ? sanitize_text_field($_POST['tec-meetup-import-group-url']) : '';
		$cats = isset($_POST['tec-meetup-import-cats']) ? (array)$_POST['tec-meetup-import-cats'] : array();
		foreach ($cats as $catkey => $catval) {
			$cats[$catkey] = (int)$catval; // cast term IDs to ints
		}

		$url = $this->format_url($url);

		if (empty($url)) {
			$this->errors[] = __( 'Error: Please check the formatting of the Meetup Group URL.', 'tec-meetup' );
			return;
		}

		// URL seems ok, let's create the import.
		$this->create_import($url, $cats);
	}

	/**
	 * Create the import using our CPT
	 *
	 * @since  0.2.0
	 * @param $url Meetup Group URL
	 * @param $cats array of event category term IDs
	 * @return void
	 */
	public function create_import( $url = '', $cats = array() ) {

		$existing_import = get_page_by_title( $url, 'OBJECT', 'tec_meetup_import' );

		if ( !is_null( $existing_import ) ) {
			$this->errors[] = __( 'Error: An import already exists for that Meetup.com group.', 'tec-meetup' );
			return;
		}

		$args = array(
			'post_title' => $url,
			'post_type' => 'tec_meetup_import',
			'post_status' => 'publish'
		);

		if (!empty($cats)) {
			$args['tax_input'] = array(
				'tribe_events_cat' => $cats
			);
		}

		$return_wp_error = true;
		$result = wp_insert_post( $args, $return_wp_error );

		if ( is_wp_error( $result ) ) {
			$this->errors[] = __( 'There was an error creating the import: ', 'tec-meetup' ) . $result->get_error_message();
			return;
		}

		// TODO: set up cron job for this import

		add_action( 'admin_notices', array( $this, 'send_success_message' ), 100 );
	}

	/**
	 * Show an error message if the URL was not formatted correctly.
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function send_error_messages() {
		foreach ($this->errors as $error) :
		    ?>
		    <div class="notice notice-error is-dismissible">
		        <p><?php echo $error; ?></p>
		    </div>
		    <?php
		endforeach;
	}

	/**
	 * Show success message
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function send_success_message() {
	    ?>
	    <div class="notice notice-success is-dismissible">
	        <p><?php _e( 'Successfully created Meetup.com group import.', 'tec-meetup' ) ?></p>
	    </div>
	    <?php
	}

	/**
	 * Make sure the URLs are in a standard format
	 *
	 * @since  0.2.0
	 * @param  $url
	 * @return string
	 */
	public function format_url( $url ) {
		if (strpos($url, 'meetup.com') === false) {
			// they didn't even try
			return '';
		}
		if (strpos($url, "http") !== 0) {
			$url = "http://" . $url;
		}
		if (strpos($url, "www.") === false) {
			// default to using 'www' for consistency
			$url = str_replace("http://", "http://www.", $url);
			$url = str_replace("https://", "https://www.", $url);
		}
		return $url;
	}
}
