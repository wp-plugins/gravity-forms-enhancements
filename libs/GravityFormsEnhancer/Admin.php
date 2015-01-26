<?php
/**
 * This file is part of the Gravity Forms Enhancer plugin.
 *
 * Copyright (c) 2015 latorante.name
 *
 * For the full copyright and license information, please view
 * the GravityFormsEnhacner.php file in root directory of this plugin.
 */

namespace GravityFormsEnhancer;

use GravityFormsEnhancer\WordPress\Utils;
use GravityFormsEnhancer\WordPress\Settings;
use GravityFormsEnhancer\WordPress\Notice;
use GravityFormsEnhancer\RepositorySettings;
use GravityFormsEnhancer\Page\PageSettings;
use GravityFormsEnhancer\Utils\Strings;


class Admin
{
    /** @var array Admin Messages */
    var $notices = array();
    /** @var \GravityFormsEnhancer\EnhancerSettings  */
    var $repositorySettings;
    /** @var \GravityFormsEnhancer\PageSettings */
    var $pageSettings;


    /**
     * @param \GravityFormsEnhancer\EnhancerSettings $repositorySettings
     */
    public function __construct(\GravityFormsEnhancer\EnhancerSettings $repositorySettings)
    {
        // Settings
        $this->repositorySettings = $repositorySettings;
        // Page Settings
        $this->pageSettings = new PageSettings($this->repositorySettings);
        // Admin
        add_action('current_screen', array($this, 'adminCurrentScreen'));
        add_action('admin_init',    array($this, 'adminInit'));
        add_action('admin_menu',    array($this, 'adminMenu'), 11);
        add_action('admin_notices', array ($this, 'adminNotices'));
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueueScripts'));
    }


    /**
     * Add Stylesheet
     */
    public function adminEnqueueScripts()
    {
        wp_register_style('gravityFormsEnhancerAdmin', GFENHANCER_ASSETS . 'GravityFormsEnhancerAdmin.css', false, md5(GFENHANCER_VER));
        wp_enqueue_style('gravityFormsEnhancerAdmin');
    }


    /**
     * @param $currentScreen
     */
    public function adminCurrentScreen($currentScreen)
    {
        if($currentScreen && $currentScreen->id == 'forms_page_GravityFormsEnhancer'){
            // Not now
        }
    }


    /**
     * Admin Init
     */
    public function adminInit()
    {
        /**
         * 1. Plugin meta links
         */
        add_filter('plugin_action_links',   array($this, 'pluginLinks'), 10, 2);
        add_filter('plugin_row_meta',       array($this, 'pluginMeta'), 10, 2);
    }


    /**
     * Admin Menu
     */
    public function adminMenu()
    {
        // Admin Pages
        add_submenu_page('gf_edit_forms', 'Enhancements', 'Enhancements', 'manage_options', 'GravityFormsEnhancer', array($this, 'renderSettings'));
    }


    /**
     * Renders Settings
     */
    public function renderSettings()
    {
        $this->pageSettings->render();
    }


    /**
     * Plugin action links
     *
     * @param $links
     * @param $file
     * @return mixed
     */
    public function pluginLinks($links, $file)
    {
        if ($file == GFENHANCER_FILE){
            array_push($links, '<a href="' . admin_url('admin.php?page=GravityFormsEnhancer') . '">Enhancements</a>');
        }
        return $links;
    }


    /**
     * Plugin Meta Links
     *
     * @param $links
     * @param $file
     * @return mixed
     */
    public function pluginMeta($links, $file)
    {
        if ($file == GFENHANCER_FILE){
            $ratePlugin = '<a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/gravity-forms-enhancer/">Rate this plugin</a>';
            $donatePlugin = '<a target="_blank" href="http://donate.latorante.name/">Donate</a>';
            array_push($links, $ratePlugin, $donatePlugin);
        }
        return $links;
    }


    /**
     * Adds notice to the array of notices
     *
     * @param string $tag
     * @param string $label
     */
    public function addNotice($tag = 'updated', $label = ''){ $this->notices[] = array($tag, $label); }


    /**
     * Returns all notices
     *
     * @return array
     */
    public function getNotices(){ return $this->notices; }


    /**
     * Sends notices to renderer
     */
    public function adminNotices()
    {
        // notices saved in db
        $savedNotices = $this->repositorySettings->getSavedNotices();
        if($savedNotices){
            foreach($savedNotices as $value){
                if(array_key_exists('error', $value)){
                    $this->displayAdminNotice('error', $value['error']);
                } elseif(array_key_exists('updated', $value)){
                    $this->displayAdminNotice('updated', $value['updated']);
                }
                // flush notices after display
                $this->repositorySettings->flushSavedNotices();
            }
        }
        // notices saved in this object
        foreach($this->notices as $key => $value){
            $this->displayAdminNotice($value[0], $value[1]);
        }
    }


    /**
     * Display admin notices
     *
     * @param null $class
     * @param null $text
     */
    private function displayAdminNotice($class = NULL, $text = NULL){ echo Notice::type($class)->text($text); }
}