<?php
/**
 * Plugin Name: Real GUIDs
 * Description: Generates real GUIDs for your posts, rather than a URL.
 * Author: Creame
 * Author URI: https://crea.me
 * Version: 1.0
 *
 * Based on Altis CMS Update Checks (Human Made).
 *
 * @package Creame
 */

namespace Creame\RealGUIDs;

add_filter( 'kses_allowed_protocols', __NAMESPACE__ . '\\add_urn_protocol' );
add_filter( 'wp_insert_post_data', __NAMESPACE__ . '\\add_uuid_to_new' );

/**
 * Add urn: to allowed protocols.
 *
 * Existing GUIDs are passed through `esc_url_raw` on post update and through
 * `esc_url` in `the_guid()` (feeds), so we need to allow urn or they get stripped.
 *
 * @param array $protocols Protocols array to append to.
 * @return array
 */
function add_urn_protocol( array $protocols ) : array {
	$protocols[] = 'urn';
	return $protocols;
}

/**
 * Add our UUID to new posts.
 *
 * @param array $data Post data to save to the database.
 * @return array
 */
function add_uuid_to_new( array $data ) : array {
	// Set a default GUID.
	if ( empty( $data['guid'] ) ) {
		$data['guid'] = wp_slash( sprintf( 'urn:uuid:%s', wp_generate_uuid4() ) );
	}

	return $data;
}
