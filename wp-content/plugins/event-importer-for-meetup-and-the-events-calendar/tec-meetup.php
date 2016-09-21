<?php
/**
 * Plugin Name: Event Importer for Meetup and The Events Calendar
 * Description: Automatically import events from Meetup.com into The Events Calendar.
 * Version:     0.3.1
 * Author:      dabernathy89
 * Author URI:  https://danielabernathy.com
 * License:     GPLv2
 * Text Domain: tec-meetup
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2016 Daniel Abernathy (email : daniel@danielabernathy.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using generator-plugin-wp
 */


/**
 * Autoloads files with classes when needed
 *
 * @since  0.2.0
 * @param  string $class_name Name of the class being requested.
 * @return void
 */
function tec_meetup_autoload_classes( $class_name ) {
	if ( 0 !== strpos( $class_name, 'TMI_' ) ) {
		return;
	}

	$filename = strtolower( str_replace(
		'_', '-',
		substr( $class_name, strlen( 'TMI_' ) )
	) );

	Tec_Meetup_Importer::include_file( $filename );
}
spl_autoload_register( 'tec_meetup_autoload_classes' );


/**
 * Main initiation class
 *
 * @since  0.2.0
 * @var  string $version  Plugin version
 * @var  string $basename Plugin basename
 * @var  string $url      Plugin URL
 * @var  string $path     Plugin Path
 */
class Tec_Meetup_Importer {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  0.2.0
	 */
	const VERSION = '0.3.1';

	/**
	 * URL of plugin directory
	 *
	 * @var string
	 * @since  0.2.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory
	 *
	 * @var string
	 * @since  0.2.0
	 */
	protected $path = '';

	/**
	 * Plugin basename
	 *
	 * @var string
	 * @since  0.2.0
	 */
	protected $basename = '';

	/**
	 * Plugin slug
	 *
	 * @var string
	 * @since  0.2.0
	 */
	public $slug = 'tec-meetup';

	/**
	 * Singleton instance of plugin
	 *
	 * @var Tec_Meetup_Importer
	 * @since  0.2.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of TMI_Import_Settings
	 *
	 * @since 0.2.0
	 * @var TMI_Import_Settings
	 */
	protected $import_settings;

	/**
	 * Instance of TMI_CPT
	 *
	 * @since 0.2.0
	 * @var TMI_CPT
	 */
	protected $import_cpt;

	/**
	 * Instance of TMI_Add_New_Import
	 *
	 * @since 0.2.0
	 * @var TMI_Add_New_Import
	 */
	protected $add_new_import;

	/**
	 * Instance of TMI_Delete_Import
	 *
	 * @since 0.2.0
	 * @var TMI_Delete_Import
	 */
	protected $delete_import;

	/**
	 * Instance of TMI_Cron
	 *
	 * @since 0.2.0
	 * @var TMI_Cron
	 */
	protected $cron;

	/**
	 * Instance of TMI_Importer
	 *
	 * @since 0.2.0
	 * @var TMI_Importer
	 */
	protected $importer;

	/**
	 * Instance of TMI_Event_Display
	 *
	 * @since 0.3.0
	 * @var TMI_Event_Display
	 */
	protected $event_display;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  0.2.0
	 * @return Tec_Meetup_Importer A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 *
	 * @since  0.2.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function plugin_classes() {
		// Attach other plugin classes to the base plugin class.
		$this->import_settings = new TMI_Import_Settings( $this );
		$this->tec_import_cpt = new TMI_CPT( $this );
		$this->add_new_import = new TMI_Add_New_Import( $this );
		$this->delete_import = new TMI_Delete_Import( $this );
		$this->cron = new TMI_Cron( $this );
		$this->importer = new TMI_Importer( $this );
		$this->event_display = new TMI_Event_Display( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	public function enqueue_admin_css( $hook ) {
	    if ( $hook !== 'tribe_events_page_events-importer' ) {
	        return;
	    }

	    wp_enqueue_style( 'tec_meetup_import_css', $this->url . 'assets/css/tec-meetup.css' );
	}

	/**
	 * Activate the plugin
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function _deactivate() {}

	/**
	 * Init hooks
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function init() {
		if ( $this->check_requirements() ) {
			load_plugin_textdomain( 'tec-meetup', false, dirname( $this->basename ) . '/languages/' );
			$this->plugin_classes();
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_css' ) );
		}
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.2.0
	 * @return boolean result of meets_requirements
	 */
	public function check_requirements() {
		if ( ! $this->meets_requirements() ) {

			// Add a dashboard notice.
			add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

			// Deactivate our plugin.
			add_action( 'admin_init', array( $this, 'deactivate_me' ) );

			return false;
		}

		return true;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function deactivate_me() {
		deactivate_plugins( $this->basename );
	}

	/**
	 * Check that all plugin requirements are met
	 *
	 * @since  0.2.0
	 * @return boolean True if requirements are met.
	 */
	public static function meets_requirements() {
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function requirements_not_met_notice() {
		// Output our error.
		echo '<div id="message" class="error">';
		echo '<p>' . sprintf( __( 'Event Importer for Meetup and The Events Calendar requires The Events Calendar to be installed and activated. The plugin has been <a href="%s">deactivated</a>.', 'tec-meetup' ), admin_url( 'plugins.php' ) ) . '</p>';
		echo '</div>';
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  0.2.0
	 * @param string $field Field to get.
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'import_settings':
			case 'import_cpt':
			case 'add_new_import':
			case 'delete_import':
			case 'cron':
			case 'importer':
			case 'event_display':
				return $this->$field;
			default:
				throw new Exception( 'Invalid '. __CLASS__ .' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory
	 *
	 * @since  0.2.0
	 * @param  string $filename Name of the file to be included.
	 * @return bool   Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( 'includes/class-'. $filename .'.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory
	 *
	 * @since  0.2.0
	 * @param  string $path (optional) appended path.
	 * @return string       Directory and path
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url
	 *
	 * @since  0.2.0
	 * @param  string $path (optional) appended path.
	 * @return string       URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}
}

/**
 * Grab the Tec_Meetup_Importer object and return it.
 * Wrapper for Tec_Meetup_Importer::get_instance()
 *
 * @since  0.2.0
 * @return Tec_Meetup_Importer  Singleton instance of plugin class.
 */
function tec_meetup() {
	return Tec_Meetup_Importer::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( tec_meetup(), 'init' ) );

register_activation_hook( __FILE__, array( tec_meetup(), '_activate' ) );
register_deactivation_hook( __FILE__, array( tec_meetup(), '_deactivate' ) );
