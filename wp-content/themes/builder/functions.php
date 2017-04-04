<?php

add_theme_support('post-thumbnails');
add_theme_support( 'menus' );
add_post_type_support( 'page', 'excerpt' );

/**
 * Return Real Component Path
 * @param $path
 * @return string
 */
function component_path($path = '') {
    return __DIR__ . "/blocks/" . $path;
}

# Initialize Attribute
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

// Bind to template include action
add_action( 'template_include', array( $bladeHandler, 'template_include_blade' ) );

// Listen for index template filter
add_filter( 'index_template', array( $bladeHandler, 'get_query_template' ) );

// Listen for page template filter
add_filter( 'page_template', array( $bladeHandler, 'get_query_template' ) );

// Listen for Buddypress include action
add_filter( 'bp_template_include', array( $bladeHandler, 'get_query_template' ));

add_filter('single_template', array( $bladeHandler, 'single_template' ), 11);

function start_scripts()
{
    wp_enqueue_style('start-style', get_stylesheet_uri());

    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css');
}

add_action('wp_enqueue_scripts', 'start_scripts');