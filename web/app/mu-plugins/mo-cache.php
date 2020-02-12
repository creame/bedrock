<?php
/*
Plugin Name: .mo file cache
Version: 0.0.1
Description: Store translations in external object cache for faster loading.
Author: Pacotole
Author URI: https://crea.me
Plugin URI: https://gist.github.com/pacotole/85e422a27bb1635d98e1334cd2d5a634
License: GPL

This plugin is a fork of a_faster_load_textdomain.php by Per Søderlind (https://gist.github.com/soderlind/610a9b24dbf95a678c3e)
*/

function mo_file_cache( $retval, $domain, $mofile ) {
	global $l10n;

	if ( ! is_readable( $mofile ) ) {
		return false;
	}

	if ( ! wp_using_ext_object_cache() ) {
		return false;
	}

	$key   = 'mo__' . md5( $mofile );
	$data  = get_transient( $key );
	$mtime = filemtime( $mofile );

	$mo = new MO();

	if ( ! $data || ! isset( $data['mtime'] ) || $mtime > $data['mtime'] ) {
		if ( ! $mo->import_from_file( $mofile ) ) {
			return false;
		}
		$data = array(
			'mtime'   => $mtime,
			'entries' => $mo->entries,
			'headers' => $mo->headers,
		);
		set_transient( $key, $data );
	} else {
		$mo->entries = $data['entries'];
		$mo->headers = $data['headers'];
	}

	if ( isset( $l10n[ $domain ] ) ) {
		$mo->merge_with( $l10n[ $domain ] );
	}

	$l10n[ $domain ] = &$mo;

	return true;
}

add_filter( 'override_load_textdomain', 'mo_file_cache', 1, 3 );
