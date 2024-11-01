<?php

namespace Distinct\TheMenu;

/*
Plugin Name: The Menu - Custom mobile navigation with icons
Plugin URI: https://github.com/distinct-development/the-menu-wordpress-org
Description: Create beautiful mobile navigation menus with custom icons, role-based visibility, and extensive style options for your WordPress site.
Version: 1.2.10
Author: Distinct
License: GPL-2.0-or-later
Author URI: https://plugins.distinct.africa
Requires at least: 6.0
Tested up to: 6.6.2
Stable tag: 4.3
Requires PHP: 7.0
*/

if ( ! defined( 'ABSPATH' ) ) exit;

function distm_activate_plugin() {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    if (!wp_doing_ajax() && !in_array($GLOBALS['pagenow'], array('plugins.php', 'update.php'))) {
        return;
    }

    distm_check_required_files();
    distm_load_default_settings();
    
    // Get the current version from options
    $current_version = get_option('distm_plugin_version', '0');
    
    // If this is a new installation or updating from a version before icon types
    if (version_compare($current_version, '1.2.8', '<')) {
        distm_migrate_menu_item_icon_types();
    }
}

function distm_deactivate_plugin() {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    if (!wp_doing_ajax() && !in_array($GLOBALS['pagenow'], array('plugins.php', 'update.php'))) {
        return;
    }

    delete_transient('the-menu_license_status');
}
// Register the activation/deactivation hooks
register_activation_hook(__FILE__, __NAMESPACE__ . '\\distm_activate_plugin');
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\\distm_deactivate_plugin');

// Include the plugin files
include_once(__DIR__ . '/admin/admin-init.php');
include_once(__DIR__ . '/admin/admin-pages.php');
include_once(__DIR__ . '/admin/admin-menus.php');
include_once(__DIR__ . '/frontend/frontend-init.php');

function distm_migrate_menu_item_icon_types() {
    // Get all menu items
    $menu_items = get_posts(array(
        'post_type' => 'nav_menu_item',
        'posts_per_page' => -1,
        'fields' => 'ids'
    ));

    foreach ($menu_items as $menu_item_id) {
        // Get existing icon URL
        $icon_url = get_post_meta($menu_item_id, '_menu_item_icon', true);
        
        // If there's an icon URL, set type to 'upload'
        if (!empty($icon_url) && $icon_url !== '') {
            update_post_meta($menu_item_id, '_menu_item_icon_type', 'upload');
        }
    }

    // Update plugin version in options to track migration
    update_option('distm_plugin_version', '1.2.8');
}

// Add a function to handle plugin updates
function distm_check_for_updates() {
    $current_version = get_option('distm_plugin_version', '0');
    
    // If updating from a version before icon types
    if (version_compare($current_version, '1.2.8', '<')) {
        distm_migrate_menu_item_icon_types();
    }
}
add_action('plugins_loaded', __NAMESPACE__ . '\\distm_check_for_updates');

if (!function_exists('get_plugin_data')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

// License validator class
class Plugin_License_Validator {
    private static $instance = null;
    private $plugin_slug;
    private $plugin_name;
    private $license_option_key;
    private $text_domain;
    private $api_endpoint;

    private function __construct($plugin_slug, $plugin_name, $text_domain) {
        $this->plugin_slug = $plugin_slug;
        $this->plugin_name = $plugin_name;
        $this->license_option_key = $plugin_slug . '_license_key';
        $this->text_domain = $text_domain;
        $this->api_endpoint = 'https://plugins.distinct.africa/api/register-plugin.php';

        add_action('admin_notices', array($this, 'license_status_notice'));
        add_action('admin_init', array($this, 'clear_license_cache'));
        add_filter('plugin_row_meta', array($this, 'modify_plugin_meta'), 10, 2);
        add_action('admin_menu', array($this, 'add_license_submenu'));
        add_action('admin_init', array($this, 'register_license_settings'));
        
        // Hook the license check handler
        add_action('admin_post_the_menu_check_license', array($this, 'handle_license_check'));
    }

    // Get the instance
    public static function get_instance($plugin_slug, $plugin_name, $text_domain) {
        if (self::$instance === null) {
            self::$instance = new self($plugin_slug, $plugin_name, $text_domain);
        }
        return self::$instance;
    }

    // Validate the license
    public function validate_license() {
        $cache_key = $this->plugin_slug . '_license_status';
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached === 'valid';
        }
    
        $license_key = sanitize_text_field(get_option($this->license_option_key, ''));
        $site_url = esc_url_raw(get_site_url());
        $plugin_data = get_plugin_data(__DIR__ . '/' . $this->plugin_slug . '.php');
        $version = sanitize_text_field($plugin_data['Version']);
    
        $response = wp_safe_remote_post($this->api_endpoint, array(
            'body' => wp_json_encode(array(
                'license_key' => $license_key,
                'site_url' => $site_url,
                'version' => $version,
                'plugin_id' => 3
            )),
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $license_key
            ),
            'timeout' => 15, 
        ));
    
        if (is_wp_error($response)) {
            return false;
        }
    
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body);
        $is_valid = isset($data->status) && $data->status === 'success';
        set_transient($cache_key, $is_valid ? 'valid' : 'invalid', 12 * HOUR_IN_SECONDS);
        return $is_valid;
    }

    // License status notice
    public function license_status_notice() {
        $screen = get_current_screen();
        if ($screen->id !== 'the-menu_page_the-menu-license-settings') {
            return;
        }

        if (isset($_GET['license_check_result']) && isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'the_menu_license_check')) {
            $status = sanitize_text_field(wp_unslash($_GET['license_check_result'])) === 'valid' ? 'valid' : 'invalid';
            $class = $status === 'valid' ? 'notice notice-success' : 'notice notice-error';
            $message = $status === 'valid' 
                ? __('License validated successfully.', 'the-menu')
                : __('Invalid license or site is not registered.', 'the-menu');
            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
        }
    }

    // Handle the license check
    public function handle_license_check() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'the-menu'));
        }
    
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'the_menu_check_license_nonce')) {
            wp_die(esc_html__('Invalid nonce verification', 'the-menu'));
        }
    
        $license_status = $this->validate_license();
        $redirect_nonce = wp_create_nonce('the_menu_license_check');
        
        $redirect_url = add_query_arg(
            array(
                'license_check_result' => $license_status ? 'valid' : 'invalid',
                '_wpnonce' => $redirect_nonce,
                'page' => 'the-menu-license-settings'
            ),
            admin_url('admin.php')
        );
    
        wp_safe_redirect($redirect_url);
        exit;
    }

    // Clear the license cache
    public function clear_license_cache() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (
            isset($_GET['license_check_result'], $_GET['_wpnonce']) &&
            wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'the_menu_license_check')
        ) {
            $result = sanitize_text_field(wp_unslash($_GET['license_check_result']));
            if (in_array($result, array('valid', 'invalid'), true)) {
                delete_transient($this->plugin_slug . '_license_status');
            }
        }
    }

    // License key field callback
    public function license_key_field_callback() {
        $license_key = get_option($this->license_option_key, '');
        $status = $this->validate_license() ? 'valid' : 'invalid';
        $status_text = $status === 'valid' ? __('Active', 'the-menu') : __('Unlicensed', 'the-menu');
        $status_class = $status === 'valid' ? 'tm-status-pill-active' : 'tm-status-pill-inactive';
        
        ?>
        <input type="text" id="<?php echo esc_attr($this->license_option_key); ?>" name="<?php echo esc_attr($this->license_option_key); ?>" value="<?php echo esc_attr($license_key); ?>" class="regular-text" />
        <div class="tm-license-zone">
            <span class="tm-status-pill <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status_text); ?></span>
            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=the_menu_check_license'), 'the_menu_check_license_nonce')); ?>" style="text-decoration:none;">
                <span class="dashicons dashicons-image-rotate"></span>
            </a>
        </div>
        <?php
    }

    // Modify the plugin meta
    public function modify_plugin_meta($plugin_meta, $plugin_file) {
        if ($plugin_file == plugin_basename(__FILE__)) {
            $is_registered = $this->validate_license();
            $clear_cache_url = wp_nonce_url(admin_url('plugins.php?action=the_menu_check_license'), 'the_menu_check_license_nonce');
            $status = $is_registered ? '<span style="color: green;"><span class="dashicons dashicons-yes-alt"></span> Registered</span>' : '<span>Unlicensed:</span> Premium features planned <a href="admin.php?page=the-menu-license-settings">Manage license</a> <a href="' . $clear_cache_url . '" title="Refresh License Status"><span class="dashicons dashicons-image-rotate" style="font-size:1.2em;margin-top:2px;"></span></a>';
            foreach ($plugin_meta as &$meta) {
                if (strpos($meta, 'Version') !== false) {
                    $meta .= ' | ' . $status;
                    break;
                }
            }
        }
        return $plugin_meta;
    }

    // Add license submenu
    public function add_license_submenu() {
        add_submenu_page(
            $this->plugin_slug,
            'License settings',
            'License',
            'manage_options',
            $this->plugin_slug . '-license-settings',
            array($this, 'license_settings_page')
        );
    }

    // License settings page
    public function license_settings_page() {
        ?>
        <div class="rss-title">
            <div class="rss-icon"></div>
            <div class="rss-content">
                <h1><?php echo esc_html($this->plugin_name); ?></h1>
                <p>License key settings</p>
            </div>
        </div>
        <div class="tm-wrap mt0">
            <div class="tm-wrapper">
                <?php
                    echo '<form action="options.php" method="post">';
                    settings_fields($this->plugin_slug . '_license_settings');
                    do_settings_sections($this->plugin_slug . '_license_settings');
                    submit_button('Save license key');
                    echo '</form>';
                ?>
            </div>
        </div>
        <?php
        include_once 'admin/assets/logo-wrapper.php';
    }

    // Handle the license settings
    public function register_license_settings() {
        if (!current_user_can('manage_options')) {
            return;
        }

        register_setting(
            $this->plugin_slug . '_license_settings',
            $this->license_option_key,
            array(
                'sanitize_callback' => array($this, 'sanitize_license_key'),
                'default' => ''
            )
        );

        add_settings_section(
            $this->plugin_slug . '_license_section',
            $this->plugin_name . ': License key settings',
            null,
            $this->plugin_slug . '_license_settings'
        );
        
        add_settings_field(
            $this->plugin_slug . '_license_key_field',
            'License Key',
            array($this, 'license_key_field_callback'),
            $this->plugin_slug . '_license_settings',
            $this->plugin_slug . '_license_section'
        );
    }
    // Sanitize the license key
    public function sanitize_license_key($input) {
        $sanitized = preg_replace('/\s+/', '', $input);
        $sanitized = preg_replace('/[^0-9a-fA-F]/', '', $sanitized);
        $sanitized = strtolower($sanitized);
        return substr($sanitized, 0, 56);
    }
}

// Load the license validator
function distm_load_the_menu() {
    Plugin_License_Validator::get_instance('the-menu', 'The Menu', 'the_menu');
}
add_action('plugins_loaded', __NAMESPACE__ . '\\distm_load_the_menu');
