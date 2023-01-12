<?php
/*
Plugin Name:  Creame Optimize
Plugin URI:   https://crea.me/
Description:  Optimizaciones de Creame para mejorar tu <em>site</em>.
Version:      2.0.4
Author:       Creame
Author URI:   https://crea.me/
License:      MIT License
*/


/**
 * ============================================================================
 * WP Admin clean up
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
    $settings['interval'] = 60;
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
    $class = defined('WP_ENV') ? 'wp-env--' . WP_ENV : 'wp-env--none';
    return is_array($classes) ? array_merge($classes, [$class]) : "$classes $class";
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

// Disable rate notices
add_filter('pre_option_fbv_review', function(){ return '0'; }); // Filebird
add_filter('pre_option_yaymail_noti_sale', '__return_true'); // Filebird YayMail
add_filter('pre_option_duplicate_post_show_notice', '__return_zero'); // Duplicate Post
add_filter('pre_transient_pwb-notice-delay', '__return_true'); // Perfect Woo Brands

// object-cache.php disable flush error
add_filter('pecl_memcached/warn_on_flush', '__return_false');

// Clear object cache when empty Cache Enabler
add_action('cache_enabler_complete_cache_cleared', 'wp_cache_flush', 9999);


/**
 * ============================================================================
 * WP Media
 * ============================================================================
 */

// Sort media library by pdf file type
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
function creame_attachment_redirect(){
    if (is_attachment()) wp_redirect(wp_get_attachment_url(), 301);
}
add_action('template_redirect', 'creame_attachment_redirect');


/**
 * ============================================================================
 * WP Head clean up
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

// Remove CSS and JS query strings versions
function creame_remove_cssjs_ver_filter($src){
    return is_admin() ? $src : remove_query_arg('ver', $src);
}
add_filter('style_loader_src', 'creame_remove_cssjs_ver_filter');
add_filter('script_loader_src', 'creame_remove_cssjs_ver_filter');

// Google Fonts replace with Bunny Fonts (GDPR compliant) & add "font-display:swap"
function creame_google_fonts($src){
    if (false === strpos($src, 'fonts.googleapis.com')) return $src;
    $src = str_replace('fonts.googleapis.com/css', 'fonts.bunny.net/css', $src);
    return false !== strpos($src, 'display=') ? add_query_arg('display', 'swap', $src) : $src;
}
add_filter('style_loader_src', 'creame_google_fonts', 100);

// Remove jQuery migrate
function creame_remove_jquery_migrate($scripts) {
    if (!is_admin()) $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
}
add_action('wp_default_scripts', 'creame_remove_jquery_migrate');

// jQuery Shim: script for <header> to capture "jQuery" calls in html body.
// Require call "shimJQ()" after jQuery is loaded to run captured functions.
function creame_jquery_shim() {
    echo '<script>!function(w,j){var i,f,u=[],o={};w[j]||(i=o.ready=function(w){u.push(w)},f=w[j]=function(w){return"function"==typeof w&&i(w),o},w.shimJQ=function(){for(;u.length;)w[j](u.shift())})}(window,"jQuery");</script>';
}

// Move scripts to footer
function creame_move_scripts_to_footer() {
    if (isset($_GET['elementor-preview'])) return;

    remove_action('wp_head', 'wp_print_scripts');
    remove_action('wp_head', 'wp_print_head_scripts', 9);
    remove_action('wp_head', 'wp_enqueue_scripts', 1);

    add_action('wp_footer', 'wp_print_scripts', 5);
    add_action('wp_footer', 'wp_enqueue_scripts', 5);
    add_action('wp_footer', 'wp_print_head_scripts', 5);

    // Add jQuery Shim
    if (wp_script_is('jquery-core')){
        add_action('wp_head', 'creame_jquery_shim');
        wp_add_inline_script('jquery-core', 'shimJQ()');
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
function creame_remove_hentry_class($classes) {
    return is_single() ? $classes : array_diff($classes, ['hentry']);
}
add_filter('post_class', 'creame_remove_hentry_class');

// Conditional plugin load. Exclude in front plugins for admin only
function creame_remove_only_admin_plugins ($plugins){
    return array_diff($plugins, [
        'classic-editor/classic-editor.php',
        'filebird/filebird.php',
        // 'duplicate-post/duplicate-post.php',
        // add more project specific plugins
    ]);
}
if (!defined('WP_CLI') && !is_admin()) add_filter('option_active_plugins', 'creame_remove_only_admin_plugins', 1);

// SEO no index search results
function creame_noindex_search() {
    if (is_search()) echo '<meta name="robots" content="noindex, follow">' . PHP_EOL;
}
add_action('wp_head', 'creame_noindex_search');

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

// Clean attrs for preload featured image
function creame_preload_featured_image_atts($attr) {
    return array_intersect_key($attr, array_flip(['src', 'srcset', 'sizes']));
}

// Preload featured image for single posts
function creame_preload_featured_image() {
    if (!is_singular()) return;

    $thumbnail_id = get_post_thumbnail_id(get_the_ID());
    if (!$thumbnail_id) return;

    add_filter('wp_get_attachment_image_attributes', 'creame_preload_featured_image_atts');
    $image = wp_get_attachment_image($thumbnail_id, 'full'); // 'thumbnail', 'medium', 'medium_large', 'large', 'full'...
    remove_filter('wp_get_attachment_image_attributes', 'creame_preload_featured_image_atts');

    if (!$image) return;

    $image = preg_replace('/<img (width="\d+" )?(height="\d+" )?/', '<link rel="preload" as="image" ', $image);
    echo str_replace([' src=', ' srcset=', ' sizes='], [' href=', ' imagesrcset=', ' imagesizes='], $image);
}
// add_action('wp_head', 'creame_preload_featured_image', 5);


/**
 * ============================================================================
 * Bedrock fixes
 * ============================================================================
 */

// Fix flush rewrite hard is_writable() warning
add_filter('flush_rewrite_rules_hard', '__return_false');

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


/**
 * ============================================================================
 * WooCommerce
 * ============================================================================
 */

// Disable WooCommerce admin
// add_filter('woocommerce_admin_disabled', '__return_true');

// Disable extension suggestions
add_filter('woocommerce_allow_marketplace_suggestions', '__return_false', 999);

// Disable connect to woocommerce.com notice
add_filter('woocommerce_helper_suppress_admin_notices', '__return_true');

// Disable extensions menu
function creame_remove_admin_addon_submenu() {
    remove_submenu_page('woocommerce', 'wc-addons');
}
add_action('admin_menu', 'creame_remove_admin_addon_submenu', 999);

// Sync first name between WP <=> Woo users
function creame_sync_first_name_wp_woo($first_name) {
    return $_POST['billing_first_name'] ?? $first_name;
}
add_filter('pre_user_first_name', 'creame_sync_first_name_wp_woo');

// Sync last name between WP <=> Woo users
function creame_sync_last_name_wp_woo($last_name) {
    return $_POST['billing_last_name'] ?? $last_name;
}
add_filter('pre_user_last_name', 'creame_sync_last_name_wp_woo');

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


/**
 * ============================================================================
 * Elementor
 * ============================================================================
 */

// Elementor dashboard widget disable
function creame_disable_elementor_dashboard_overview_widget() {
    remove_meta_box('e-dashboard-overview', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'creame_disable_elementor_dashboard_overview_widget', 40);

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


/**
 * ============================================================================
 * Autoptimize
 * ============================================================================
 */

// Remove image optimize notice
add_filter('autoptimize_filter_main_imgopt_plug_notice', '__return_empty_string');

// Autoptimice use fonts CDN
add_filter('autoptimize_filter_css_fonts_cdn', '__return_true');

// Fix BunnyCDN priority to apply before Autoptimize
function creame_fix_bunnycdn_with_autoptimize () {
    if ( class_exists('BunnyCDN') ) {
        remove_action('template_redirect', 'doRewrite');
        add_action('template_redirect', 'doRewrite', 1);
    }
}
add_action('init', 'creame_fix_bunnycdn_with_autoptimize', 20);


/**
 * ============================================================================
 * The SEO Framework
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
 * Loco Translate, always allow file modification on Custom location
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
 * Google Analytics selfhosted (avoid "leverage browser caching")
 * ============================================================================
 */

// Update ga script (hooked on 'delete_expired_transients' daily CRON)
function creame_ga_update_script() {
    $cache_path = WP_CONTENT_DIR . '/cache/ga';

    if (wp_mkdir_p($cache_path)) {
        $request = wp_safe_remote_get('https://www.google-analytics.com/analytics.js', ['timeout' => 10]);

        if (!is_wp_error($request)) {
            $ga_lm = strtotime(wp_remote_retrieve_header($request, 'last-modified'));

            if ($ga_lm !== get_option('ga_last_modified')) {
                file_put_contents("$cache_path/$ga_lm.js" , wp_remote_retrieve_body($request));
                update_option('ga_last_modified', $ga_lm);
                do_action('ce_clear_cache'); // "Cache Enabler" clear caches
            }
        }
    }
}

// Replace ga script with selfhosted (for GAinWP)
function creame_ga_selfhosted_script($src) {
    $ga_lm = get_option('ga_last_modified');
    return $ga_lm ? content_url("/cache/ga/$ga_lm.js") : $src;
}

if (defined('WP_ENV') && WP_ENV === 'production') {
    add_action('delete_expired_transients', 'creame_ga_update_script');
    add_filter('gainwp_analytics_script_path', 'creame_ga_selfhosted_script');
    add_filter('aiwp_analytics_script_path', 'creame_ga_selfhosted_script');
}

// GA4 Elementor forms submit track event 'form_submit'
function creame_elementor_form_submit_track() {
    if (!wp_script_is('aiwp-tracking-analytics-events')) return;
    ob_start(); ?>
<script>
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
</script>
    <?php
    $script = str_replace(array('<script>', '</script>'), '', ob_get_clean());
    wp_add_inline_script('aiwp-tracking-analytics-events', $script, 'after');
}
add_action('wp_head', 'creame_elementor_form_submit_track', 100);
add_action('wp_footer', 'creame_elementor_form_submit_track', 100);


/**
 * ============================================================================
 * Custom project scripts
 * ============================================================================
 */

// It's your turn...
