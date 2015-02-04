<?php
/**
 * This file is part of the Gravity Forms Enhancer plugin.
 *
 * Copyright (c) 2015 latorante.name
 *
 * For the full copyright and license information, please view
 * the GravityFormsEnhacner.php file in root directory of this plugin.
 */

use GravityFormsEnhancer\Admin;
use GravityFormsEnhancer\Frontend;
use GravityFormsEnhancer\RepositorySettings;

/**
 * Class GravityFormsEnhancerInit
 * @author Martin Picha (http://latorante.name)
 */
class GravityFormsEnhancerInit
{
    /** @var \GravityFormsEnhancer\RepositorySettings */
    var $repo;

    /**
     * Constructor, does all this beautiful magic, loads all libs
     * registers all sorts of funky hooks, checks stuff and so on.
     */
    public function __construct()
    {
        // Cosntants define
        define('GFENHANCER_KEY',     'gravity-forms-enhancements');
        define('GFENHANCER_FILE',    'gravity-forms-enhancements/GravityFormsEnhacner.php');
        define('GFENHANCER_HOME_URL',get_option('siteurl'));
        define('GFENHANCER_FOLDER',  plugins_url(NULL, __FILE__));
        define('GFENHANCER_ASSETS',  GFENHANCER_FOLDER . '/assets/');
        define('GFENHANCER_ROOT',    dirname(__FILE__) . DIRECTORY_SEPARATOR);
        define('GFENHANCER_VER',     '1.2');
        // Start the engine last file to require, rest is auto
        // Custom auto loader, PSR-0 Standard
        require_once('GravityFormsRobotLoader.php');
        $classLoader = new GravityFormsEnhancerRobotLoader('GravityFormsEnhancer', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR);
        $classLoader->register();
        // Repository
        $this->repo = new \GravityFormsEnhancer\EnhancerSettings();
        // Loaded Plugin
        add_action('plugins_loaded', array($this, 'run'));
    }


    /**
     * Run app
     * @return Admin|Frontend
     */
    public function run()
    {
        if(is_admin()){
            return new Admin($this->repo);
        }
        return new Frontend($this->repo);
    }

    public static function activate(){}

}

$gravityFormsEnhancerInit = new GravityFormsEnhancerInit();