<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function distm_check_required_files() {
    // Verify requirements
    $requirements_met = true;
    $errors = array();

    // Check PHP version
    if (version_compare(PHP_VERSION, '7.0', '<')) {
        $requirements_met = false;
        $errors[] = 'PHP 7.0 or higher is required. Current version is ' . PHP_VERSION;
    }

    // Check required files
    $required_files = array(
        '../admin/admin-init.php',
        '../admin/admin-pages.php',
        '../admin/admin-menus.php',
        '../frontend/frontend-init.php',
        '../admin/assets/menu-logo.svg'
    );

    foreach ($required_files as $file) {
        if (!file_exists(__DIR__ . '/' . $file)) {
            $requirements_met = false;
            $errors[] = 'Required file missing: ' . $file;
        }
    }

    // If requirements aren't met, deactivate the plugin and show error
    if (!$requirements_met) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            '<p>' . wp_kses_post(implode('</p><p>', array_map('esc_html', $errors))) . '</p>' .
            '<p>' . esc_html__('The Menu plugin cannot be activated. Please fix the issues above and try again.', 'the-menu') . '</p>',
            esc_html__('Plugin Activation Error', 'the-menu'),
            array('back_link' => true)
        );
    }
}

// Enqueue the plugin styles
function distm_enqueue_admin_scripts($hook_suffix) {
    if ($hook_suffix == 'toplevel_page_the-menu' || $hook_suffix == 'the-menu_page_the-menu-license-settings' || $hook_suffix == 'nav-menus.php') {
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('distm-admin-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery', 'wp-color-picker', 'media-upload'), '1.0.7', true);
    }
    if ($hook_suffix == 'toplevel_page_the-menu' || $hook_suffix == 'the-menu_page_the-menu-license-settings'){
        wp_enqueue_style('distm-admin-style', plugin_dir_url(__FILE__) . 'css/style.css', array(), '1.0.7');
    }
}
add_action('admin_enqueue_scripts', 'distm_enqueue_admin_scripts');

// Sanitize the plugin settings
function distm_get_svg_content($url) {
    if (substr($url, -4) !== '.svg') {
        return false;
    }

    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return false;
    }

    $svg_content = wp_remote_retrieve_body($response);
    if (empty($svg_content)) {
        return false;
    }

    return $svg_content;
}