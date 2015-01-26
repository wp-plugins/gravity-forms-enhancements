<?php
/*
    Plugin Name: Gravity Forms Enhancements
    Description: Simple library to enhance the Gravity Forms experience, build on a necessity and desire not to amend Gravity Forms core files.
    Author: latorante
    Author URI: http://latorante.name
    Author Email: martin@latorante.name
    Version: 1.0
    License: GPLv2
*/

/**
 * This file is part of the Gravity Forms Enhancer plugin.
 *
 * Copyright (c) 2015 latorante.name
 *
 * For the full copyright and license information, please view
 * the GravityFormsEnhacner.php file in root directory of this plugin.
 */

/**
 * 1. If no Wordpress, go home
 */

if (!defined('ABSPATH')) { exit; }

/**
 * 2. Check minimum requirements (wp version, php version)
 * Reason behind this is, we just need PHP 5.3.1 at least,
 * and wordpress 3.3 or higher. We just can't run the show
 * on some outdated installation.
 */

require_once('GravityFormsEnhancerCheck.php');
GravityFormsEnhancerCheck::checkRequirements();

/**
 * 3. Activation / deactivation
 */

register_activation_hook(__FILE__, array('GravityFormsEnhancerInit', 'activate'));

/**
 * 4. Go, and do Enhacements!
 */

require_once('GravityFormsEnhacnerInit.php');