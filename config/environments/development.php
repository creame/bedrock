<?php
/** Development */
define('SAVEQUERIES', true);
define('WP_DEBUG', true);
define('SCRIPT_DEBUG', true);

/** Disabled publins on development */
define('DISABLED_PLUGINS', serialize([
  'autoptimize/autoptimize.php',
  'cache-enabler/cache-enabler.php',
  'ithemes-security-pro/ithemes-security-pro.php',
  'google-analytics-dashboard-for-wp/gadwp.php'
]));
