<?php
/**
 * Configuration overrides for WP_ENV === 'staging'
 */

use Roots\WPConfig\Config;

/**
 * You should try to keep staging as close to production as possible. However,
 * should you need to, you can always override production configuration values
 * with `Config::define`.
 *
 * Example: `Config::define('WP_DEBUG', true);`
 * Example: `Config::define('DISALLOW_FILE_MODS', false);`
 */

Config::define('DISALLOW_INDEXING', true);

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
