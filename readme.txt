=== AVAK Header Footer Script Placer ===
Contributors: ajayrajbanshi
Tags: header, footer, scripts, custom code, tracking
Requires at least: 5.2
Tested up to: 6.8
Stable tag: 1.0.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enable placing custom code (HTML/JS/CSS) in the header, footer, and opening body section of your WordPress website.

== Description ==

AVAK Header Footer Script Placer allows you to easily insert custom HTML, JavaScript, and CSS code into your WordPress website's header, footer, and opening body sections.

**Features:**

* **Header Code Injection** - Insert code in the `<head>` section
* **Opening Body Code Injection** - Insert code right after the opening `<body>` tag
* **Footer Code Injection** - Insert code before the closing `</body>` tag
* **Syntax Highlighting** - Built-in code editor with syntax highlighting
* **User-Friendly Interface** - Clean and intuitive admin interface
* **Security** - Capability-based access control

**Perfect for adding:**

* Google Tag Manager, Google Analytics, Facebook Pixel, and other tracking codes
* Custom CSS for styling
* JavaScript libraries and custom scripts
* Meta tags for SEO
* Google Tag Manager (including noscript)
* Custom fonts
* Any other custom HTML/JS/CSS code

== Installation ==

1. Upload the `avak-header-footer` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Header Footer Scripts to configure the plugin

== Frequently Asked Questions ==

= Does this work with all themes? =

Yes, the header and footer code will work with all WordPress themes. The opening body code requires theme support for the `wp_body_open` action hook (WordPress 5.2+).

= Can I use PHP code? =

No, this plugin is designed for HTML, CSS, and JavaScript only. For security reasons, PHP code execution is not supported.

= What user permissions are required? =

Only administrators (users with 'manage_options' capability) can access and modify the scripts.

= Will this slow down my website? =

The plugin itself is very lightweight. However, the scripts you add may affect performance depending on their size and functionality.

= Can I use this with caching plugins? =

Yes, this plugin is compatible with most caching and optimization plugins.

= Is multisite supported? =

Yes, the plugin works with WordPress multisite installations.

== Screenshots ==

1. Admin settings page showing the three code editor sections
2. Header code editor with syntax highlighting

== Changelog ==

= 1.0.0 =
* Initial release
* Header code injection
* Footer code injection
* Opening body code injection
* Code editor with syntax highlighting
* Admin interface with proper security measures

== Upgrade Notice ==

= 1.0.0 =
Initial release of AVAK Header Footer Script Placer.

== Support ==

For bug reports and feature requests, please visit the plugin's support forum on WordPress.org.

== Privacy Policy ==

This plugin does not collect, store, or transmit any user data. Any code inserted by administrators is their responsibility.
