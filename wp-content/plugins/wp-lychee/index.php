<?php
/**
 * Plugin Name: WP-Lychee KingComposer & qTranslate-X
 * Description: Enables multilingual framework for plugin "KingComposer".
 * Version: 1.0
 * Author: iViVi
 * Author URI: https://ivivi.vn
 * License: GPL2
 * Tags: multilingual, multi, language, translation, qTranslate-X, KingComposer
 * Author e-mail: cse.pmdat@gmail.com
 */

function wp_lychee_init_qtranslate()
{
    add_filter('qtranslate_load_admin_page_config','qtranslate_load_admin_page_config_func', 10, 1);
}
add_action('qtranslate_init_language','wp_lychee_init_qtranslate');

function qtranslate_load_admin_page_config_func($page_config) {

    if (isset($page_config['post'])) {
        $page_config['post']['js-exec'][] = [
            'handle' => 'kingcomposer-js-exec',
            'src' => __DIR__ . '/active_language.js'
        ];
    }
    return $page_config;
}