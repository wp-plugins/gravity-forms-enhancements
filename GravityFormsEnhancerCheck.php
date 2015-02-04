<?php
/**
 * This file is part of the Gravity Forms Enhancer plugin.
 *
 * Copyright (c) 2015 latorante.name
 *
 * For the full copyright and license information, please view
 * the GravityFormsEnhacner.php file in root directory of this plugin.
 */

/**
 * Class GravityFormsEnhancerCheck
 * @author Martin Picha (http://latorante.name)
 */
class GravityFormsEnhancerCheck
{
    /**
     * Let's check Wordpress version, and PHP version, PHP memory limit and tell those
     * guys whats needed to upgrade, if anything.
     */
    public static function checkRequirements()
    {
        // get vars
        global $wp_version;
        $memoryLimit = GravityFormsEnhancerCheck::getMemoryLimit();
        // minimum versions
        $checkMinWp  = '3.3';
        $checkMinPHP = '5.3';
        $checkMinMemory = 60 * (1024 * 1024);
        // recover hideLink
        $recoverLink = '<br /><br /><a href="'. admin_url('plugins.php') .'">' . __('Back to plugins.', 'genoo') . '</a>';
        if(!function_exists('is_plugin_active')){
            // If this function doesn't exists, we need to include this file.
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        // Check WordPress version
        if (!version_compare($wp_version, $checkMinWp, '>=')){
            GravityFormsEnhancerCheck::deactivatePlugin(
                sprintf(__('We re really sorry, but <strong>Gravity Forms Enhancer</strong> requires at least WordPress varsion <strong>%1$s or higher.</strong> You are currently using <strong>%2$s.</strong> Please upgrade your WordPress.', 'gfenhancer'), $checkMinWp, $wp_version) . $recoverLink
            );
        // Check PHP version
        } elseif (!version_compare(PHP_VERSION, $checkMinPHP, '>=')){
            GravityFormsEnhancerCheck::deactivatePlugin(
                sprintf(__('We re really sorry, but you need PHP version at least <strong>%1$s</strong> to run <strong>Gravity Forms Enhancer.</strong> You are currently using PHP version <strong>%2$s</strong>', 'gfenhancer'),  $checkMinPHP, PHP_VERSION) . $recoverLink
            );
        // Check PHP Memory Limit
        } elseif(!version_compare($memoryLimit, $checkMinMemory, '>=')) {
            $memoryLimitReadable = GravityFormsEnhancerCheck::getReadebleBytes($memoryLimit);
            $minMemoryLimitReadable = GravityFormsEnhancerCheck::getReadebleBytes($checkMinMemory);
            GravityFormsEnhancerCheck::deactivatePlugin(
                sprintf(__('We re really sorry, but to run <strong>Gravity Forms Enhancer</strong> properly you need at least <strong>%1$s</strong> of PHP memory. You currently have <strong>%2$s</strong>', 'gfenhancer'), $minMemoryLimitReadable, $memoryLimitReadable) . $recoverLink
            );
        // We do need DOM Document class ...
        } elseif(!class_exists('DOMDocument')){
            GravityFormsEnhancerCheck::deactivatePlugin(
                sprintf(__('We re really sorry, but to run <strong>Gravity Forms Enhancer</strong> you need <strong>DOMDocument</strong> class present in your PHP installation.', 'gfenhancer'), $minMemoryLimitReadable, $memoryLimitReadable) . $recoverLink
            );
        // No, we can't really enhance any forms without Gravity Forms plugin, right?
        } elseif(!is_plugin_active('gravityforms/gravityforms.php')){
            GravityFormsEnhancerCheck::deactivatePlugin(
                sprintf(__('We re really sorry, but to run <strong>Gravity Forms Enhancer</strong> properly you do need to have <strong>Gravity Forms</strong> plugin installed and <strong>activated</strong>.', 'gfenhancer')) . $recoverLink
            );
        }
    }


    /**
     * Get Memory Limit
     *
     * @return int|string
     */
    public static function getMemoryLimit(){ return GravityFormsEnhancerCheck::getBytes(ini_get('memory_limit')); }


    /**
     * Ini get value in bytes, helper for get memory limit.
     *
     * @param $val
     * @return int|string
     */
    public static function getBytes($val)
    {
        // if no value, it's zero
        if(empty($val))return 0;
        // swap around
        switch (substr ($val, -1))
        {
            case 'M': case 'm': return (int)$val * 1048576;
            case 'K': case 'k': return (int)$val * 1024;
            case 'G': case 'g': return (int)$val * 1073741824;
            default: return $val;
        }
    }


    /**
     * Readable human format when low memory
     *
     * @param $bytes
     * @param int $precision
     * @return string
     */
    public static function getReadebleBytes($bytes, $precision = 2)
    {
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;
        if (($bytes >= 0) && ($bytes < $kilobyte)){
            return $bytes . ' B';
        } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)){
            return round($bytes / $kilobyte, $precision) . ' KB';
        } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
            return round($bytes / $megabyte, $precision) . ' MB';
        } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
            return round($bytes / $gigabyte, $precision) . ' GB';
        } elseif ($bytes >= $terabyte){
            return round($bytes / $terabyte, $precision) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }


    /**
     * Deactivates our plugin if anything goes wrong. Also, removes the
     * "Plugin activated" message, if we don't pass requriments check.
     */
    public static function deactivatePlugin($message)
    {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        deactivate_plugins('gravity-forms-enhancements/GravityFormsEnhacner.php');
        unset($_GET['activate']);
        wp_die($message);
        exit();
    }
}