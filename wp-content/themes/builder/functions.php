<?php

add_theme_support('post-thumbnails');
add_theme_support('menus');
add_post_type_support('page', 'excerpt');

/**
 * Return Real Component Path
 * @param $path
 * @return string
 */
function component_path($path = '')
{
    return __DIR__ . "/blocks/" . $path;
}

# Initialize Attribute
require_once('BFI_Thumb.php');
define("BFITHUMB_UPLOAD_DIR", 'cache');

require 'helpers.php';
require 'Attribute.php';
require 'ViewHandler.php';
$attribute = new Attribute();
$__env = $attribute->factory;
$bladeHandler = new ViewHandler();

add_action('init', 'your_init', 99);
function your_init()
{
    global $attribute;

    # Register and use

    # Block Builder Shortcode + Kingcomposer
    $components = rglob(__DIR__ . "/blocks/*.php");
    foreach ($components as $component) {
        $attribute->start_section($component);
    }

    # Start FrontMode
    $attribute->start_front();
}

# Remove unused functions
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

function remove_json_api () {

    // Remove the REST API lines from the HTML Header
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

    // Remove the REST API endpoint.
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );

    // Turn off oEmbed auto discovery.
    add_filter( 'embed_oembed_discover', '__return_false' );

    // Don't filter oEmbed results.
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

    // Remove oEmbed discovery links.
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );

    // Remove Weblog Client
    remove_action ('wp_head', 'rsd_link');

    // Remove  Windows Live Writer Manifest Link
    remove_action( 'wp_head', 'wlwmanifest_link');
    remove_action( 'wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'wp_generator');

}
add_action( 'after_setup_theme', 'remove_json_api' );


// Bind to template include action
add_action('template_include', array($bladeHandler, 'template_include_blade'));

// Listen for index template filter
add_filter('index_template', array($bladeHandler, 'get_query_template'));

// Listen for page template filter
add_filter('page_template', array($bladeHandler, 'get_query_template'));

// Listen for Buddypress include action
add_filter('bp_template_include', array($bladeHandler, 'get_query_template'));

add_filter('single_template', array($bladeHandler, 'single_template'), 11);

function switch_qtranlateX_kingcomposer( $hook ) {
    if ('edit.php' != $hook) {
        return;
    }
    wp_enqueue_script( 'qtranslateX_kingcomposer', __DIR__ . '/compiler/active_langugage.js' );
}

add_action('admin_enqueue_scripts', 'switch_qtranlateX_kingcomposer');

add_filter('wp_nav_menu_mainmenusidebar_items', function ($items) {

    global $q_config;
    if (is_404()) $url = get_option('home'); else $url = '';
    $languages = '<li class="language-chooser">';
    $count = count(qtranxf_getSortedLanguages());
    foreach (qtranxf_getSortedLanguages() as $index => $language) {
        $languages .= '<a href="' . qtranxf_convertURL($url, $language, false, true) . '">' . $q_config['language_name'][$language];
        if ($index < ($count - 1)) {
            $languages .= '<i>|</i>';
        }
    }
    $languages .= "</li>";
    $items = '<li><a href="#" class="b-closemenu"><i class="fa fa-times"></i></a></li>' . $items . $languages;
    return $items;
});

function show_main_menu()
{
    global $wp_query;
    $queried_object = $wp_query->get_queried_object();
    $queried_object_id = (int)$wp_query->queried_object_id;
    $items = wp_get_nav_menu_items("Main Menu");
    $html = "";
    foreach ($items as $menu_item) {
        if ($menu_item->object_id == $queried_object_id &&
            (
                (!empty($home_page_id) && 'post_type' == $menu_item->type && $wp_query->is_home && $home_page_id == $menu_item->object_id) ||
                ('post_type' == $menu_item->type && $wp_query->is_singular) ||
                ('taxonomy' == $menu_item->type && ($wp_query->is_category || $wp_query->is_tag || $wp_query->is_tax) && $queried_object->taxonomy == $menu_item->object)
            )
        ) {
            $is_active = "active";
        } else {
            $is_active = "";
        }
        $html .= '<li class="' . $is_active . '"><a href="' . $menu_item->url . '">' . $menu_item->title . '</a></li>';
    }
    return $html;
}

add_filter('post_gallery', function ($blank, $attr) {
    $ids = $attr['ids'];
    $_attachments = get_posts(['include' => $ids,
        'post_status' => 'inherit',
        'post_type' => 'attachment',
        'post_mime_type' => 'image'
    ]);
    $html = "";
    $options = [1136, 638, 'bfi_thumb' => true];
    foreach ($_attachments as $id => $attachment) {
        $html .= '<div class="item"><img src="' . wp_get_attachment_image_src($attachment->ID, $options)[0]. '" alt=""></div>';
    }
    return $html;
}, 10, 2);