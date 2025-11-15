<?php
/**
 * Uninstall script for AVAK Header Footer Script Placer
 *
 * This file is executed when the plugin is deleted via the WordPress admin.
 * It removes all plugin data from the database.
 *
 * @package AVAK_Header_Footer
 */

// Exit if accessed directly or not uninstalling
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Security check
if (!current_user_can('activate_plugins')) {
    exit;
}

// Delete all plugin options
delete_option('avak_hf_header_code');
delete_option('avak_hf_footer_code');
delete_option('avak_hf_body_open_code');

// For multisite installations
if (is_multisite()) {
    global $wpdb;

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query necessary for uninstall across all sites in multisite. Caching not needed as this runs once during plugin deletion.
    $avak_hf_blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
    $avak_hf_original_blog_id = get_current_blog_id();

    foreach ($avak_hf_blog_ids as $avak_hf_blog_id) {
        switch_to_blog($avak_hf_blog_id);

        delete_option('avak_hf_header_code');
        delete_option('avak_hf_footer_code');
        delete_option('avak_hf_body_open_code');
    }

    switch_to_blog($avak_hf_original_blog_id);
}

// Clear any cached data
wp_cache_flush();
