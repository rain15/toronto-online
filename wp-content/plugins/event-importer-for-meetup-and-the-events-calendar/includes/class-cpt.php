<?php
/**
 * Event Importer for Meetup and The Events Calendar Custom Post Type
 *
 * @version 0.3.1
 * @package Event Importer for Meetup and The Events Calendar
 */

class TMI_CPT {
	/**
	 * Parent plugin class
	 *
	 * @var class
	 * @since  0.2.0
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
		// Hook late to ensure that the tribe_events_cat taxonomy is set.
		add_action( 'init', array($this,'register_cpt'), 100 );
	}

	/**
	 * Registers the custom post type for holding imports.
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function register_cpt() {
		$labels = array(
			'name'                  => _x( 'Meetup Imports', 'Post Type General Name', 'tec-meetup' ),
			'singular_name'         => _x( 'Meetup Import', 'Post Type Singular Name', 'tec-meetup' ),
			'menu_name'             => __( 'Meetup Imports', 'tec-meetup' ),
			'name_admin_bar'        => __( 'Meetup Imports', 'tec-meetup' ),
			'archives'              => __( 'Meetup Import Archives', 'tec-meetup' ),
			'parent_item_colon'     => __( 'Parent Meetup Import:', 'tec-meetup' ),
			'all_items'             => __( 'All Meetup Imports', 'tec-meetup' ),
			'add_new_item'          => __( 'Add New Meetup Import', 'tec-meetup' ),
			'add_new'               => __( 'Add New', 'tec-meetup' ),
			'new_item'              => __( 'New Meetup Import', 'tec-meetup' ),
			'edit_item'             => __( 'Edit Meetup Import', 'tec-meetup' ),
			'update_item'           => __( 'Update Meetup Import', 'tec-meetup' ),
			'view_item'             => __( 'View Meetup Import', 'tec-meetup' ),
			'search_items'          => __( 'Search Meetup Import', 'tec-meetup' ),
			'not_found'             => __( 'Not found', 'tec-meetup' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'tec-meetup' ),
			'featured_image'        => __( 'Featured Image', 'tec-meetup' ),
			'set_featured_image'    => __( 'Set featured image', 'tec-meetup' ),
			'remove_featured_image' => __( 'Remove featured image', 'tec-meetup' ),
			'use_featured_image'    => __( 'Use as featured image', 'tec-meetup' ),
			'insert_into_item'      => __( 'Insert into item', 'tec-meetup' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'tec-meetup' ),
			'items_list'            => __( 'Meetup Imports list', 'tec-meetup' ),
			'items_list_navigation' => __( 'Meetup Imports list navigation', 'tec-meetup' ),
			'filter_items_list'     => __( 'Filter items list', 'tec-meetup' ),
		);
		$args = array(
			'label'                 => __( 'Meetup Import', 'tec-meetup' ),
			'labels'                => $labels,
			'supports'              => array( 'title', ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => false,
			'show_in_menu'          => false,
			'menu_position'         => 5,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => false,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'rewrite'               => false,
			'capability_type'       => 'page',
			'taxonomies'			=> array( 'tribe_events_cat' )
		);
		register_post_type( 'tec_meetup_import', $args );
	}
}
