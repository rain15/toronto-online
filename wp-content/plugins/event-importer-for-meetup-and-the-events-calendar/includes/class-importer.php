<?php
/**
 * Event Importer for Meetup and The Events Calendar Importer
 * @version 0.3.1
 * @package Event Importer for Meetup and The Events Calendar
 */

class TMI_Importer {
	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.2.0
	 */
	protected $plugin = null;

	/**
	 * Array of country codes to country names
	 *
	 * @var   array
	 * @since 0.2.0
	 */
	protected $countries = array();

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
	 * Do the scheduled import for a Meetup group.
	 *
	 * @since  0.2.0
	 * @param  $post_id
	 * @return void
	 */
	public function do_import( $post_id = null ) {
		$post = get_post( $post_id );
		$api_key = Tribe__Events__Importer__Options::getOption('meetup_api_key');

		if ( !$post || !$api_key ) {
			return;
		}

		if ( empty($this->countries) ) {
			$this->countries = Tribe__View_Helpers::constructCountries( '', false );
		}

		$group_slug = $this->get_group_slug_from_url( $post->post_title );

		$response = wp_remote_get(
			'https://api.meetup.com/' . $group_slug . '/events/?key=' . $api_key,
			array(
				'headers' => array(
					'Content-Type' => 'application/json'
				)
			)
		);

		if ( !is_wp_error( $response ) ) {
			$events = json_decode( $response['body'], true );
			if ( is_array( $events ) ) {
				foreach ( $events as $event ) {
					$this->create_event( $event, $group_slug, $post_id );
				}
			}
		}

		// TODO: allow import of more than 200 events by following link headers sent by the API.
	}

	/**
	 * Create or update a The Events Calendar event from a Meetup.com event.
	 *
	 * @since  0.2.0
	 * @param  $event
	 * @return void
	 */
	public function create_event( $event = array(), $group_slug = '', $post_id = null ) {
		if ( $event && is_array( $event ) && array_key_exists('id', $event) ) {

			$args = $this->build_args_for_event( $event, $group_slug );

			$existing_event_id = $this->get_event_by_meetup_id( $event['id'] );

			if ( $existing_event_id ) {
				tribe_update_event( $existing_event_id, $args );
				do_action( 'tec_meetup_event_updated', $existing_event_id, $args );
			} else {
				$this->create_new_event( $event, $args, $post_id );
			}
		}
	}

	/**
	 * Create a new event.
	 *
	 * @since  0.2.2
	 * @param  $meetup_event_id
	 * @return int|false
	 */
	public function create_new_event( $event = array(), $args = array(), $post_id = null ) {
		$new_event_id = tribe_create_event( $args );
		if ( $new_event_id ) {
			update_post_meta( $new_event_id, '_tec_meetup_import_event_id', $event['id'] );
			update_post_meta( $new_event_id, '_tec_meetup_import_event_link', $event['link'] );
			update_post_meta( $new_event_id, '_tec_meetup_import_event_raw_data', json_encode( $event ) );

			// Associate the event categories from the import to this new event.
			$event_cats = wp_get_post_terms( $post_id, 'tribe_events_cat' );
			if ( !is_wp_error( $event_cats ) ) {
				$term_ids = array();
				foreach ( $event_cats as $event_cat ) {
					$term_ids[] = $event_cat->term_id;
				}

				if ( !empty( $term_ids ) ) {
					wp_set_object_terms( $new_event_id, $term_ids, 'tribe_events_cat' );
				}
			}

			do_action( 'tec_meetup_new_event_imported', $new_event_id, $args, $event );
		}
	}

	/**
	 * Retrieve an event by the Meetup event ID
	 *
	 * @since  0.2.0
	 * @param  $meetup_event_id
	 * @return int|false
	 */
	protected function get_event_by_meetup_id( $meetup_event_id ) {
		$args = array(
			'posts_per_page' => -1,
			'post_type' => 'tribe_events',
			'meta_key' => '_tec_meetup_import_event_id',
			'meta_value' => $meetup_event_id
		);

		$events = new WP_Query( $args );

		if ( $events->have_posts() ) : while ( $events->have_posts() ) : $events->the_post();
			return get_the_ID();
		endwhile; endif;
		wp_reset_postdata();

		return false;
	}

	/**
	 * Build the array args for the tribe_create_event and tribe_update_event functions
	 *
	 * @since  0.2.0
	 * @param  $event
	 * @param  $group_slug
	 * @return array
	 */
	protected function build_args_for_event( $event = array(), $group_slug = '' ) {
		$event_status = Tribe__Events__Importer__Options::get_default_post_status( 'tec-meetup' );
		$event_status = apply_filters( 'tec_meetup_import_event_status', $event_status, $group_slug );

		if ( array_key_exists( 'time', $event ) ) {
			$event_timestamp = floor( $event['time'] / 1000 );
		} else {
			$event_timestamp = time();
		}
		$event_time = gmdate( 'Y-m-d H:i:s', $event_timestamp );

		$event_duration = array_key_exists( 'duration', $event ) ? $event['duration'] : 10800000;
		$event_duration = absint( floor( $event_duration / 1000 ) ); // convert to seconds
		$event_end_timestamp = $event_timestamp + $event_duration;
		$event_end_time = gmdate( 'Y-m-d H:i:s', $event_end_timestamp );

		$date_format = Tribe__Date_Utils::datepicker_formats( tribe_get_option( 'datepickerFormat' ) );
		if ( !$date_format ) {
			$date_format = 'Y-m-d';
		}

		$args = array(
			'post_title' => array_key_exists( 'name', $event ) ? $event['name'] : '',
			'post_content' => array_key_exists( 'description', $event ) ? $event['description'] : '',
			'post_status' => $event_status,
			'EventStartDate' => get_date_from_gmt($event_time, $date_format),
			'EventEndDate' => get_date_from_gmt($event_end_time, $date_format),
			'EventStartHour' => get_date_from_gmt($event_time, 'h'),
			'EventStartMinute' => get_date_from_gmt($event_time, 'i'),
			'EventStartMeridian' => get_date_from_gmt($event_time, 'a'),
			'EventEndHour' => get_date_from_gmt($event_end_time, 'h'),
			'EventEndMinute' => get_date_from_gmt($event_end_time, 'i'),
			'EventEndMeridian' => get_date_from_gmt($event_end_time, 'a'),
		);

		$venue_args = $this->get_venue_args($event);

		if ( $venue_args ) {
			$args['Venue'] = $venue_args;
		}

		return $args;
	}

	/**
	 * Get the args for creating a new venue or using an existing one.
	 *
	 * @since  0.2.0
	 * @param  $event
	 * @return void
	 */
	public function get_venue_args( $event = array() ) {
		if ( !array_key_exists( 'venue', $event ) ) {
			return null;
		}

		$venue = $event['venue'];

		$existing_venues = get_posts( array(
			'posts_per_page' => 1,
			'post_type' => 'tribe_venue',
			'meta_key' => '_tec_meetup_import_venue_id',
			'meta_value' => $event['venue']['id']
		) );

		if ( is_array( $existing_venues ) && !empty( $existing_venues ) ) {
			return array(
				'VenueID' => $existing_venues[0]->ID
			);
		}

		$new_venue = tribe_create_venue( array(
			'Venue' => $venue['name'],
			'Country' => $this->countries[strtoupper($venue['country'])],
			'Address' => $venue['address_1'],
			'City' => $venue['city'],
			'State' => $venue['state'],
			'Zip' => $venue['zip'],
		) );

		if ( $new_venue ) {
			update_post_meta( $new_venue, '_tec_meetup_import_venue_id', $venue['id'] );
			return array(
				'VenueID' => $new_venue
			);
		}

		return null;
	}

	/**
	 * Given a meetup.com URL, return the group slug
	 *
	 * @since  0.2.0
	 * @param  $post_id
	 * @return void
	 */
	public function get_group_slug_from_url( $url = '' ) {
		// Get rid of everything up to and including 'meetup.com/'
		$url = str_replace( 'https://', '', $url );
		$url = str_replace( 'http://', '', $url );
		$url = $this->str_replace_first( 'www.', '', $url );
		$url = $this->str_replace_first( 'meetup.com/', '', $url );

		// Grab everything up to the first slash.
		$first_slash_pos = strpos( $url, '/' );
		if ($first_slash_pos !== false ) {
			$url = substr($url, 0, $first_slash_pos);
		}

		return $url;
	}

	/**
	 * Helper function to replace the first occurrence of a string.
	 *
	 * @since  0.2.0
	 * @param  $needle
	 * @param  $replace
	 * @param  $haystack
	 * @return string
	 */
	public function str_replace_first( $needle = '', $replace = '', $haystack = '' ) {
		$result = $haystack;
		$position = strpos( $haystack, $needle );

		if ( $position !== false ) {
		    $result = substr_replace( $haystack, $replace, $position, strlen( $needle ) );
		}

		return $result;
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.2.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'tec_meetup_do_import', array( $this, 'do_import') );
	}
}
