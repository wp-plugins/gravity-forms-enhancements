<?php
/**
 * This file is part of the Gravity Forms Enhancer plugin.
 *
 * Copyright (c) 2015 latorante.name
 *
 * For the full copyright and license information, please view
 * the GravityFormsEnhacner.php file in root directory of this plugin.
 */

if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')){ exit(); }

/**
 * Uninstall function
 */
function GravityFormsEnhancerUninstall()
{
    // Delete db option
    delete_option('GravityFormsEnhancer');
}

/**
 * Go Go Go!
 */

if (function_exists('is_multisite') && is_multisite()){
    global $wpdb;
    $blogs = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
    foreach ($blogs as $blog){
        switch_to_blog($blog);
        GravityFormsEnhancerUninstall();
        restore_current_blog();
    }
} else {
    GravityFormsEnhancerUninstall();
}