<?php
/**
 * WordPress View Bootstrapper
 */
define('ABSPATH', preg_replace('/\/releases\/\d+\//', '/current/', __DIR__) . '/wp/');
define('WP_USE_THEMES', true);
require __DIR__ . '/wp/wp-blog-header.php';
