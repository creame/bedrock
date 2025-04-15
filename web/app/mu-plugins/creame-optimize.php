<?php
/*
Plugin Name:  Creame Optimize
Plugin URI:   https://crea.me/
Description:  Optimizaciones de Creame para mejorar tu <em>site</em>.
Version:      2.2.1
Author:       Creame
Author URI:   https://crea.me/
License:      MIT License
*/


// Set WP_ENV
if (!defined('WP_ENV')) define('WP_ENV', 'production');


/**
 * ============================================================================
 * MARK: WP Admin clean up
 * ============================================================================
 */

// Hide update WordPress message
function creame_hide_update_wordpress_notice() {
    remove_action('admin_notices', 'update_nag', 3);
}
add_action('admin_head', 'creame_hide_update_wordpress_notice', 1);

// Disable Heartbeat API
function creame_stop_heartbeat() {
    wp_deregister_script('heartbeat');
}
// add_action('init', 'creame_stop_heartbeat', 1);

// Or change Heartbeat interval setting
function creame_heartbeat_interval($settings) {
    $settings['interval'] = 90;
    return $settings;
}
add_filter('heartbeat_settings', 'creame_heartbeat_interval');

// Set Creame admin avatar url
function creame_custom_avatar_url($url, $id_or_email, $args) {
    if ($args['force_default'] || false === strpos($url, 'd9b60a057ee91a15dda1fa8b00d0692e')) return $url;

    $user = false;
    if (is_numeric($id_or_email)) $user = get_user_by('id', absint($id_or_email));
    elseif ( $id_or_email instanceof WP_User ) $user = $id_or_email;
    elseif ( $id_or_email instanceof WP_Post ) $user = get_user_by('id', (int)$id_or_email->post_author);
    elseif ( $id_or_email instanceof WP_Comment && !empty($id_or_email->user_id)) $user = get_user_by('id', (int)$id_or_email->user_id);
    else $user = get_user_by('email', $id_or_email);

    return $user && !is_wp_error($user) && preg_match('/(servicios|sistemas)(\+.*)?@crea\.me/i', $user->user_email) ?
        'https://s.gravatar.com/avatar/' . md5('servicios@crea.me') . '?s=' . $args['size'] : $url;
}
add_filter('get_avatar_url', 'creame_custom_avatar_url', 10, 3);

// Add wp-env--environment body class
function creame_body_env_class( $classes ) {
    $wp_env = 'wp-env--' . WP_ENV;
    return is_array($classes) ? array_merge($classes, [$wp_env]) : "$classes $wp_env";
}
add_filter('admin_body_class', 'creame_body_env_class' );
add_filter('login_body_class', 'creame_body_env_class' );
add_filter('body_class', 'creame_body_env_class' );

// Custom admin styles
function creame_custom_admin_styles() {
?>
<style>
  /* Hide */
  #wp-version-message,
  .wcpdf-extensions-ad, /* WooCommerce PDF Invoices & Packing Slips */
  .wrap.woocommerce .informacion, .wrap.woocommerce .cabecera, .wrap.woocommerce h3, /* WC - APG Campo NIF/CIF/NIE */
  div[id^=gainwp-container-]>div:last-child, /* GAinWP */
  #aiwp-container-1>div:last-child, /* Analytics Insights */
  #seiwp-container-1>div:last-child, /* Search Engine Insights */
  #cache-settings .notice-info, /* Cache enabler */
  #e-dashboard-overview .e-overview__feed, /* Elementor dashboard widget */
  .itsec-pro-label /* iThemes Security */
  { display:none !important; }
  /* Bulk edit */
  #bulk-titles, ul.cat-checklist { height:14rem; }
  #wpbody-content .bulk-edit-row fieldset .inline-edit-group label { max-width:none; }
  .inline-edit-row fieldset .timestamp-wrap, .inline-edit-row fieldset label span.input-text-wrap { margin-left:10em; }
  .inline-edit-row fieldset label span.title, .inline-edit-row fieldset.inline-edit-date legend { min-width:9em; width:auto; margin-right:1em; }
  /* Status colors */
  tr.status-draft td, tr.status-draft th { background:rgba(243,238,195,0.5); }
  tr.status-trash td, tr.status-trash th { background:rgba(205,108,118,0.2); }
  .wp-list-table i.eicon-elementor-square { color:#940040; }
</style>
<?php
}
add_action('admin_head', 'creame_custom_admin_styles');

// Disable password change notification to admin
if (!function_exists('wp_password_change_notification')) {
    function wp_password_change_notification() {}
}

// Disable admin email check interval
add_filter('admin_email_check_interval', '__return_zero');

// Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false', PHP_INT_MAX);
add_filter('xmlrpc_methods', '__return_empty_array', PHP_INT_MAX);
add_filter('xmlrpc_element_limit', function (): int { return 1; }, PHP_INT_MAX);

// Disable REST-API
// add_filter('json_enabled', '__return_false');
// add_filter('json_jsonp_enabled', '__return_false');

// Disable post by email
add_filter('enable_post_by_email_configuration', '__return_false');

// Hide other themes on Admin > Appearance
function creame_hide_themes($wp_themes){
    return array_intersect_key($wp_themes, [WP_DEFAULT_THEME => 1]);
}
if (defined('WP_DEFAULT_THEME')) add_filter('wp_prepare_themes_for_js', 'creame_hide_themes');

// Remove language selector on wp-admin login (WP 5.9)
add_filter('login_display_language_dropdown', '__return_false');

// Remove admin menus
add_action('admin_menu', function(){
    remove_menu_page('jet-dashboard'); // JetPlugins
    remove_menu_page('filebird-settings'); // Filebird
}, 100);

// Disable rate/upsell notices
add_filter('pre_option_fbv_review', function(){ return '0'; }); // Filebird
add_filter('pre_option_yaymail_noti_sale', '__return_true'); // Filebird YayMail
add_filter('pre_option_duplicate_post_show_notice', '__return_zero'); // Duplicate Post
add_filter('pre_transient_pwb-notice-delay', '__return_true'); // Perfect Woo Brands
add_filter('jetpack_just_in_time_msgs', '__return_false'); // Jetpack

// object-cache.php disable flush error
add_filter('pecl_memcached/warn_on_flush', '__return_false');

// Clear object cache when empty Cache Enabler
add_action('cache_enabler_complete_cache_cleared', 'wp_cache_flush', 9999);


/**
 * ============================================================================
 * MARK: WP Media
 * ============================================================================
 */

// Filter media library by pdf file type
function creame_post_mime_types($post_mime_types) {
    $post_mime_types['application/pdf'] = [__('PDF'), __('Manage PDF'), _n_noop('PDF <span class="count">(%s)</span>', 'PDF <span class="count">(%s)</span>')];
    return $post_mime_types;
}
add_filter('post_mime_types', 'creame_post_mime_types');

// Attachment unique slugs (prevent post slug conflicts)
function creame_unique_attachment_slug($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug) {
    return 'attachment' == $post_type ? uniqid('media-') : $slug;
}
add_filter('wp_unique_post_slug', 'creame_unique_attachment_slug', 10, 6);

// Auto sanitize attachment title & alt text
function creame_attachment_title($post_ID) {
    $post  = get_post($post_ID);
    $title = preg_replace('%\s*[-_\s]+\s*%', ' ', $post->post_title);
    // $title = ucwords(strtolower($title)); // Title Case

    // Set image alt text
    if (wp_attachment_is_image($post_ID)) update_post_meta($post_ID, '_wp_attachment_image_alt', $title);

    wp_update_post([
        'ID'           => $post_ID,
        'post_title'   => $title, // Set attachment title
        // 'post_excerpt' => $title, // Set attachment caption (Excerpt)
        // 'post_content' => $title, // Set attachment description (Content)
    ]);
}
add_action('add_attachment', 'creame_attachment_title');

// Redirect attachment page to attachment file
add_action('template_redirect', function(){ if (is_attachment()) wp_redirect(wp_get_attachment_url(), 301); });


/**
 * ============================================================================
 * MARK: WP Head clean up
 * ============================================================================
 */

// Clean header
function creame_clean_header() {
    remove_action('wp_head', 'wp_generator');                             // Remove WordPress version
    remove_action('wp_head', 'wlwmanifest_link');                         // Remove wlwmanifest.xml (needed to support windows live writer)
    remove_action('wp_head', 'rsd_link');                                 // Remove really simple discovery link
    remove_action('wp_head', 'feed_links_extra', 3);                      // Remove all extra rss feed links
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);              // Remove shortlink
    remove_action('wp_head', 'wp_oembed_add_discovery_links');            // Remove oembed links
    remove_action('wp_head', 'start_post_rel_link');                      // Remove rel links
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'parent_post_rel_link');
    remove_action('wp_head', 'adjacent_posts_rel_link');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'rest_output_link_wp_head');                 // Remove REST-API link
    remove_action('set_comment_cookies', 'wp_set_comment_cookies');       // Remove commments cookies
    remove_action('template_redirect', 'wp_shortlink_header', 11, 0);     // Remove HTTP headers
    remove_action('template_redirect', 'rest_output_link_header', 11, 0);

    add_filter('the_generator', '__return_false');                        // Removes the generator name from the RSS feeds
    add_filter('use_default_gallery_style', '__return_false');            // Remove gallery inline styles
    add_filter('feed_links_show_comments_feed', '__return_false');        // Remove comments feed link
    add_filter('show_recent_comments_widget_style', '__return_false');    // Remove recent comments widget styles
    add_filter('revslider_meta_generator', '__return_false');             // Remove plugin revslider generator meta
}
add_action('after_setup_theme', 'creame_clean_header');

// Remove emojis
function creame_remove_emojis(){
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    add_filter('emoji_svg_url', '__return_false');
    add_filter('tiny_mce_plugins', function($plugins){ return is_array($plugins) ? array_diff($plugins, ['wpemoji']) : []; });
}
add_action('admin_init', 'creame_remove_emojis');
add_action('init', 'creame_remove_emojis');

// Remove CSS and JS query strings versions
function creame_remove_cssjs_ver_filter($src){
    return is_admin() ? $src : remove_query_arg('ver', $src);
}
add_filter('style_loader_src', 'creame_remove_cssjs_ver_filter');
add_filter('script_loader_src', 'creame_remove_cssjs_ver_filter');

// Remove jQuery migrate
function creame_remove_jquery_migrate($scripts) {
    if (!is_admin()) $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
}
add_action('wp_default_scripts', 'creame_remove_jquery_migrate');

// Move scripts to footer
function creame_move_scripts_to_footer() {
    if (isset($_GET['elementor-preview']) || is_admin_bar_showing()) return;

    remove_action('wp_head', 'wp_print_scripts');
    remove_action('wp_head', 'wp_print_head_scripts', 9);
    remove_action('wp_head', 'wp_enqueue_scripts', 1);

    add_action('wp_footer', 'wp_print_scripts', 5);
    add_action('wp_footer', 'wp_enqueue_scripts', 5);
    add_action('wp_footer', 'wp_print_head_scripts', 5);

    // Add jQuery Shim: script for <header> to capture "jQuery" calls in html body.
    // Require call "shimJQ()" after jQuery is loaded to run captured functions.
    if (wp_script_is('jquery-core')){
        add_action('wp_head', function(){ echo '<script>!function(w,j){var i,f,u=[],o={};w[j]||(i=o.ready=function(w){u.push(w)},f=w[j]=function(w){return"function"==typeof w&&i(w),o},w.shimJQ=function(){for(;u.length;)w[j](u.shift())})}(window,"jQuery");</script>'; });
        wp_add_inline_script('jquery-core', 'window.shimJQ && shimJQ();');
    }
}
add_action('wp_enqueue_scripts', 'creame_move_scripts_to_footer', 11);

// Clean enqueued style and script tags
function creame_clean_style_and_script_tags($tag) {
    $clean = [
        "/type=['\"]text\/(javascript|css)['\"]/" => "",
        "/media=['\"]all['\"]/"                   => "",
        "/ +/"                                    => " ",
        "/ \/?>/"                                 => ">",
    ];
    $tag = preg_replace(array_keys($clean), $clean, $tag);
    // only replace "'" on tag attributes (prevent errors on inline <script> content)
    return preg_replace_callback("/^<[^>]*>/", function($v) { return str_replace("'", '"', $v[0]); }, $tag);
}
add_filter('style_loader_tag', 'creame_clean_style_and_script_tags');
add_filter('script_loader_tag', 'creame_clean_style_and_script_tags');

// Remove hentry class on pages (fix error on google search console)
add_filter('post_class', function($classes){ return is_single() ? $classes : array_diff($classes, ['hentry']); });

// Conditional plugin load.
function creame_active_plugins ($plugins){
    // Heartbeat disable all without heartbeat filters
    if (wp_doing_ajax() && isset($_POST['action']) && 'heartbeat' === $_POST['action']) return array_intersect($plugins, [
        'elementor/elementor.php',
        'ithemes-security-pro/ithemes-security-pro.php',
        'woocommerce/woocommerce.php',
    ]);
    // Admin only plugins
    if (!defined('WP_CLI') && !is_admin()) return array_diff($plugins, [
        'classic-editor/classic-editor.php',
        'duplicate-post/duplicate-post.php',
        // add more project specific plugins
    ]);
    return $plugins;
}
add_filter('option_active_plugins', 'creame_active_plugins', 1);

// SEO no index search results
add_action('wp_head', function(){ if (is_search()) echo '<meta name="robots" content="noindex, follow">' . PHP_EOL; });

// Remove filter capital P dangit
remove_filter('the_title', 'capital_P_dangit', 11);
remove_filter('the_content', 'capital_P_dangit', 11);
remove_filter('comment_text', 'capital_P_dangit', 31);

// Embed YouTube cookieless
function creame_embed_youtube_nocookie($output) {
    return str_replace('https://www.youtube.com/', 'https://www.youtube-nocookie.com/', $output);
}
add_filter('embed_oembed_html', 'creame_embed_youtube_nocookie', 10);

// Remove emded.js (WP < 5.9)
// remove_action('wp_head', 'wp_oembed_add_host_js');

// Remove WP 5.9 global styles if not use Full Site Edit
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_footer', 'wp_enqueue_global_styles', 1);

// Remove WP 5.9 SVG duotuone filters
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
remove_action('in_admin_header', 'wp_global_styles_render_svg_filters');

// Only load blocks assets for current page
add_filter('should_load_separate_core_block_assets', '__return_true');

// Preload featured image for single posts (better page speed if used for hero image)
function creame_preload_featured_image() {
    if (!is_singular()) return;

    $thumbnail_id = get_post_thumbnail_id(get_the_ID());
    if (!$thumbnail_id) return;

    $preload_atts = function($attr) { return array_intersect_key($attr, array_flip(['src', 'srcset', 'sizes'])); };

    add_filter('wp_get_attachment_image_attributes', $preload_atts);
    $image = wp_get_attachment_image($thumbnail_id, 'full'); // 'thumbnail', 'medium', 'medium_large', 'large', 'full'...
    remove_filter('wp_get_attachment_image_attributes', $preload_atts);

    if (!$image) return;

    $image = preg_replace('/<img (width="\d+" )?(height="\d+" )?/', '<link rel="preload" as="image" ', $image);
    echo str_replace([' src=', ' srcset=', ' sizes='], [' href=', ' imagesrcset=', ' imagesizes='], $image);
}
// add_action('wp_head', 'creame_preload_featured_image', 5);


/**
 * ============================================================================
 * MARK: Fonts
 * ============================================================================
 */

add_filter('style_loader_src', 'creame_google_to_bunny_fonts', 100);
add_filter('style_loader_src', 'creame_bunny_fonts_inline', 110, 2);
add_action('wp', 'creame_elementor_remove_google_fonts_preconnect_tag');

// Google Fonts replace with Bunny Fonts (GDPR compliant) & add "font-display:swap"
function creame_google_to_bunny_fonts($src){
    if (false === strpos($src, 'fonts.googleapis.com')) return $src;
    $src = str_replace('fonts.googleapis.com/css', 'fonts.bunny.net/css', $src);
    return false !== strpos($src, 'display=') ? add_query_arg('display', 'swap', $src) : $src;
}

// Inline Bunny Fonts CSS (request from server and store it in transient)
function creame_bunny_fonts_inline($src, $handle){
    if (false === strpos($src, 'fonts.bunny.net')) return $src;

    $key = 'fonts_bunnynet_' . md5($src);
    if (false === $css = get_transient($key)){
        $css = wp_remote_retrieve_body(wp_safe_remote_get($src, ['timeout' => 1]));
        if (empty($css)) return $src;

        $css = preg_replace(['/(\s*)([{|}|:|;|,])(\s+)/', '/\/\*.*\*\//'], ['$2', ''], $css); // Remove spaces & comments
        $css = join("\n", array_filter(explode("\n", $css), function($l){ return str_contains($l, '-latin-'); })); // Only latin chars
        set_transient($key, $css, DAY_IN_SECONDS * 2);
    }
    // Print preconnect link and styles (prevent autoptimize) & return empty src
    echo "\n<link rel=\"preconnect\" href=\"https://fonts.bunny.net/\" crossorigin>";
    echo "\n<!-- noptimize --><style id=\"$handle-inline-css\">\n$css\n</style><!-- /noptimize -->\n";
    return '';
}

// Remove Elementor Google Fonts preconnect tag
function creame_elementor_remove_google_fonts_preconnect_tag(){
    if (class_exists('Elementor\Plugin')) remove_action( 'wp_head', [Elementor\Plugin::instance()->frontend, 'print_google_fonts_preconnect_tag'], 8);
}


/**
 * ============================================================================
 * MARK: Bedrock fixes
 * ============================================================================
 */

// Fix flush rewrite hard is_writable() warning
add_filter('flush_rewrite_rules_hard', '__return_false');

// Fix js/css url https://example.com/app/srv/www/example/releases/20250320142042/web/app/plugins/
function creame_remove_releases_path($src){
    return preg_replace('#(/app/srv/.*/app/)#', '/app/', $src);
}
add_filter('style_loader_src', 'creame_remove_releases_path');
add_filter('script_loader_src', 'creame_remove_releases_path');

// JetEngine fix assets path
add_filter('cx_include_module_url', function($url, $path){
    return plugin_dir_url(preg_replace('/\/releases\/\d+\//', '/current/', $path));
}, 10, 2);

// CMB2 fix assets url
add_filter('cmb2_meta_box_url', function($url){
    return preg_replace('/^.*\/releases\/\d+\/web\//', trailingslashit(WP_HOME), $url);
});

// iThemes Security disable write wp-config.php
add_filter('itsec_filter_can_write_to_files', '__return_false');

// iThemes Security fix it_icon_font_admin_enueue_scripts()
function fix_admin_ithemes_icon_font() {
    if (wp_style_is('ithemes-icon-font')) {
        wp_deregister_style('ithemes-icon-font');
        wp_enqueue_style('ithemes-icon-font', plugin_dir_url('ithemes-security-pro/lib/icon-fonts/.').'icon-fonts.css');
    }
}
add_action('admin_enqueue_scripts', 'fix_admin_ithemes_icon_font', 11);

// Fix remove '/wp/'
define('AUTOPTIMIZE_WP_SITE_URL', WP_HOME); // Autoptimize, for url replace
define('SEIWP_SITE_URL', home_url('/'));    // Search Engine Insights, site url

// Images to WebP set base path
add_filter('itw_abspath', function($path){ return trailingslashit( WP_CONTENT_DIR ); });


/**
 * ============================================================================
 * MARK: WooCommerce
 * ============================================================================
 */

// Disable WooCommerce admin
// add_filter('woocommerce_admin_disabled', '__return_true');

// Disable extension suggestions
add_filter('woocommerce_allow_marketplace_suggestions', '__return_false', 999);

// Disable connect to woocommerce.com notice
add_filter('woocommerce_helper_suppress_admin_notices', '__return_true');

// Disable Woo usage tracking
add_filter('pre_option_woocommerce_allow_tracking', '__return_false');

// Disable Install WooCommerce Update Manager notice
add_filter('get_user_metadata', function($null, $user_id, $meta_key) {
    return 'dismissed_woo_updater_not_installed_notice' === $meta_key ? true : $null;
}, 10, 3);

// Disable extensions menu
add_action('admin_menu', function() { remove_submenu_page('woocommerce', 'wc-addons'); }, 999);

// Sync first/last name between WP <=> Woo users
add_filter('pre_user_first_name', function($first_name) { return $_POST['billing_first_name'] ?? $first_name; });
add_filter('pre_user_last_name', function($last_name) { return $_POST['billing_last_name'] ?? $last_name; });

// Hide customer shipping fields if disabled
function creame_customer_hide_shipping($fields) {
    if ('disabled' === get_option('woocommerce_ship_to_countries') || 'billing_only' === get_option('woocommerce_ship_to_destination')) unset($fields['shipping']);
    return $fields;
}
add_filter('woocommerce_customer_meta_fields', 'creame_customer_hide_shipping');

// Fix prefetch & prerender links
function creame_fix_resource_hints($urls, $relation_type) {
    if (!in_array($relation_type, ['prefetch', 'prerender'], true)) return $urls;
    $abspath = untrailingslashit(get_option('siteurl')).'/wp-includes/';
    foreach ($urls as &$url) $url['href'] = preg_replace('/^\/wp-includes\//', $abspath, $url['href']);
    return $urls;
}
add_filter('wp_resource_hints', 'creame_fix_resource_hints', 20, 2);

// Disable Stripe scripts out of checkout page
add_filter('wc_stripe_load_scripts_on_product_page_when_prbs_disabled', '__return_false');
add_filter('wc_stripe_load_scripts_on_cart_page_when_prbs_disabled', '__return_false');

// Disable ssl checkout on development
if (WP_ENV === 'development') add_filter('pre_option_woocommerce_force_ssl_checkout', '__return_zero');

// Woo Subscriptions in staging mode
add_filter('woocommerce_subscriptions_is_duplicate_site', function($is_duplicate){ return $is_duplicate || WP_ENV !== 'production'; });

// Stripe Gateway in test mode
add_filter('pre_option_fkwcs_mode', function($null){ return WP_ENV === 'production' ? $null : 'test'; });


/**
 * ============================================================================
 * MARK: Elementor
 * ============================================================================
 */

// Elementor dashboard widget disable
add_action('wp_dashboard_setup', function(){ remove_meta_box('e-dashboard-overview', 'dashboard', 'normal'); }, 40);

// Elementor editor reduce widgets size
add_action('elementor/editor/after_enqueue_styles', function(){
    wp_add_inline_style('elementor-editor', '.elementor-panel .elementor-element { display:flex; } .elementor-panel .elementor-element .icon { padding:5px; }');
});

// Elementor disable data tracking
add_filter('pre_option_elementor_allow_tracking', function(){ return 'no'; });

// Elementor font-display https://developers.elementor.com/elementor-pro-2-7-custom-fonts-font-display-support/
add_filter('elementor_pro/custom_fonts/font_display', function(){ return 'swap'; });

// Elementor post status icon
function creame_elementor_post_state_icon($states) {
    if (isset($states['elementor'])) {
        unset($states['elementor']);
        return ['elementor' => '<i class="eicon-elementor-square" title="Elementor"></i>'] + $states;
    }
    return $states;
}
add_filter('display_post_states', 'creame_elementor_post_state_icon', 100);

// Load Elementor assets from CDN
function creame_elementor_assets_from_cdn($assets_url){
    if (!class_exists('BunnyCDN')) return $assets_url;
    $options = BunnyCDN::getOptions();
    return str_replace($options['site_url'], 'https://'.$options['cdn_domain_name'], $assets_url);
}
add_filter('elementor/frontend/assets_url', 'creame_elementor_assets_from_cdn');
add_filter('elementor_pro/frontend/assets_url', 'creame_elementor_assets_from_cdn');

// GA4 Elementor forms submit track event 'form_submit'
function creame_elementor_form_submit_track() {
    if (!wp_script_is('aiwp-tracking-analytics-events')) return;
    $script = <<<SCRIPT
        jQuery(function($){
            $(document).on('submit_success', function (e) {
                window.gtag && gtag('event', 'form_submit', {
                    form_id: e.target.id || '',
                    form_name: e.target.name || '',
                    form_destination: e.target.action || '',
                    form_submit_text: e.target.querySelector('[type=submit]').innerText || '',
                });
            });
        });
        SCRIPT;
    wp_add_inline_script('aiwp-tracking-analytics-events', $script);
}
add_action('wp_head', 'creame_elementor_form_submit_track', 100);
add_action('wp_footer', 'creame_elementor_form_submit_track', 100);


/**
 * ============================================================================
 * MARK: Autoptimize & CDN
 * ============================================================================
 */

// Remove image optimize notice
add_filter('autoptimize_filter_main_imgopt_plug_notice', '__return_empty_string');

// Autoptimice use fonts CDN
add_filter('autoptimize_filter_css_fonts_cdn', '__return_true');

// Fix BunnyCDN priority to apply before Autoptimize
function creame_fix_bunnycdn_with_autoptimize () {
    if (!class_exists('BunnyCDN')) return;
    remove_action('template_redirect', 'doRewrite');
    add_action('template_redirect', 'doRewrite', 1);
}
add_action('init', 'creame_fix_bunnycdn_with_autoptimize', 20);

// Clear BunnyCDN cache, better call by "do_action('cdn_clear');"
function creame_bunnycdn_clear_cache () {
    if (!class_exists('BunnyCDN')) return;
    $options = BunnyCDN::getOptions();
    if (empty($options['api_key'])) return;

    $endpoint = add_query_arg('hostname', urlencode($options['cdn_domain_name']), 'https://bunnycdn.com/api/pullzone/purgeCacheByHostname');
    $headers = ['headers' => ['AccessKey' => trim(htmlspecialchars($options['api_key']))]];
    wp_remote_post($endpoint, $headers);
}
add_action('cdn_clear', 'creame_bunnycdn_clear_cache');
add_action('cache_enabler_complete_cache_cleared', 'creame_bunnycdn_clear_cache'); // On Cache Enabler cleared


/**
 * ============================================================================
 * MARK: The SEO Framework
 * ============================================================================
 */

// Remove The SEO Framework annotations
add_filter('the_seo_framework_indicator', '__return_false');
add_filter('the_seo_framework_indicator_sitemap', '__return_false');

// Fix The SEO Framework auto description "en el"
function creame_fix_generated_description($description) {
    return str_replace(' en el ' . get_bloginfo('name'), ' en ' . get_bloginfo('name'), $description);
}
add_filter('the_seo_framework_generated_description', 'creame_fix_generated_description');

// Add custom rules to robots.txt
function creame_custom_robots_txt($robots) {
    // Set language slugs (if Polylang languages set from directory)
    // $langs = ['es', 'en'];

    $slug     = empty($langs) ? '' : '/*';
    $sitemaps = empty($langs) ? [] : array_map(function($l){ return "Sitemap: " . WP_HOME . "/$l/sitemap.xml\n"; }, $langs);

    return $robots .
        implode('', $sitemaps) .
        "\n" .
        "Allow: /*.js$\n" .
        "Allow: /*.css$\n" .
        "\n" .
        "Disallow: /?s=\n" .
        "Disallow: $slug/?s=\n" .
        "Disallow: $slug/search\n" .
        "\n" .
        "Allow: $slug/feed/$\n" .
        "Disallow: $slug/comments/feed\n" .
        "Disallow: $slug/*/feed/$\n" .
        "Disallow: $slug/*/feed/rss/$\n" .
        "Disallow: $slug/*/*/feed/$\n" .
        "Disallow: $slug/*/*/feed/rss/$\n" .
        "Disallow: $slug/*/*/*/feed/$\n" .
        "Disallow: $slug/*/*/*/feed/rss/$\n";
}
add_filter('robots_txt', 'creame_custom_robots_txt', 11);


/**
 * ============================================================================
 * MARK: Loco Translate
 * ============================================================================
 */

// Add '_loco' to default Loco context
function custom_loco_file_mod_allowed_context($context, $file) {
    return strpos($file->dirname(), 'languages/loco') !== false ? 'download_language_pack_loco' : $context;
}
add_filter('loco_file_mod_allowed_context', 'custom_loco_file_mod_allowed_context', 10, 2);

// If context is 'download_language_pack_loco' always allow modification
function custom_file_mod_allowed($allow, $context) {
    if (wp_doing_ajax()) return 'download_language_pack' === $context ? true : $allow;
    else return 'download_language_pack_loco' === $context ? true : $allow;
}
add_filter('file_mod_allowed', 'custom_file_mod_allowed', 10, 2);


/**
 * ============================================================================
 * MARK: Custom project
 * ============================================================================
 */

// It's your turn...
