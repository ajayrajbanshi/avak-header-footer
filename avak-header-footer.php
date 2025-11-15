<?php
/**
 * Plugin Name: AVAK Header Footer Script Placer
 * Plugin URI: https://github.com/ajayrajbanshi/avak-header-footer
 * Description: Enable placing code (HTML/JS/CSS) on Header, Footer and Opening body section
 * Version: 1.0.1
 * Author: Ajay Rajbanshi
 * Author URI: https://www.ajayrajbanshi.com.np
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: avak-header-footer-script-placer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
if (!defined('AVAK_HF_VERSION')) {
    define('AVAK_HF_VERSION', '1.0.0');
}
if (!defined('AVAK_HF_PLUGIN_DIR')) {
    define('AVAK_HF_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('AVAK_HF_PLUGIN_URL')) {
    define('AVAK_HF_PLUGIN_URL', plugin_dir_url(__FILE__));
}

/**
 * Main plugin class
 */
class AVAK_Header_Footer {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_head', array($this, 'insert_header_code'));
        add_action('wp_footer', array($this, 'insert_footer_code'));
        add_action('wp_body_open', array($this, 'insert_body_open_code'));

        // Fallback for themes that don't support wp_body_open
        add_action('wp_footer', array($this, 'insert_body_open_code_fallback'), 1);

        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_notices', array($this, 'show_theme_compatibility_notice'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('AVAK Header Footer Settings', 'avak-header-footer-script-placer'),
            __('Header Footer Scripts', 'avak-header-footer-script-placer'),
            'manage_options',
            'avak-header-footer',
            array($this, 'settings_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('avak_hf_settings', 'avak_hf_header_code', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_code'),
            'default' => '',
            'show_in_rest' => false
        ));

        register_setting('avak_hf_settings', 'avak_hf_footer_code', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_code'),
            'default' => '',
            'show_in_rest' => false
        ));

        register_setting('avak_hf_settings', 'avak_hf_body_open_code', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_code'),
            'default' => '',
            'show_in_rest' => false
        ));
    }

    /**
     * Sanitize code input
     *
     * @param string $code The code to sanitize
     * @return string Sanitized code
     */
    public function sanitize_code($code) {
        // Verify user capability
        if (!current_user_can('unfiltered_html')) {
            // For users without unfiltered_html capability, use wp_kses_post
            return wp_kses_post($code);
        }

        // For administrators and users with unfiltered_html capability
        // Return code as-is, but ensure it's a string
        return is_string($code) ? $code : '';
    }

    /**
     * Insert header code
     */
    public function insert_header_code() {
        $header_code = get_option('avak_hf_header_code', '');
        if (!empty($header_code)) {
            echo "\n<!-- AVAK Header Code -->\n";
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- This is intentional. Code is sanitized on save and requires unfiltered_html capability.
            echo $header_code;
            echo "\n<!-- /AVAK Header Code -->\n";
        }
    }

    /**
     * Insert footer code
     */
    public function insert_footer_code() {
        $footer_code = get_option('avak_hf_footer_code', '');
        if (!empty($footer_code)) {
            echo "\n<!-- AVAK Footer Code -->\n";
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- This is intentional. Code is sanitized on save and requires unfiltered_html capability.
            echo $footer_code;
            echo "\n<!-- /AVAK Footer Code -->\n";
        }
    }

    /**
     * Insert body open code
     */
    public function insert_body_open_code() {
        $body_open_code = get_option('avak_hf_body_open_code', '');
        if (!empty($body_open_code)) {
            echo "\n<!-- AVAK Body Open Code -->\n";
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- This is intentional. Code is sanitized on save and requires unfiltered_html capability.
            echo $body_open_code;
            echo "\n<!-- /AVAK Body Open Code -->\n";

            // Set flag that wp_body_open was executed
            if (!defined('AVAK_HF_BODY_OPEN_EXECUTED')) {
                define('AVAK_HF_BODY_OPEN_EXECUTED', true);
            }
        }
    }

    /**
     * Fallback for themes that don't support wp_body_open
     * This will execute in footer if wp_body_open hook was never called
     */
    public function insert_body_open_code_fallback() {
        // Only execute if wp_body_open was not called
        if (!defined('AVAK_HF_BODY_OPEN_EXECUTED')) {
            $body_open_code = get_option('avak_hf_body_open_code', '');
            if (!empty($body_open_code)) {
                echo "\n<!-- AVAK Body Open Code (Fallback) -->\n";
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- This is intentional. Code is sanitized on save and requires unfiltered_html capability.
                echo $body_open_code;
                echo "\n<!-- /AVAK Body Open Code (Fallback) -->\n";
            }
        }
    }

    /**
     * Show theme compatibility notice if wp_body_open is not supported
     */
    public function show_theme_compatibility_notice() {
        $screen = get_current_screen();
        if ($screen->id !== 'settings_page_avak-header-footer') {
            return;
        }

        $body_open_code = get_option('avak_hf_body_open_code', '');
        if (empty($body_open_code)) {
            return;
        }

        // Check if theme supports wp_body_open by checking if action exists
        if (!did_action('wp_body_open') && current_theme_supports('wp_body_open') === false) {
            ?>
            <div class="notice notice-warning">
                <p>
                    <strong><?php esc_html_e('Theme Compatibility Notice:', 'avak-header-footer-script-placer'); ?></strong>
                    <?php esc_html_e('Your current theme may not support the wp_body_open hook. The "Opening Body Code" will be inserted as a fallback in the footer section instead. For optimal placement, consider updating your theme or manually adding wp_body_open() after the <body> tag in your theme\'s header.php file.', 'avak-header-footer-script-placer'); ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'settings_page_avak-header-footer') {
            return;
        }

        wp_enqueue_code_editor(array('type' => 'text/html'));
        wp_enqueue_style(
            'avak-hf-admin',
            AVAK_HF_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            AVAK_HF_VERSION
        );
        wp_enqueue_script(
            'avak-hf-admin',
            AVAK_HF_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'code-editor'),
            AVAK_HF_VERSION,
            true
        );
    }

    /**
     * Settings page
     */
    public function settings_page() {
        // Double-check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(
                esc_html__('You do not have sufficient permissions to access this page.', 'avak-header-footer-script-placer'),
                esc_html__('Permission Denied', 'avak-header-footer-script-placer'),
                array('response' => 403)
            );
        }

        // Save settings with proper validation
        if (isset($_POST['avak_hf_save'])) {
            // Verify nonce
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verification doesn't require sanitization
            if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(wp_unslash($_POST['_wpnonce']), 'avak_hf_settings_nonce')) {
                wp_die(
                    esc_html__('Security check failed. Please try again.', 'avak-header-footer-script-placer'),
                    esc_html__('Security Error', 'avak-header-footer-script-placer'),
                    array('response' => 403)
                );
            }

            // Sanitize and save each field
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized below via sanitize_code method
            $header_code = isset($_POST['avak_hf_header_code']) ? wp_unslash($_POST['avak_hf_header_code']) : '';
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized below via sanitize_code method
            $footer_code = isset($_POST['avak_hf_footer_code']) ? wp_unslash($_POST['avak_hf_footer_code']) : '';
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized below via sanitize_code method
            $body_open_code = isset($_POST['avak_hf_body_open_code']) ? wp_unslash($_POST['avak_hf_body_open_code']) : '';

            // Apply sanitization
            $header_code = $this->sanitize_code($header_code);
            $footer_code = $this->sanitize_code($footer_code);
            $body_open_code = $this->sanitize_code($body_open_code);

            // Update options
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- Using WordPress Options API, not direct DB queries
            update_option('avak_hf_header_code', $header_code, false);
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- Using WordPress Options API, not direct DB queries
            update_option('avak_hf_footer_code', $footer_code, false);
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- Using WordPress Options API, not direct DB queries
            update_option('avak_hf_body_open_code', $body_open_code, false);

            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved successfully!', 'avak-header-footer-script-placer') . '</p></div>';
        }

        $header_code = get_option('avak_hf_header_code', '');
        $footer_code = get_option('avak_hf_footer_code', '');
        $body_open_code = get_option('avak_hf_body_open_code', '');

        ?>
        <div class="wrap avak-hf-settings">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="avak-hf-container">
                <form method="post" action="">
                    <?php wp_nonce_field('avak_hf_settings_nonce'); ?>

                    <div class="avak-hf-section">
                        <h2><?php esc_html_e('Header Code', 'avak-header-footer-script-placer'); ?></h2>
                        <p class="description">
                            <?php esc_html_e('This code will be inserted in the <head> section of your website.', 'avak-header-footer-script-placer'); ?>
                        </p>
                        <textarea
                            name="avak_hf_header_code"
                            id="avak_hf_header_code"
                            class="avak-hf-code-editor"
                            rows="10"
                        ><?php echo esc_textarea($header_code); ?></textarea>
                    </div>

                    <div class="avak-hf-section">
                        <h2><?php esc_html_e('Opening Body Code', 'avak-header-footer-script-placer'); ?></h2>
                        <p class="description">
                            <?php esc_html_e('This code will be inserted right after the opening <body> tag.', 'avak-header-footer-script-placer'); ?>
                        </p>
                        <textarea
                            name="avak_hf_body_open_code"
                            id="avak_hf_body_open_code"
                            class="avak-hf-code-editor"
                            rows="10"
                        ><?php echo esc_textarea($body_open_code); ?></textarea>
                    </div>

                    <div class="avak-hf-section">
                        <h2><?php esc_html_e('Footer Code', 'avak-header-footer-script-placer'); ?></h2>
                        <p class="description">
                            <?php esc_html_e('This code will be inserted before the closing </body> tag.', 'avak-header-footer-script-placer'); ?>
                        </p>
                        <textarea
                            name="avak_hf_footer_code"
                            id="avak_hf_footer_code"
                            class="avak-hf-code-editor"
                            rows="10"
                        ><?php echo esc_textarea($footer_code); ?></textarea>
                    </div>

                    <div class="avak-hf-submit">
                        <?php submit_button(__('Save Changes', 'avak-header-footer-script-placer'), 'primary', 'avak_hf_save', false); ?>
                    </div>
                </form>
            </div>

            <div class="avak-hf-info">
                <h3><?php esc_html_e('Usage Examples', 'avak-header-footer-script-placer'); ?></h3>
                <ul>
                    <li><strong><?php esc_html_e('Header Code:', 'avak-header-footer-script-placer'); ?></strong> <?php esc_html_e('Meta tags, custom CSS, analytics scripts', 'avak-header-footer-script-placer'); ?></li>
                    <li><strong><?php esc_html_e('Opening Body Code:', 'avak-header-footer-script-placer'); ?></strong> <?php esc_html_e('Google Tag Manager (noscript), conversion tracking', 'avak-header-footer-script-placer'); ?></li>
                    <li><strong><?php esc_html_e('Footer Code:', 'avak-header-footer-script-placer'); ?></strong> <?php esc_html_e('JavaScript libraries, deferred scripts, chat widgets', 'avak-header-footer-script-placer'); ?></li>
                </ul>

                <h3><?php esc_html_e('Best Practices', 'avak-header-footer-script-placer'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Always test code in a staging environment first', 'avak-header-footer-script-placer'); ?></li>
                    <li><?php esc_html_e('Keep code organized with comments for easy maintenance', 'avak-header-footer-script-placer'); ?></li>
                    <li><?php esc_html_e('Minify CSS and JavaScript for better performance', 'avak-header-footer-script-placer'); ?></li>
                    <li><?php esc_html_e('Use async or defer attributes for non-critical scripts', 'avak-header-footer-script-placer'); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }
}

// Initialize the plugin
new AVAK_Header_Footer();
