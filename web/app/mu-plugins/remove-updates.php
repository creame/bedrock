<?php
/**
 * Plugin Name: Remove Updates
 * Description: Removes update checks and nags when file modifications are disabled (updates managed with Composer).
 * Author: Creame
 * Author URI: https://crea.me
 * Version: 1.0
 *
 * Based on Altis CMS Update Checks (Human Made).
 *
 * DISALLOW_FILE_MODS already removes the update_* capabilities, the admin bar
 * updates menu and the automatic updates, but WordPress still calls
 * api.wordpress.org to check for updates and shows the core update nag
 * to every user ("Please notify the site administrator").
 *
 * @package Creame
 */

namespace Creame\Remove_Updates;

if ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) {
	remove_updates();
}

/**
 * Bootstrap setup to remove updates from the admin.
 */
function remove_updates() : void {
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\remove_update_checks' );
	add_action( 'admin_init', __NAMESPACE__ . '\\remove_update_nag' );
}

/**
 * Remove the checks that call out to api.wordpress.org to check for updates.
 *
 * This prevents WordPress from running blocking synchronous requests while
 * loading the admin and from scheduling (and running) the update check crons.
 */
function remove_update_checks() : void {
	// Admin page loads.
	remove_action( 'admin_init', '_maybe_update_core' );
	remove_action( 'admin_init', '_maybe_update_plugins' );
	remove_action( 'admin_init', '_maybe_update_themes' );
	remove_action( 'load-plugins.php', 'wp_update_plugins' );
	remove_action( 'load-update.php', 'wp_update_plugins' );
	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	remove_action( 'load-themes.php', 'wp_update_themes' );
	remove_action( 'load-update.php', 'wp_update_themes' );
	remove_action( 'load-update-core.php', 'wp_update_themes' );

	// Cron. Also unhook the events in case they were already scheduled.
	remove_action( 'init', 'wp_schedule_update_checks' );
	remove_action( 'wp_version_check', 'wp_version_check' );
	remove_action( 'wp_update_plugins', 'wp_update_plugins' );
	remove_action( 'wp_update_themes', 'wp_update_themes' );
}

/**
 * Remove the core update nag from the admin header.
 *
 * Shown to all users (not only admins) on single sites.
 */
function remove_update_nag() : void {
	remove_action( 'admin_notices', 'update_nag', 3 );
	remove_action( 'network_admin_notices', 'update_nag', 3 );
}
