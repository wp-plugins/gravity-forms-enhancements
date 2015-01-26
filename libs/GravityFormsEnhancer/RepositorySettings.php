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

use GravityFormsEnhancer\Utils\Strings;


abstract class RepositorySettings
{
    /** general - used only by plugin calls */
    const KEY_GENERAL = GFENHANCER_KEY;
    /** @var get_option key */
    var $key;


    /**
     * Constructor
     */
    public function __construct(){ $this->key = GFENHANCER_KEY; }


    /**
     * Get the value of a settings field
     *
     * @param string  $option  settings field name
     * @param string  $section the section name this field belongs to
     * @param string  $default default text if it's not found
     * @return string
     */
    public function getOption($option, $section, $default = '')
    {
        $options = get_option($section);
        if (isset($options[$option])){
            return $options[$option];
        }
        return $default;
    }


    /**
     * Get options namespace
     *
     * @param $namespace
     * @return mixed
     */
    public function getOptions($namespace){ return get_option($namespace); }


    /**
     * Set option
     *
     * @param $option
     * @param $value
     * @return mixed
     */
    public function setOption($option, $value){ return update_option($option, $value); }


    /**
     * Delete option
     *
     * @param $option
     * @return mixed
     */
    public function deleteOption($option){ return delete_option($option); }


    /**
     * Update options, we don't need to check if it exists, it will create it if not.
     *
     * @param $namespace
     * @param array $options
     * @return mixed
     */

    public function updateOptions($namespace, array $options = array()){ return update_option($namespace, $options); }


    /**
     * Add saved notice
     *
     * @param $key
     * @param $value
     */
    public function addSavedNotice($key, $value){ $this->injectSingle('notices', array($key => $value), self::KEY_GENERAL); }


    /**
     * Get saved notices
     *
     * @return null
     */
    public function getSavedNotices()
    {
        $general = $this->getOptions(self::KEY_GENERAL);
        if(isset($general['notices'])){
            return $general['notices'];
        }
        return null;
    }


    /**
     * Flush aaved notices - just rewrites with null value
     *
     * @return bool
     */
    public function flushSavedNotices()
    {
        $this->injectSingle('notices', null, self::KEY_GENERAL);
        return true;
    }

    /**
     * Set single
     *
     * @param $key
     * @param $value
     * @param $namespace
     * @return mixed
     */
    public function injectSingle($key, $value, $namespace)
    {
        $original = $this->getOptions($namespace);
        if(is_array($value)){
            // probably notices
            $inject[$key] = array_merge((array)$original[$key], array($value));
        } else {
            $inject[$key] = $value;
        }
        return $this->updateOptions($namespace, array_merge((array)$original, (array)$inject));
    }


    /**
     * Flush all settings
     */

    public static function flush()
    {
        $refl = new \ReflectionClass(__CLASS__);
        $constants = $refl->getConstants();
        if($constants){
            foreach($constants as $constant => $value){
                if(Strings::startsWith($constant, 'KEY_')){
                    delete_option($refl->getConstant($constant));
                }
            }
        }
    }
}