<?php
/**
 * Event Importer for Meetup and The Events Calendar Import Settings
 * @version 0.3.1
 * @package Event Importer for Meetup and The Events Calendar
 */

class TMI_Import_Settings {
	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.2.0
	 */
	protected $plugin = null;

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
		add_filter( 'tribe_import_tabs', array( $this, 'add_meetup_import_tab' ) );
		add_action( 'tribe_import_render_tab_' . $this->plugin->slug, array( $this, 'display_tab_content' ) );
		add_action( 'tribe_import_general_settings', array( $this, 'add_import_settings_fields' ), 30 );
		add_filter( 'tribe_import_available_options', array( $this, 'add_to_available_import_options') );
		add_filter( 'tribe_field_output_text', array( $this, 'add_placeholder_to_text_field'), 20, 3 );
	}

	/**
	 * Allow TEC to save our options on the Import Settings screen
	 *
	 * @since  0.2.0
	 * @return array
	 */
	public function add_to_available_import_options( $options = array() ) {
		$options[] = 'meetup_api_key';
		$options[] = 'meetup_link_text';
		return $options;
	}

	/**
	 * Register tab on The Events Calendar import page
	 *
	 * @since  0.2.0
	 * @return array
	 */
	public function add_meetup_import_tab( $tabs = array() ) {
		$tabs[ __( 'Meetup.com', 'tec-meetup' ) ] = $this->plugin->slug;
		return $tabs;
	}

	/**
	 * Display content for import tab
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function display_tab_content() {
		$event_cats = get_terms( 'tribe_events_cat', array(
			'hide_empty' => 0
		) );
		include( $this->plugin->path . 'includes/views/tec-meetup-tab-content.php' );
	}

	/**
	 * Display content for import tab
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function add_import_settings_fields( $fields = array() ) {
		$meetup_import_settings_fields = array(
			'tec-meetup-title' => array(
				'type' => 'html',
				'html' => '<h3>' . esc_html__( 'Meetup.com Import Settings', 'tec-meetup' ) . '</h3>',
			),
			'tec-meetup-form-content-start' => array(
				'type' => 'html',
				'html' => '<div class="tribe-settings-form-wrap">',
			),
			'imported_post_status[tec-meetup]' => array(
				'type' => 'dropdown',
				'label' => __( 'Default status to use for imported events', 'tec-meetup' ),
				'options' => Tribe__Events__Importer__Options::get_possible_stati(),
				'validation_type' => 'options',
				'parent_option' => Tribe__Events__Main::OPTIONNAME,
				'importer_type' => 'meetup',
			),
			'meetup_api_key' => array(
				'type' => 'text',
				'label' => __( 'Meetup.com API Key', 'tec-meetup' ),
				'validation_type' => 'textarea',
				'parent_option' => Tribe__Events__Main::OPTIONNAME,
				'importer_type' => 'meetup',
			),
			'meetup_link_text' => array(
				'type' => 'text',
				'label' => __( 'Meetup.com event link text', 'tec-meetup' ),
				'tooltip' => __( 'Leave blank if you do not want to display links to Meetup.com on events', 'tec-meetup' ),
				'validation_type' => 'textarea',
				'parent_option' => Tribe__Events__Main::OPTIONNAME,
				'importer_type' => 'meetup',
			),
			'tec-meetup-form-content-end' => array(
				'type' => 'html',
				'html' => '</div>',
			),
		);

		return array_merge( $fields, $meetup_import_settings_fields );
	}

	/**
	 * Add option to display links to the Meetup.com page for each event
	 *
	 * @since  0.3.0
	 * @return string
	 */
	public function add_placeholder_to_text_field( $field, $id, $foo ) {
		if ( $id === 'meetup_link_text' ) {
			$name_field = 'name="meetup_link_text"';
			$name_attr_pos = strpos( $field, $name_field );
			$field = substr_replace(
				$field,
				' placeholder="' . __( 'Ex: View on Meetup.com', 'tec-meetup' ) . '"',
				$name_attr_pos + strlen($name_field),
				0
			);
		}

		return $field;
	}
}
