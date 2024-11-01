=== The Menu - Custom mobile navigation with icons ===
Contributors: ryanwiggilldistinct
Tags: mobile-navigation, navigation-menu, mobile-menu, menu-icons, custom-menu
Requires at least: 6.0
Tested up to: 6.6.2
Stable tag: 1.2.10
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Icon: assets/icon-256x256.png
Banner: assets/banner-1544x500.png

Create beautiful mobile navigation menus with custom icons, role-based visibility, and extensive style options for your WordPress site.

== Description ==

Transform your WordPress site's mobile navigation with The Menu - a powerful mobile-friendly navigation solution featuring custom icons, role-based visibility, and extensive customisation options. Perfect for creating professional bottom navigation bars, sticky menus, and app-like mobile experiences. This responsive menu plugin offers SVG icon support, smooth animations, and seamless integration with popular themes and page builders.
The Menu plugin is designed to enhance your website's usability and aesthetic appeal, providing a navigation experience that's both visually stunning and functionally robust. Whether you're building a mobile-first website, an online shop, or a Progressive Web App (PWA), The Menu adapts to your needs.

= Core features =

- Mobile-optimised bottom navigation bar
- Custom SVG icon support and built-in Dashicons
- Extensive colour customisation options
- Transparent menus that adapt on scroll
- Role-based menu item visibility
- Additional "add-on" menu for extra navigation items
- App-like mobile navigation experience
- Smooth animations and transitions
- Compatible with popular page builders
- PWA-friendly design
- Responsive and touch-optimised
- Custom icon upload support
- Page-specific menu visibility
- Multiple menu styles (Pill, Rounded, Flat)

= Perfect for =

- Mobile-first websites
- Online shops
- Restaurant menus
- Progressive Web Apps
- Service businesses
- Portfolio sites
- Business directories
- Event websites
- Community platforms
- Educational sites

= Professional customisation =
The Menu offers extensive customisation options to match your brand:

- Custom colours for icons and backgrounds
- Multiple menu styles and layouts
- Flexible positioning options
- Icon-only or icon-with-text display
- Transparent scroll effects
- Custom icon uploads
- Built-in Dashicons support

= Advanced features =

- Role-based visibility: Show different menu items to different user roles
- Page exclusions: Choose which pages show or hide the menu
- Add-on menu: Additional menu space for extra navigation items
- Mobile-only option: Display the menu only on mobile devices
- Loading animations: Smooth transitions between pages
- SVG colour controls: Customise icon colours directly
- Multiple menu positions: Left, right, and add-on menu locations
- Transparent effects: Menu adapts as users scroll

= Compatible with popular tools =
Works seamlessly with:

- WooCommerce
- Elementor
- Divi Builder
- Gutenberg
- Popular WordPress themes
- Custom theme frameworks
- Other navigation plugins

== Installation ==

- Upload the plugin files to the /wp-content/plugins/the-menu directory, or install the plugin through the WordPress plugins screen directly
- Activate the plugin through the 'Plugins' screen in WordPress
- Navigate to 'The Menu' in your admin sidebar to configure the plugin
- Set up your menus under 'Appearance > Menus'

= Quick start guide =

- Create a new menu in WordPress
- Assign it to one of The Menu's locations (Left, Right, or Add-on)
- Add icons to your menu items
- Customise colours and styles
- Configure visibility settings
- Test on mobile devices

== Frequently Asked Questions ==
= How do I add icons to menu items? =
You can add icons in two ways:

- Upload custom SVG icons through the menu item settings
- Select from the built-in Dashicons library
- Each menu item has these options when editing in the WordPress admin area.

= Can I control who sees certain menu items? =
Yes, you have granular control over menu item visibility:

- Show to everyone
- Show only to logged-in users
- Show only to logged-out users
- Show to specific user roles
- Hide on specific pages

= Is the menu responsive? =
Yes, The Menu is fully responsive and mobile-optimised. You can:

- Set it to appear only on mobile devices
- Configure different styles for various screen sizes
- Optimise for both portrait and landscape orientations

= Will it work with my theme? =
The Menu is designed to work with any properly coded WordPress theme. It's been tested with:

- Popular theme frameworks
- Custom themes
- WooCommerce themes
- Page builder themes

= Can I customise the colours? =
Yes, you have complete control over:

- Background colours
- Icon colours
- Text colours
- Featured item colours
- Add-on menu colours
- Transparency effects

= Does it support touch gestures? =
Yes, The Menu is optimised for touch devices with:

- Smooth touch interactions
- Swipe support
- Touch-friendly hit areas
- Mobile-optimised animations

== Screenshots ==

1. The Menu by Distinct - Mobile navigation made beautiful
2. Highly customisable interface with multiple style options
3. Add-on menu for extra navigation items
4. Perfect companion for Progressive Web Apps
5. Seamless integration with WordPress menu editor
6. Easy-to-use customisation options

== Changelog ==

= 1.2.10 - 29/10/2024 =

- [HOTFIX] Dashicon and menu styling was a little off on some sites
- Now allows scrolling in the addon menu

= 1.2.9 - 29/10/2024 =

- [NOTE] Please check your site once updated, the new dashicons functionallity may override current uploaded icons, which means you may need to re-select them.
- Added default dashicons to select for menu icons incase you don't have svg icons to upload.
- Fixed icon colours in the add-on menu, it was changing paths colours only and now it looks for 'circle', 'rect', etc. as well.
- Add-on menu items weren't lining up nicely when link text wrapping varied, this has been fixed so that they line up.
- When trying to set the visibility of a menu item to logged in users you had to save the menu and then open the menu item again to be able to select roles, this has now been updated to show immediately.

= 1.2.7 - 23/10/2024 =

- Added loading of default colours if no settings are set on plugin activation
- Added delete data on uninstall option. If checked, all plugin data and settings will be removed when deleting the plugin from the plugins page. This will not affect data if you remove files via FTP or deactivate the plugin.
- Better handling of plugin activation and deactivation
- FIXED: On some sites when a featured icon was not set it could cause a fatal error.

= 1.2.6 - 21/10/2024 =

- FIXED: There was an issue where excluded pages weren't being saved, this has been resolved.
- Updated UI branding to match the wordpress.org plugin page branding

= 1.2.5 - 19/10/2024 =

- FIXED: Media library wouldn't open when trying to upload a featured icon

= 1.2.4 - 19/10/2024 =

- Improved the overall security and robustness of the plugin.
- Updated the contributors of the plugin.

= 1.2.3 - 18/10/2024 =

- Improved the overall security and robustness of the plugin by ensuring proper sanitisation of user input and escaping of output. The functionality of the plugin remains the same, but it's now more resilient against potential security issues.

= 1.2.2 - 07/08/2024 =
This version fixes a security related bug.

= 1.2.1 - 04/08/2024 =

- WP version check
- Fixed an issue where if you disabled transparency on scroll it was still trying to detect scroll and was showing errors in console every time you scrolled

= 1.2.0 - 08/07/2024 =

- Added the ability to select who can see certain menu items (Everyone, Logged out users, or Logged in users) and if Logged in users is selected you are able to chose which roles can see the menu item.

== Terms of use ==

By installing and activating The Menu plugin, you agree to the following terms:

1. Licence and usage

   The Menu is licensed under GPL-2.0-or-later. You are free to use, modify, and distribute this plugin in accordance with the terms of this licence.

2. Third-party services

   This plugin communicates with https://plugins.distinct.africa for the following purposes:

   a. Licence validation: To verify the authenticity of your licence key.
   b. Usage statistics: To count the number of active installations of our plugin.
   c. Version checking: To ensure your plugin is up-to-date.

   Data transmitted:
   - Your website's domain name
   - The plugin version you're using
   - Your licence key if you have one

   No personal data (such as email addresses or user information) is collected or transmitted.

   This check occurs once every 12 hours to minimise server load.

3. Future use

   In upcoming versions, this service may be used to unlock premium features for licensed users.

4. Data usage and privacy

   We are committed to protecting your privacy. The data collected is used solely for the purposes stated above and is not shared with any third parties.

5. Disclaimer of warranty

   The Menu plugin is provided "as is" without warranty of any kind, either expressed or implied, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose.

6. Limitation of liability

   In no event shall the authors or copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the plugin or the use or other dealings in the plugin.

7. Support and updates

   While we strive to provide regular updates and support, we reserve the right to discontinue or modify any part of the service at any time.

8. Changes to terms

   We reserve the right to modify these terms at any time. Continued use of the plugin after any such changes shall constitute your consent to such changes.

By using The Menu plugin, you acknowledge that you have read, understood, and agree to be bound by these Terms of Use. If you do not agree to these terms, please uninstall and stop using the plugin.

For any questions or concerns regarding these terms, please contact us at hello@distinct.africa.

Last updated: 18/10/2024