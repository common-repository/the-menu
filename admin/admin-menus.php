<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Register the plugin settings
function distm_register_my_menus() {
    register_nav_menus(
        array(
            'left-menu' => esc_html__('[THE MENU] Left Menu', 'the-menu'),
            'right-menu' => esc_html__('[THE MENU] Right Menu', 'the-menu'),
            'addon-menu' => esc_html__('[THE MENU] Add-on Menu', 'the-menu')
        )
    );
}
add_action('after_setup_theme', 'distm_register_my_menus');

// Add custom fields to the menu items
function distm_add_custom_menu_fields($item_id, $item, $depth, $args) {
    if (!current_user_can('edit_theme_options')) {
        return;
    }

    $nonce_field = 'distm_menu_item_' . $item_id;
    wp_nonce_field('distm_menu_item_' . $item_id, $nonce_field);

    // Get existing meta values with proper fallbacks
    $icon = get_post_meta($item_id, '_menu_item_icon', true);
    $icon_type = get_post_meta($item_id, '_menu_item_icon_type', true);
    $dashicon = get_post_meta($item_id, '_menu_item_dashicon', true);

    // Set default values if not set
    if (empty($icon_type)) {
        $icon_type = 'dashicon';
    }
    if (empty($dashicon)) {
        $dashicon = 'menu';
    }
    ?>
    <div class="field-custom description description-wide">
        <h3 style="margin-bottom: 10px;"><span class="dashicons dashicons-align-full-width"></span> <?php esc_html_e('Menu Icon', 'the-menu'); ?></h3>
        
        <!-- Icon Type Selector -->
        <div class="icon-type-selector" style="margin-bottom: 10px;">
            <label class="icon-type-label">
                <input type="radio" name="menu-item-icon-type[<?php echo esc_attr($item_id); ?>]" 
                    value="dashicon" <?php checked($icon_type === 'dashicon' || empty($icon)); ?> class="icon-type-radio">
                <?php esc_html_e('Select dashicon', 'the-menu'); ?>
            </label>
            <label class="icon-type-label" style="margin-left: 15px;">
                <input type="radio" name="menu-item-icon-type[<?php echo esc_attr($item_id); ?>]" 
                    value="upload" <?php checked($icon_type === 'upload' && !empty($icon)); ?> class="icon-type-radio">
                <?php esc_html_e('Upload icon', 'the-menu'); ?>
            </label>
        </div>

        <!-- Hidden field to ensure the dashicon value is always submitted -->
        <input type="hidden" 
            name="menu-item-dashicon[<?php echo esc_attr($item_id); ?>]" 
            class="selected-dashicon" 
            value="<?php echo esc_attr($dashicon); ?>">

        <!-- Upload Icon Section -->
        <div class="icon-upload-section" style="<?php echo $icon_type === 'dashicon' ? 'display: none;' : ''; ?>">
            <div class="icon-preview" style="margin-bottom: 10px;">
                <?php if (!empty($icon) && $icon_type === 'upload'): ?>
                    <img src="<?php echo esc_url($icon); ?>" style="max-width: 40px; height: auto;">
                <?php endif; ?>
            </div>
            <input type="text" 
                id="edit-menu-item-icon-<?php echo esc_attr($item_id); ?>" 
                class="widefat code edit-menu-item-icon"
                style="width: 70%;" 
                name="menu-item-icon[<?php echo esc_attr($item_id); ?>]" 
                value="<?php echo $icon_type !== 'dashicon' ? esc_attr($icon) : ''; ?>" 
                readonly/>
            <button type="button" 
                class="button upload-icon-button" 
                style="width: 29%;"
                data-item-id="<?php echo esc_attr($item_id); ?>"><?php esc_html_e('Upload', 'the-menu'); ?></button>
        </div>

        <!-- Dashicon Selection Section -->
        <div class="dashicon-selection-section" style="<?php echo $icon_type === 'upload' ? 'display: none;' : ''; ?>">
            <input type="text" 
                class="dashicon-search" 
                placeholder="<?php esc_attr_e('Search dashicons...', 'the-menu'); ?>"
                style="width: 100%; margin-bottom: 10px;">
            <div class="dashicon-grid" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                <?php
                // Commonly used menu icons displayed first
                $dashicons = array(
                    'menu' => __('Menu (Default)', 'the-menu'),
                    'admin-home' => __('Home', 'the-menu'),
                    'admin-users' => __('Users', 'the-menu'),
                    'admin-post' => __('Post', 'the-menu'),
                    'admin-settings' => __('Settings', 'the-menu'),
                    'admin-appearance' => __('Appearance', 'the-menu'),
                    'cart' => __('Cart', 'the-menu'),
                    'category' => __('Category', 'the-menu'),
                    'dashboard' => __('Dashboard', 'the-menu'),
                    'email' => __('Email', 'the-menu'),
                    'calendar' => __('Calendar', 'the-menu'),
                    'format-gallery' => __('Gallery', 'the-menu'),
                    'heart' => __('Heart', 'the-menu'),
                    'info' => __('Info', 'the-menu'),
                    'location' => __('Location', 'the-menu'),
                    'phone' => __('Phone', 'the-menu'),
                    'portfolio' => __('Portfolio', 'the-menu'),
                    'products' => __('Products', 'the-menu'),
                    'search' => __('Search', 'the-menu'),
                    'share' => __('Share', 'the-menu'),
                    'star-empty' => __('Star', 'the-menu'),
                    'tag' => __('Tag', 'the-menu'),
                    'format-video' => __('Video', 'the-menu'),
                );

                // Display commonly used icons first
                foreach ($dashicons as $icon_name => $label) {
                    $selected = ($dashicon === $icon_name) ? 'selected' : '';
                    printf(
                        '<span class="dashicons dashicons-%s dashicon-option %s" data-icon="%s" title="%s"></span>',
                        esc_attr($icon_name),
                        esc_attr($selected),
                        esc_attr($icon_name),
                        esc_attr($label)
                    );
                }

                // Add separator
                echo '<div class="dashicon-separator" style="width: 100%; border-bottom: 1px solid #ddd; margin: 10px 0; grid-column: 1/-1;"></div>';

                // Add category based icons
                $all_dashicons = array(
                    // Admin Menu
                    __('Admin Menu', 'the-menu') => array(
                        'menu', 'menu-alt', 'menu-alt2', 'menu-alt3',
                        'admin-site', 'admin-site-alt', 'admin-site-alt2', 'admin-site-alt3',
                        'dashboard', 'admin-post', 'admin-media', 'admin-links', 'admin-page',
                        'admin-comments', 'admin-appearance', 'admin-plugins', 'plugins-checked',
                        'admin-users', 'admin-tools', 'admin-settings', 'admin-network', 'admin-home',
                        'admin-generic', 'admin-collapse', 'filter', 'admin-customizer', 'admin-multisite'
                    ),
                    __('Welcome Screen', 'the-menu') => array(
                        'welcome-write-blog', 'welcome-add-page', 'welcome-view-site', 'welcome-widgets-menus',
                        'welcome-comments', 'welcome-learn-more'
                    ),
                    __('Post Formats', 'the-menu') => array(
                        'format-aside', 'format-image', 'format-gallery', 'format-video', 'format-status', 
                        'format-quote', 'format-chat', 'format-audio', 'camera', 'camera-alt',
                        'images-alt', 'images-alt2', 'video-alt', 'video-alt2', 'video-alt3'
                    ),
                    __('Media', 'the-menu') => array(
                        'media-archive', 'media-audio', 'media-code', 'media-default', 'media-document',
                        'media-interactive', 'media-spreadsheet', 'media-text', 'media-video',
                        'playlist-audio', 'playlist-video', 'controls-play', 'controls-pause',
                        'controls-forward', 'controls-skipforward', 'controls-back', 'controls-skipback',
                        'controls-repeat', 'controls-volumeon', 'controls-volumeoff'
                    ),
                    __('Social', 'the-menu') => array(
                        'share', 'share-alt', 'share-alt2', 'rss', 'email',
                        'email-alt', 'email-alt2', 'networking', 'amazon',
                        'facebook', 'facebook-alt', 'google', 'instagram',
                        'linkedin', 'pinterest', 'podio', 'reddit', 'spotify',
                        'twitch', 'twitter', 'twitter-alt', 'whatsapp',
                        'xing', 'youtube'
                    ),
                    __('Misc', 'the-menu') => array(
                        'location', 'location-alt', 'vault', 'shield', 'shield-alt',
                        'sos', 'search', 'slides', 'analytics', 'chart-pie',
                        'chart-bar', 'chart-line', 'chart-area', 'groups',
                        'businessman', 'businesswoman', 'businessperson', 'id',
                        'id-alt', 'products', 'awards', 'forms', 'testimonial',
                        'portfolio', 'book', 'book-alt', 'download', 'upload',
                        'backup', 'clock', 'lightbulb', 'microphone', 'desktop',
                        'laptop', 'tablet', 'smartphone', 'phone', 'store',
                        'album', 'palmtree', 'tickets-alt', 'money', 'money-alt',
                        'smiley', 'thumbs-up', 'thumbs-down', 'superhero', 'superhero-alt'
                    )
                );

                foreach ($all_dashicons as $category => $icons) {
                    printf(
                        '<div class="dashicon-category-header" style="width: 100%%; grid-column: 1/-1; margin: 10px 0 5px; font-weight: bold;">%s</div>',
                        esc_html($category)
                    );
                    
                    foreach ($icons as $icon_name) {
                        if (!isset($dashicons[$icon_name])) {  // Skip if already shown in common icons
                            $selected = ($dashicon === $icon_name) ? 'selected' : '';
                            printf(
                                '<span class="dashicons dashicons-%s dashicon-option %s" data-icon="%s" title="%s"></span>',
                                esc_attr($icon_name),
                                esc_attr($selected),
                                esc_attr($icon_name),
                                esc_attr($icon_name)
                            );
                        }
                    }
                }
                ?>
            </div>
            <input type="hidden" 
                name="menu-item-dashicon[<?php echo esc_attr($item_id); ?>]" 
                class="selected-dashicon" 
                value="<?php echo esc_attr($dashicon); ?>">
        </div>
    </div>
    <?php
}
add_filter('wp_nav_menu_item_custom_fields', 'distm_add_custom_menu_fields', 10, 4);

// Save the custom menu fields
function distm_save_custom_menu_fields($menu_id, $menu_item_db_id, $args) {
    if (!current_user_can('edit_theme_options')) {
        return;
    }

    // Verify nonce
    $nonce_field = 'distm_menu_item_' . $menu_item_db_id;
    if (!isset($_POST[$nonce_field]) || 
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_field])), 'distm_menu_item_' . $menu_item_db_id)) {
        return;
    }

    // Get the icon URL and type
    $icon_url = isset($_POST['menu-item-icon'][$menu_item_db_id]) ? 
        esc_url_raw(wp_unslash($_POST['menu-item-icon'][$menu_item_db_id])) : '';
    
    $icon_type = isset($_POST['menu-item-icon-type'][$menu_item_db_id]) ?
        sanitize_text_field(wp_unslash($_POST['menu-item-icon-type'][$menu_item_db_id])) : 'dashicon';

    // Force icon type to dashicon if there's no URL
    if (empty($icon_url)) {
        $icon_type = 'dashicon';
    }
    
    // If we have a URL, force icon type to upload
    if (!empty($icon_url)) {
        $icon_type = 'upload';
        update_post_meta($menu_item_db_id, '_menu_item_icon', $icon_url);
    } else {
        delete_post_meta($menu_item_db_id, '_menu_item_icon');
    }

    // Save icon type
    update_post_meta($menu_item_db_id, '_menu_item_icon_type', $icon_type);

    // Handle dashicon - always save a default
    $dashicon = isset($_POST['menu-item-dashicon'][$menu_item_db_id]) 
        ? sanitize_text_field(wp_unslash($_POST['menu-item-dashicon'][$menu_item_db_id]))
        : 'menu';
    update_post_meta($menu_item_db_id, '_menu_item_dashicon', $dashicon);
}
add_action('wp_update_nav_menu_item', 'distm_save_custom_menu_fields', 10, 3);

function distm_add_menu_nonce_field() {
    wp_nonce_field('distm_custom_menu_fields', 'distm_custom_menu_nonce');
}
add_action('wp_nav_menu_item_custom_fields', 'distm_add_menu_nonce_field', 5, 4);

// Add the custom visibility field
function distm_add_custom_visibility_field($item_id, $item, $depth, $args) {
    $visibility = get_post_meta($item_id, '_menu_item_visibility', true);
    if (empty($visibility)) {
        $visibility = 'everyone';
    }
    $roles = get_post_meta($item_id, '_menu_item_roles', true);
    if (!is_array($roles)) {
        $roles = array();
    }
    
    $visibility_options = array(
        'everyone' => __('Everyone', 'the-menu'),
        'logged_in' => __('Logged in users', 'the-menu'),
        'logged_out' => __('Logged out users', 'the-menu')
    );

    $available_roles = wp_roles()->roles;
    ?>
    <p class="field-visibility description description-wide">
        <label for="edit-menu-item-visibility-<?php echo esc_attr($item_id); ?>">
            <?php esc_html_e('Visibility', 'the-menu'); ?><br />
            <select id="edit-menu-item-visibility-<?php echo esc_attr($item_id); ?>" 
                    class="widefat code edit-menu-item-visibility" 
                    name="menu-item-visibility[<?php echo esc_attr($item_id); ?>]">
                <?php foreach ($visibility_options as $key => $label) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($visibility, $key); ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </p>

    <div class="field-roles description description-wide" style="<?php echo $visibility === 'logged_in' ? '' : 'display:none;'; ?>">
        <label>
            <?php esc_html_e('User Roles (for logged in users)', 'the-menu'); ?><br />
            <?php foreach ($available_roles as $role_key => $role) : ?>
                <input type="checkbox" 
                       name="menu-item-roles[<?php echo esc_attr($item_id); ?>][]" 
                       value="<?php echo esc_attr($role_key); ?>" 
                       <?php checked(in_array($role_key, $roles)); ?>>
                <?php echo esc_html($role['name']); ?><br>
            <?php endforeach; ?>
            <small><i style="opacity: 0.5;line-height:1em;">
                <?php esc_html_e('Select who can see this menu item. If none are selected, all roles can see it.', 'the-menu'); ?>
            </i></small>
        </label>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#edit-menu-item-visibility-<?php echo esc_js($item_id); ?>').on('change', function() {
                var $rolesField = $(this).closest('.menu-item-settings').find('.field-roles');
                if ($(this).val() === 'logged_in') {
                    $rolesField.slideDown();
                } else {
                    $rolesField.slideUp();
                }
            });
        });
    </script>
    <?php
}
add_action('wp_nav_menu_item_custom_fields', 'distm_add_custom_visibility_field', 10, 4);


// Save the custom visibility field
function distm_save_custom_visibility_field($menu_id, $menu_item_db_id) {
    if (!current_user_can('edit_theme_options')) {
        return;
    }

    if (!isset($_POST['distm_custom_menu_nonce']) || 
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['distm_custom_menu_nonce'])), 'distm_custom_menu_fields')) {
        return;
    }

    // Save visibility setting
    if (isset($_POST['menu-item-visibility'][$menu_item_db_id])) {
        $visibility = sanitize_text_field(wp_unslash($_POST['menu-item-visibility'][$menu_item_db_id]));
        update_post_meta($menu_item_db_id, '_menu_item_visibility', $visibility);
    }

    // Save user roles
    $roles = isset($_POST['menu-item-roles'][$menu_item_db_id]) ? 
            array_map('sanitize_text_field', wp_unslash($_POST['menu-item-roles'][$menu_item_db_id])) : 
            array();
    update_post_meta($menu_item_db_id, '_menu_item_roles', $roles);
}
add_action('wp_update_nav_menu_item', 'distm_save_custom_visibility_field', 10, 2);

// WordPress Walker for the menu
class DISTM_Icon_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth=0, $args=null, $id=0) {
        // Get icon settings with strong validation
        $icon_type = get_post_meta($item->ID, '_menu_item_icon_type', true);
        if (!in_array($icon_type, ['dashicon', 'upload'])) {
            $icon_type = 'dashicon'; // Default fallback
        }
        
        $dashicon = get_post_meta($item->ID, '_menu_item_dashicon', true);
        if (empty($dashicon)) {
            $dashicon = 'menu'; // Default fallback
        }
        
        $icon_url = get_post_meta($item->ID, '_menu_item_icon', true);
        
        $title = apply_filters('the_title', $item->title, $item->ID);
        $url = $item->url;
        $icon_html = '';

        if ($icon_type === 'dashicon') {
            $icon_html = sprintf(
                '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                esc_attr($dashicon)
            );
        } elseif ($icon_type === 'upload' && !empty($icon_url)) {
            if (substr($icon_url, -4) === '.svg') {
                $svg_content = distm_get_svg_content($icon_url);
                if ($svg_content !== false) {
                    $icon_html = $svg_content;
                } else {
                    // Fallback to dashicon if SVG fails
                    $icon_html = sprintf(
                        '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                        'menu'
                    );
                }
            } else {
                $icon_html = sprintf(
                    '<img src="%s" alt="%s %s" class="tm-menu-icon" />',
                    esc_url($icon_url),
                    esc_attr($title),
                    esc_attr__('Icon', 'the-menu')
                );
            }
        } else {
            // Default fallback to menu dashicon
            $icon_html = '<span class="dashicons dashicons-menu" aria-hidden="true"></span>';
        }

        // Get visibility settings
        $visibility = get_post_meta($item->ID, '_menu_item_visibility', true);
        if (empty($visibility)) {
            $visibility = 'everyone';
        }
        $roles = get_post_meta($item->ID, '_menu_item_roles', true);
        if (!is_array($roles)) {
            $roles = array();
        }

        // Check visibility permissions
        $show_item = false;
        if ($visibility === 'everyone') {
            $show_item = true;
        } elseif ($visibility === 'logged_in' && is_user_logged_in()) {
            if (empty($roles) || array_intersect($roles, wp_get_current_user()->roles)) {
                $show_item = true;
            }
        } elseif ($visibility === 'logged_out' && !is_user_logged_in()) {
            $show_item = true;
        }

        if ($show_item) {
            $output .= '<li class="tm-menu-item-' . esc_attr($item->ID) . '">';
            
            // Get the 'don't display menu text' setting
            $options = get_option('distm_settings');
            $hide_text = isset($options['distm_disable_menu_text']) && $options['distm_disable_menu_text'];

            $output .= '<a href="' . esc_url($url) . '">' . $icon_html;
            
            // Only add the title span if 'don't display menu text' is not enabled
            if (!$hide_text) {
                $output .= '<span class="tm-menu-item-title">' . esc_html($title) . '</span>';
            }
            
            $output .= '</a>';
        }
    }
}