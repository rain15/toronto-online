<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Event Importer for Meetup and The Events Calendar Saved Imports Table
 * @version 0.3.1
 * @package Event Importer for Meetup and The Events Calendar
 */

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class TMI_Saved_Imports_Table extends WP_List_Table {
	public function __construct() {
		parent::__construct( array(
			'singular' => 'meetup-import',     // Singular name of the listed records.
			'plural'   => 'meetup-imports',    // Plural name of the listed records.
			'ajax'     => false,       // Does this table support ajax?
		) );
	}

	/**
	 * Return columns for table.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'url'		=> __( 'URL', 'tec-meetup' ),
			'categories'  => __( 'Categories', 'tec-meetup' )
		);
	}

	/**
	 * Specify the columns available on this table
	 *
	 * @return array
	 */
	// public function get_column_info() {
	// 	$this->_column_headers = array( $this->get_columns(), array(), array() );
	// 	$this->_column_headers[] = $this->get_primary_column_name();

	// 	return $this->_column_headers;
	// }

	protected function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Get URL column value.
	 *
	 * @param object $item
	 * @return string
	 */
	protected function column_url( $item ) {
		$page = wp_unslash( $_REQUEST['page'] ); // WPCS: Input var ok.

		// Build delete row action.
		$delete_query_args = array(
			'page'   => $page,
			'action' => 'delete',
			'meetup-import'  => $item['url'],
		);

		$actions['delete'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $delete_query_args ), 'deletemeetupimport' ) ),
			_x( 'Delete', 'List table row action', 'tec-meetup' )
		);

		// Return the title contents.
		return sprintf( '%1$s %2$s',
			$item['url'],
			$this->row_actions( $actions )
		);
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @return void
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = array();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$imports = $this->get_imports();

		$total_items = count( $imports );

		$this->items = $imports;

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => 1,
			'total_pages' => 1,
		) );
	}

	/**
	 * Get the existing imports
	 *
	 * @return array
	 */
	public function get_imports() {
		$formatted_imports = array();

		$raw_imports = get_posts( array(
			'post_type' => 'tec_meetup_import',
			'posts_per_page' => -1
		) );

		// Create an array of the imports formatted to place into the table
		foreach ($raw_imports as $key => $value) {
			$raw_cats = wp_get_post_terms( $value->ID, 'tribe_events_cat' );
			$cats = array();
			$formatted_imports[$value->ID] = array(
				'url' => $value->post_title,
				'categories' => ''
			);

			// Collect the event category names
			if (is_array($raw_cats)) {
				foreach ($raw_cats as $cat) {
					$cats[] = $cat->name;
				}
				$formatted_imports[$value->ID]['categories'] = implode(', ', $cats);
			}
		}

		return $formatted_imports;
	}
}
