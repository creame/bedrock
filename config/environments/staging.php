<?php
/**
 * Configuration overrides for WP_ENV === 'staging'
 */

use Roots\WPConfig\Config;

// Disable cache
Config::define('WP_CACHE', false);

// Disabled publins
Config::define('DISABLED_PLUGINS', serialize([
  'autoptimize/autoptimize.php',
  'bunnycdn/bunnycdn.php',
  'cache-enabler/cache-enabler.php',
  'ithemes-security-pro/ithemes-security-pro.php',
  'ga-in/gainwp.php',
]));
