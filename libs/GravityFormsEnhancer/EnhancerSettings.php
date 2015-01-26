<?php
/**
 * This file is part of the Instagram Approve plugin.
 *
 * Copyright (c) 2015 latorante.name
 *
 * For the full copyright and license information, please view
 * the GravityFormsEnhacner.php file in root directory of this plugin.
 */

namespace GravityFormsEnhancer;

use GravityFormsEnhancer\Utils\Strings;


class EnhancerSettings extends RepositorySettings
{
    /** @var bool  */
    var $enhancer = false;
    /** @var bool */
    var $checkMoveLabels = false;
    /** @var bool */
    var $checkRemoveFirstLabel = false;
    /** @var bool */
    var $checkRemoveAllLabels = false;
    /** @var bool */
    var $checkAddPlaceholdersHTML5 = false;
    /** @var bool */
    var $checkAddPlaceholdersJavascript = false;


    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        // Assign
        $this->checkMoveLabels = $this->getOption('move-complex-fields-labels-before-inputs', 'gravityFormsEnhancerLabels');
        $this->checkRemoveFirstLabel =  $this->getOption('remove-first-label-of-complexed-fields', 'gravityFormsEnhancerLabels');
        $this->checkRemoveAllLabels = $this->getOption('remove-all-labels', 'gravityFormsEnhancerLabels');
        $this->checkAddPlaceholdersHTML5 = $this->getOption('add-html5-placeholders-to-fields', 'gravityFormsEnhancerPlaceholders');
        $this->checkAddPlaceholdersJavascript = $this->getOption('add-javascript-placeholders-to-fields', 'gravityFormsEnhancerPlaceholders');
        // Sanatize
        $this->sanatize();
    }


    /**
     * Sanatizer goes through assigned properties
     * of this class and sanatizes some of the fields,
     * good example is checkboxes, which have values of "on" or null,
     * where "on" needs to be converted to TRUE, and the other one
     * to FALSE
     */
    public function sanatize()
    {
        // Get properties
        $refl = new \ReflectionClass(__CLASS__);
        $properties = $refl->getProperties();
        if($properties){
            foreach($properties as $property){
                // Only our class
                if($property->class == __CLASS__){
                    // Checkboxes need to fix values, from "on" to "TRUE" etc.
                    if(Strings::startsWith($property->name, 'check')){
                        $this->{$property->name} = $this->checked($this->{$property->name});
                        // Are we running enhancer?
                        if($this->{$property->name} == TRUE){
                            $this->enhancer = TRUE;
                        }
                    }
                }
            }
        }
    }


    /**
     * Checked?
     *
     * @param $data
     * @return bool
     */
    public function checked($data)
    {
        return $data == 'on' ? TRUE : FALSE;
    }
}