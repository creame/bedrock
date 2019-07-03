<?php
/**
 * Configuration overrides for WP_ENV === 'development'
 */

use Roots\WPConfig\Config;

Config::define('SAVEQUERIES', true);
Config::define('WP_DEBUG', true);
Config::define('WP_DEBUG_DISPLAY', true);
Config::define('WP_DISABLE_FATAL_ERROR_HANDLER', true);
Config::define('SCRIPT_DEBUG', true);

ini_set('display_errors', 1);

// Enable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', false);

// Disable cache
Config::define('WP_CACHE', false);

// Disabled publins
Config::define('DISABLED_PLUGINS', serialize([
  'autoptimize/autoptimize.php',
  'cache-enabler/cache-enabler.php',
  'ithemes-security-pro/ithemes-security-pro.php',
  'google-analytics-dashboard-for-wp/gadwp.php'
]));
