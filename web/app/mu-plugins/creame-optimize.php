<?php
/*
Plugin Name:  Creame Optimize
Plugin URI:   https://crea.me/
Description:  Optimizaciones de Creame para mejorar tu <em>site</em>.
Version:      1.4.3
Author:       Creame
Author URI:   https://crea.me/
License:      MIT License
*/


/**
 * ============================================================================
 * WP Admin clean up
 * Needs intervention (https://github.com/soberwp/intervention)
 * ============================================================================
 */

use function \Sober\Intervention\intervention;

if (function_exists('Sober\Intervention\intervention')) {
    // Front/Admin customizations
    add_action('init', 'creame_custom_intervention', 1);
    add_action('admin_init', 'creame_custom_intervention_admin', 1);
}

function creame_custom_intervention() {
    // Remove frontend toolbar
    // intervention('remove-toolbar-frontend');

    // Remove Emoji
    intervention('remove-emoji');
}

function creame_custom_intervention_admin() {
    // Add Welcome Dashboard
    // intervention('add-dashboard-item', [
    //     'Welcome',
    //     'Welcome to '.get_bloginfo('name')
    // ]);

    // Remove Emoji
    intervention('remove-emoji');

    // Add SVG Support
    intervention('add-svg-support', [
        'admin',
        'editor',
    ]);

    // Remove Dashboard Items
    // all, welcome, notices, activity, right-now, recent-comments, incoming-links, plugins, quick-draft, drafts, news
    intervention('remove-dashboard-items', [
        'welcome',
        'quick-draft',
        'news',
    ]);

    // Remove Howdy
    intervention('remove-howdy', '');

    // Update label footer
    $email = defined('CREAME_SUPPORT_EMAIL') ? CREAME_SUPPORT_EMAIL : 'i@crea.me';
    $label = '<em><strong>Wordpress</strong> optimizado por <strong><a href="https://crea.me" style="' .
        'background:url(\'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAAZCAMAAADzN3VRAAAB7FB' .
        'MVEUAAAAAi00Uak1LTk1MTU1NTU1PTE1bRk1dSE1yOk1zOU17NU3YBk3aBU3bBU3hAU3uAE3/AE1NTU1TSk3YBk1MT' .
        'U1NTU2JLk2SKk3YBk1NTU2lIE3YBk3bBU1MTU1NTU1RS02YJ03XB03YBk2uG02nH03/AE3ZBk1NTU1IUE1NTU3YBk1' .
        'MTk1NTU3WB03YBk1KTk3YBk3YBk3YBk0AeE3YBk3YBk1gQ027FU1NTU1NTU3YBk3YBk3YBk3YBk3YBk0vXE3YBk1NT' .
        'U3YBk3YBk3YBk3ZBU1NTU3VCE3aBU3YBk3VCE3YBk3YBk3XBk3YBk3YBk1SS03YBk3YBk3YBk1NTU1NTU1NTU3YBk1' .
        'NTU1QS03YBk1tPU1NTU3YBk3XB03YBk3YBk1NTU3YBk3LDU3YBk1NTU3YBk1NTU3NDE1NTU3YBk3YBk3YBk1NTU1QT' .
        'E3YBk3YBk1XSE3YBk1NTU1NTU1NTU3YBk3YBk3YBk1NTU3YBk3YBk3gAk1PTE3YBk3YBk3YBk3WB03YBk3eA03JDU3' .
        'WB03YBk1NTU2/E03YBk1EUk3XB03YBk1NTU3YBk1EUk1JT01LTk1NTU1OTE1OTU1dRU1fRE2cJU3RCU3VB03WB03XB' .
        'k3YBk3ZBU3aBU3cBE3eA03hAU3///+NV4AIAAAAkHRSTlMAAAAAAAAAAAAAAAAAAAAAAAABAQECAgICAgMDAwMEBAQ' .
        'EBAQFBgYHCQoNDQ8PEBMVFxgfICEsLS8yNTU6Oz9AQUNFTE5PT1FWWFtgYWVnZ2ltb3B4fn+AgISGh42OlJiYmZ2fo' .
        'KGjo6aoqbG1ubq6wMnKyszT2Nve4uXm6u3v8vj5+vr6+/v7/Pz8/f39/v6XkcTvAAABfUlEQVR4AX3QA4MbQRTA8Sn' .
        'iV8Tm1E7N1HZT20htnRVzznfv9Envbez/en/LYa2TSdWcKMZ0NhttaqB4ihaTjKoCU+Fw69s7MpupSABarXfpltBtm' .
        'U7GTghxT2Zz65ZowcG5FXTLNn4e/rdJpmOy10I83yAzuTxmtQrAsm7/t0z2utyoYDJ3SIhfZ21arf3cg+ChlWcmB8c' .
        'fatYogdH7bkWE+Hn/2Mv5RBo/bjs/t9B5cYVzFaNvlB34lBIjubHowFAMf69/MtWHV8HBZBLJDj760RUfnZid7sXHu' .
        'zCd7tkLjEj66uX6Hft2v5uJpbHb/wV78AWJRNJny3xwDcMYw6N3sR//b68MlW7t6iskYbx8gVZ4hJUDDjfzcuMkhtN' .
        '4ilXiEKTzSfzwBul1x6vEAQF6S740duysEgMYvtO7MRnDXnwKVQJWONyD4XAsHMY//hohCvzFNE1f94C1Rog2X3r1/' .
        'tlpTnuslhwGkLJagaSWDJw7uENSVhuUYm1aBIKXbqRcHZAFAAAAAElFTkSuQmCC\') center right no-repeat;' .
        'background-size:auto 100%;padding:0 25px 4px 0;text-decoration:none;">Creame</a></strong> ' .
        'Soporte/incidencias: <a href="mailto:' . $email . '">' . $email . '</a></em>';

    intervention('update-label-footer', $label);

    // Remove Page Components
    // all, editor, author, thumbnail, page-attributes, custom-fields, comments
    intervention('remove-page-components', [
        'author',
        'custom-fields',
        'comments',
    ]);

    // Remove Post Components
    // all, editor, author, excerpt, trackbacks, custom-fields, comments, slug, revisions, thumbnail
    intervention('remove-post-components', [
        // 'comments',
        'trackbacks',
        'custom-fields',
    ]);

    // Remove unused user fields
    // options, option-title, option-editor, option-schemes, option-shortcuts, option-toolbar,
    // names, name-first, name-last, name-nickname, name-display,
    // contact, contact-web, about, about-bio, about-profile
    intervention('remove-user-fields', [
        'option-title',
        'option-editor',
        'option-schemes',
        'option-shortcuts',
        'contact',
        'contact-web',
        'about',
        'about-bio',
        'about-profile',
    ]);

    // Remove toolbar items for all
    // logo, updates, site-name, comments, customize, new, new-post, new-page, new-media, new-user, account,
    // account-user, account-profile, view, preview, archive
    intervention('remove-toolbar-items', [
        'logo',
        'comments',
        'customize',
    ], 'all');

    // Remove toolbar items for non-admin users
    intervention('remove-toolbar-items', [
        'updates',
    ], 'all-not-admin');

    // Remove update notices from non-admin users
    intervention('remove-update-notices');

    // Remove senseless user roles from WordPress
    intervention('remove-user-roles', [
        'subscriber',
        'contributor',
    ]);

    // Set default pagination at 50 (wp default 20)
    // intervention('update-pagination', 50);

    // Remove Menu items
    // all, danger-zone, dashboard, updates,
    // posts, post-new, post-categories, post-tags,
    // media, media-new,
    // pages, page-new,
    // comments,
    // themes, theme-widgets, theme-menu, theme-editor,
    // plugins, plugin-new, plugin-editor,
    // users, user-new, user-profile,
    // tools, tool-import, tool-export,
    // settings, setting-writing, setting-reading, setting-media, setting-permalink, setting-discussion, setting-media, setting-disable-comments,
    // acf, acf-new, acf-tools, acf-updates
    intervention('remove-menu-items', 'danger-zone', 'all-not-admin');

    intervention('remove-menu-items', [
        // 'posts',
        // 'comments',
        'theme-editor',
        'plugin-editor',
        'tool-import',
        'tool-export',
    ], 'all');

    // Remove ACF Menu items on production
    if (defined('WP_ENV') && WP_ENV === 'production') {
        intervention('remove-menu-items', [
            'acf',
            'acf-new',
            'acf-tools',
            'acf-updates',
        ], 'all');
    }

    // Remove Tags taxonomy
    // intervention('remove-taxonomies', 'tag');
}

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

// Add wp-env-environment body class
function creame_body_env_class( $classes ) {
    $class = defined('WP_ENV') ? 'wp-env-' . WP_ENV : 'wp-env-none';
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
</style>
<?php
}
add_action('admin_head', 'creame_custom_admin_styles');

// Disable password change notification to admin
if (!function_exists('wp_password_change_notification')) {
    function wp_password_change_notification() {}
}

// Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false', PHP_INT_MAX);
add_filter('xmlrpc_methods', '__return_empty_array', PHP_INT_MAX);
add_filter('xmlrpc_element_limit', function (): int { return 1; }, PHP_INT_MAX);

// Disable post by email
add_filter( 'enable_post_by_email_configuration', '__return_false' );

// Hide other themes on Admin > Appearance
function creame_hide_themes($wp_themes){
    return array_intersect_key($wp_themes, [WP_DEFAULT_THEME => 1]);
}
if (defined('WP_DEFAULT_THEME')) add_filter('wp_prepare_themes_for_js', 'creame_hide_themes');

// Fix JetEngine assets path
add_filter('cx_include_module_url', function($url, $path){
    return plugin_dir_url(preg_replace('/\/releases\/\d+\//', '/current/', $path));
}, 10, 2);

// Remove JetPlugins admin menu
add_action('admin_menu', function(){ remove_menu_page('jet-dashboard'); }, 100);

// Disable WooCommerce 4 noise
add_filter('woocommerce_helper_suppress_connect_notice', '__return_true');

// iThemes Security disable write wp-config.php
add_filter('itsec_filter_can_write_to_files', '__return_false');

// object-cache.php disable flush error
add_filter('pecl_memcached/warn_on_flush', '__return_false');


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

// Attachment unique slugs (pervent post conflicts)
function creame_unique_attachment_slug($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug) {
    return 'attachment' == $post_type ? uniqid('media-') : $slug;
}
add_filter('wp_unique_post_slug', 'creame_unique_attachment_slug', 10, 6);

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

// Remove wordpress version
remove_action('wp_head', 'wp_generator');

// Remove wlwmanifest.xml (needed to support windows live writer)
remove_action('wp_head', 'wlwmanifest_link');

// Remove really simple discovery link
remove_action('wp_head', 'rsd_link');

// Remove all extra rss feed links
remove_action('wp_head', 'feed_links_extra', 3);

// Remove shortlink
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

// Remove oembed links
remove_action('wp_head', 'wp_oembed_add_discovery_links');
// remove_action('wp_head', 'wp_oembed_add_host_js'); // emded.js

// Remove rel links
remove_action('wp_head', 'start_post_rel_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link');
remove_action('wp_head', 'adjacent_posts_rel_link');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

// Remove REST-API link
remove_action('wp_head', 'rest_output_link_wp_head');

// Disable REST-API
// add_filter('json_enabled', '__return_false');
// add_filter('json_jsonp_enabled', '__return_false');

// Remove commments cookies
remove_action('set_comment_cookies', 'wp_set_comment_cookies');

// Removes the generator name from the RSS feeds
add_filter('the_generator', '__return_false');

// Remove gallery inline styles
add_filter('use_default_gallery_style', '__return_false');

// Remove comments feed link
add_filter('feed_links_show_comments_feed', '__return_false');

// Remove recent comments widget styles
add_filter('show_recent_comments_widget_style', '__return_false');

// Remove plugin revslider generator meta
add_filter('revslider_meta_generator', '__return_false');

// Remove CSS and JS query strings versions
function creame_remove_cssjs_ver_filter($src){
    return strpos($src, '?ver=') ? remove_query_arg('ver', $src) : $src;
}
add_filter('style_loader_src', 'creame_remove_cssjs_ver_filter', 10, 2);
add_filter('script_loader_src', 'creame_remove_cssjs_ver_filter', 10, 2);

// Remove jQuery migrate
function creame_remove_jquery_migrate($scripts) {
    if (!is_admin()) $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
}
add_action('wp_default_scripts', 'creame_remove_jquery_migrate');

// Load jQuery from jQuery's CDN with a local fallback
function creame_register_jquery_from_cdn(){
    $jquery_version = str_replace('-wp', '', wp_scripts()->registered['jquery']->ver);

    wp_deregister_script('jquery');
    wp_register_script('jquery', 'https://code.jquery.com/jquery-' . $jquery_version . '.min.js', [], null, true);

    add_filter('wp_resource_hints', function ($urls, $relation_type) {
        if ($relation_type === 'dns-prefetch') $urls[] = 'code.jquery.com';
        return $urls;
    }, 10, 2);

    $jquery_fallback_src = apply_filters('script_loader_src', includes_url('/js/jquery/jquery.js'), 'jquery-fallback');
    // Use "<scri"+"pt to avoid Elementor rocket_loader_filter
    wp_add_inline_script('jquery', 'window.jQuery || document.write("<scri"+"pt src=\"' . $jquery_fallback_src . '\"><\/script>");');
}
add_action('wp_enqueue_scripts', 'creame_register_jquery_from_cdn', 100);

// Move scripts to footer
function creame_move_scripts_to_footer() {
    remove_action('wp_head', 'wp_print_scripts');
    remove_action('wp_head', 'wp_print_head_scripts', 9);
    remove_action('wp_head', 'wp_enqueue_scripts', 1);

    add_action('wp_footer', 'wp_print_scripts', 5);
    add_action('wp_footer', 'wp_enqueue_scripts', 5);
    add_action('wp_footer', 'wp_print_head_scripts', 5);
}
// add_action('wp_enqueue_scripts', 'creame_move_scripts_to_footer');

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

// Conditional plugin load
function creame_conditional_plugins ($plugins){
    return array_diff($plugins, [
        'duplicate-post/duplicate-post.php',
        // Add project specific plugins...
    ]);
}
if (!defined('WP_CLI') && !is_admin()) add_filter('option_active_plugins', 'creame_conditional_plugins', 1);

// Add custom metas
function creame_custom_metas() {
    // SEO no index search results
    if (is_search()) echo '<meta name="robots" content="noindex, follow">' . PHP_EOL;

    // Add other metas...
}
add_action('wp_head', 'creame_custom_metas');

// Remove filter capital P dangit
remove_filter( 'the_title', 'capital_P_dangit', 11 );
remove_filter( 'the_content', 'capital_P_dangit', 11 );
remove_filter( 'comment_text', 'capital_P_dangit', 31 );


/**
 * ============================================================================
 * Elementor
 * ============================================================================
 */

// Elementor editor reduce widgets size
add_action('elementor/editor/after_enqueue_styles', function(){
    wp_add_inline_style('elementor-editor', '.elementor-panel .elementor-element { display:flex; } .elementor-panel .elementor-element .icon { padding:5px; }');
});

// Elementor disable data tracking
add_filter( 'pre_option_elementor_allow_tracking', function(){ return 'no'; } );

// Elementor font-display https://developers.elementor.com/elementor-pro-2-7-custom-fonts-font-display-support/
add_filter( 'elementor_pro/custom_fonts/font_display', function(){ return 'swap'; });


/**
 * ============================================================================
 * Autoptimize
 * ============================================================================
 */

// Fix '/wp/' for url replace
define('AUTOPTIMIZE_WP_SITE_URL', WP_HOME);

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
    return $robots . PHP_EOL .
        "Allow: /*.js$" . PHP_EOL .
        "Allow: /*.css$" . PHP_EOL .
        PHP_EOL .
        "Disallow: /?s=" . PHP_EOL .
        "Disallow: /search" . PHP_EOL .
        PHP_EOL .
        "Allow: /feed/$" . PHP_EOL .
        "Disallow: /comments/feed" . PHP_EOL .
        "Disallow: /*/feed/$" . PHP_EOL .
        "Disallow: /*/feed/rss/$" . PHP_EOL .
        "Disallow: /*/*/feed/$" . PHP_EOL .
        "Disallow: /*/*/feed/rss/$" . PHP_EOL .
        "Disallow: /*/*/*/feed/$" . PHP_EOL .
        "Disallow: /*/*/*/feed/rss/$" . PHP_EOL;
}
add_filter('robots_txt', 'creame_custom_robots_txt', 11);


/**
 * ============================================================================
 * Loco Translate, always allow file modification on Custom location
 * ============================================================================
 */

// Add '_loco' to default Loco context
function custom_loco_file_mod_allowed_context($context, $file) {
    return strpos($file->dirname(), 'languages/loco') === false ? $context : $context . '_loco';
}
add_filter('loco_file_mod_allowed_context', 'custom_loco_file_mod_allowed_context', 10, 2);

// If context is 'download_language_pack_loco' always allow modification
function custom_file_mod_allowed($allow, $context) {
    return 'download_language_pack_loco' === $context ? true : $allow;
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
}


/**
 * ============================================================================
 * Custom project scripts
 * ============================================================================
 */
