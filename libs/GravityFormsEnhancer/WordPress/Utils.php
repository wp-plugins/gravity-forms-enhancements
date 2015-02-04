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

/**
 * Class Utils
 * @package GravityFormsEnhancer\WordPress
 * @author Martin Picha (http://latorante.name)
 */
class Utils
{
    /**
     * For our porpuses, we just need to add API key to each call
     *
     * @param $url
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function addQueryParam($url, $key, $value = NULL){ return add_query_arg($key, $value, $url); }


    /**
     * Add query params, in array
     *
     * @param $url
     * @param array $params
     * @return mixed
     */
    public static function addQueryParams($url, array $params = array()){ return add_query_arg($params, $url); }


    /**
     * Remove query parameter
     *
     * @param $url
     * @param $key
     * @return mixed
     */
    public static function removeQueryParam($url, $key){ return remove_query_arg($key, $url); }


    /**
     * Gets real server lastQuery
     *
     * @return string
     */
    public static function getRealUrl()
    {
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
        $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
        $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
        return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
    }
}