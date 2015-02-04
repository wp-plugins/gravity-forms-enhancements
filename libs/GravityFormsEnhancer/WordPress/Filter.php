<?php
/**
 * This file is part of the Gravity Forms Enhancer plugin.
 *
 * Copyright (c) 2015 latorante.name
 *
 * For the full copyright and license information, please view
 * the GravityFormsEnhacner.php file in root directory of this plugin.
 */

namespace GravityFormsEnhancer\WordPress;

use GravityFormsEnhancer\Utils\Strings;

/**
 * Class Filter
 * @package GravityFormsEnhancer\WordPress
 * @author Martin Picha (http://latorante.name)
 */
class Filter
{
    /**
     * Add filter
     *
     * @param $tag
     * @param $f
     * @param int $p
     * @param null $args
     */
    public static function add($tag, $f, $p = 10, $args = NULL)
    {
        add_filter($tag, $f, $p, $args);
    }


    /**
     * Remove filter
     *
     * @param $tag
     * @param $f
     * @param int $p
     */
    public static function remove($tag, $f, $p = 10)
    {
        remove_filter($tag, $f, $p);
    }
}