<?php

/**
 * Tell SearchWP to give extra weight to results based on a date stored as a Custom Field
 * value. The more recent the date the more bonus weight is given. The Custom Field value
 * in the database needs to be UNIX_TIMESTAMP()-compatible (e.g. YYYYMMDD) which Advanced
 * Custom Fields does by default.
 *
 * Customize $date_meta_key to be that of your meta_key and adjust the modifier to your liking.
 */
class MySearchwpMetaDateWeightMods {
	private $date_meta_key = 'display_date'; // Needs to store data as YYYYMMDD (ACF does this already).
	private $modifier      = 1;

	function __construct() {
		add_filter( 'searchwp_query_join',  array( $this, 'searchwp_query_join' ), 10, 2 );
		add_filter( 'searchwp_weight_mods', array( $this, 'searchwp_weight_mods' ) );
	}

	function searchwp_query_join( $sql, $engine ) {
		global $wpdb;

		return $sql . " LEFT JOIN {$wpdb->postmeta} AS searchwpmetadatesort ON {$wpdb->posts}.ID = searchwpmetadatesort.post_id AND searchwpmetadatesort.meta_key = '{$this->date_meta_key}'";
	}

	function searchwp_weight_mods( $sql ) {
		global $wpdb;

		return $sql . " + ( ( UNIX_TIMESTAMP( NOW() ) - ( UNIX_TIMESTAMP( NOW() ) - UNIX_TIMESTAMP( searchwpmetadatesort.meta_value ) ) - (SELECT UNIX_TIMESTAMP( meta_value ) FROM {$wpdb->postmeta} WHERE meta_key = '{$this->date_meta_key}' ORDER BY meta_value ASC LIMIT 1 ) ) / 86400 ) * {$this->modifier}";
	}
}

new MySearchwpMetaDateWeightMods();