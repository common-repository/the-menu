<?php
if (!defined('ABSPATH')) exit;

// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Function to check if we should delete data
function distm_should_delete_data() {
    $settings = get_option('distm_settings');
    return isset($settings['distm_delete_data']) && $settings['distm_delete_data'];
}

// Only proceed if the user opted to delete data
if (distm_should_delete_data()) {
    // Remove all plugin options
    delete_option('distm_settings');
    
    // Remove any transients
    delete_transient('the-menu_license_status');

    // Meta keys to remove
    $meta_keys = array(
        '_menu_item_icon',
        '_menu_item_visibility',
        '_menu_item_roles'
    );

    // Get all menu items
    $menu_items = get_posts(array(
        'post_type' => 'nav_menu_item',
        'posts_per_page' => -1,
        'fields' => 'ids'
    ));

    // Delete meta for each menu item
    foreach ($menu_items as $menu_item_id) {
        foreach ($meta_keys as $meta_key) {
            delete_post_meta($menu_item_id, $meta_key);
        }
    }

    // Clear any cached data
    wp_cache_flush();

    // Clean up wp_options
    $option_keys = array(
        'distm_settings',
        'the-menu_license_status'
    );

    foreach ($option_keys as $option) {
        delete_option($option);
    }
}