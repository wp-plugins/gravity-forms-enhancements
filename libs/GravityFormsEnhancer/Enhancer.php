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

use GravityFormsEnhancer\WordPress\Filter;
use GravityFormsEnhancer\EnhancerSettings;

/**
 * Class Enhancer
 * @package GravityFormsEnhancer
 * @author Martin Picha (http://latorante.name)
 */
class Enhancer
{
    /** @var null | array | int */
    public static $form = NULL;
    /** @var int  */
    var $priority = 10;
    /** @var array */
    var $fieldLabelFirstRemoved = array();
    /** @var array */
    var $fieldHTML5Types = array(
        'color',
        'date' => 'date',
        'datetime',
        'datetime-local',
        'email' => 'email',
        'month',
        'number',
        'range',
        'search',
        'phone' => 'tel',
        'time',
        'website' => 'url',
        'week',
    );

    /**
     * Register Gravity Forms handler, with Settings object,
     * so we can get settings and now what to do with
     * final HTML.
     *
     * @param EnhancerSettings $repositorySettings
     */
    public function __construct(\GravityFormsEnhancer\EnhancerSettings $repositorySettings)
    {
        // We only run the show if Enhancer is enabled,
        // meaning if original behaviour is being modified
        // in settings.
        if($repositorySettings->enhancer == TRUE){
            // PHP lower than 5.4 support (meh)
            $that = $this;
            // Add gravity forms HTML content filter
            Filter::add('gform_field_content', function($content, $field, $value, $lead_id, $form_id) use ($repositorySettings, $that){
                // Apply form
                self::$form = apply_filters('gravity_forms_enhancer_form', self::$form);
                // Not for admin
                if(!is_admin()){
                    if(is_numeric(self::$form)){
                        if(self::$form == $form_id){
                            // Hunt for specific form
                            $content = $that->hunt($content, $field, $repositorySettings);
                        }
                    } elseif(is_array(self::$form)){
                        if(in_array($form_id, self::$form)){
                            // Hunt for list of forms
                            $content = $that->hunt($content, $field, $repositorySettings);
                        }
                    } else {
                        // Hunt for all forms
                        $content = $that->hunt($content, $field, $repositorySettings);
                    }
                }
                // Always return content
                return $content;
            }, $this->priority, 5);
        }
    }


    /**
     * Hunter that finds labels and moves
     * them around.
     *
     * @param $content
     * @param $field
     * @param EnhancerSettings $repositorySettings
     * @return mixed
     */
    private function hunt($content, $field, \GravityFormsEnhancer\EnhancerSettings $repositorySettings)
    {
        // Field basic
        $fieldId = $field['id'];
        $fieldHasLabel = (isset($field['label']) && !empty($field['label'])) ? TRUE : FALSE;
        $fieldHasChoices = (isset($field['choices']) && !empty($field['choices'])) ? count($field['choices']) : FALSE;
        $fieldHasInputs = (isset($field['inputs']) && !empty($field['inputs'])) ? count($field['inputs']) : FALSE;
        $fieldType = (isset($field['type']) && !empty($field['type'])) ? $field['type'] : NULL;
        $fieldComplexed = FALSE;
        $fieldCanRetype = (isset($this->fieldHTML5Types[$fieldType])) ? TRUE : FALSE;
        // Complexed field?
        if(isset($field['inputs'])
            && !empty($field['inputs'])
            && ($fieldType !== 'checkbox' && $fieldType !== 'radio')){  // For some reason checkbox / radios would have been counted as complexed as well.
            // Yup it is now.
            $fieldComplexed = TRUE;
        }
        // Set errors off
        libxml_use_internal_errors(TRUE);
        // DomDocument
        $dom = new \DOMDocument;
        $dom->loadHTML($content);
        $dom->preserveWhiteSpace = FALSE;
        // Loaded HTML
        $labels = $dom->getElementsByTagName("label");
        $labelMoveTo = array();
        $labelFirst = $dom->getElementsByTagName("label")->item(0);
        // Remove first label for complexed fields?
        if($repositorySettings->checkRemoveFirstLabel === TRUE && !empty($labelFirst) && $fieldComplexed === TRUE){
            $labelFirst->parentNode->removeChild($labelFirst);
        }
        // Now, let's do some magic
        if($labels){
            foreach($labels as $label){
                // Here comes the fun, we assign lables by their
                // "for" attribute, which is the input id,
                // to the array, so we can move them before inputs.
                $labelMoveTo[$label->getAttribute('for')] = $label;
            }
        }
        // Did we find anything? Good
        if($labelMoveTo && !empty($labelMoveTo)){
            foreach($labelMoveTo as $inputId => $label){
                // Can have placehodler?
                $fieldCanHavePlaceholder = FALSE;
                $fieldCanHavePlaceholderJavascript = FALSE;
                $fieldCanHavePlaceholderSelect = FALSE;
                $fieldCanDeletePlaceholder = TRUE;
                // Get input by given ID
                $input = $dom->getElementById($inputId);
                // If we have an input, insert label before, if it's not a checkbox | radio | submit
                if($input){
                    // Basics
                    if($input->tagName == 'textarea'
                        || ($input->tagName == 'input'
                            && $input->getAttribute('type') !== 'checkbox'
                            && $input->getAttribute('type') !== 'radio'
                            && $input->getAttribute('type') !== 'submit')
                    ){
                        // This type of field can have placeholder
                        $fieldCanHavePlaceholder = TRUE;
                        // And can have Javascript placeholder
                        $fieldCanHavePlaceholderJavascript = TRUE;
                        if($input->tagName == 'textarea'){
                            // If it's not textarea of course
                            $fieldCanHavePlaceholderJavascript = FALSE;
                        }
                    }// Radios, and checkboxes should have labels
                    if($input->tagName == 'input' && ($input->getAttribute('type') == 'radio' || $input->getAttribute('type') == 'checkbox')){
                        $fieldCanDeletePlaceholder = FALSE;
                    }
                    // Selects can have placeholders too! (sort of)
                    if($input->tagName == 'select'){
                        $fieldCanHavePlaceholderSelect = TRUE;
                    }
                }
                // Magic begins right here
                // Add HTML5 Placeholders
                if($input && $fieldCanHavePlaceholder && $repositorySettings->checkAddPlaceholdersHTML5 === TRUE){
                    $input->setAttribute('placeholder', $label->nodeValue);
                }
                // Add Javascript Placehodlers
                if($input && $fieldCanHavePlaceholder && $fieldCanHavePlaceholderJavascript && $repositorySettings->checkAddPlaceholdersJavascript === TRUE){
                    $input->setAttribute('onfocus', 'if (this.value=="'. $label->nodeValue .'"){this.value="";}');
                    $input->setAttribute('onblur', 'if (this.value=="") {this.value="'. $label->nodeValue .'";}');
                    // Can we for sure?
                    if($input->hasAttribute('value')){
                        // Add value placeholder
                        $input->removeAttribute('value');
                        $input->setAttribute('value', $label->nodeValue);
                    }
                }
                // Add "placeholders" to select.
                if($input && $fieldCanHavePlaceholderSelect && $repositorySettings->checkAddPlaceholdersSelects){
                    // This is sort of annyoing, but the address field usually has
                    // empty first field select, argh, we'll remove it if it's there
                    if($fieldType == 'address'){
                        if(
                            $input->firstChild->nodeValue == ''
                            && $input->firstChild->getAttribute('value') == ''
                            && $input->firstChild->getAttribute('selected') == TRUE
                        ){
                            // Ok, we have a winner, remove him! Empty bastard
                            $input->removeChild($input->firstChild);
                        }
                    }
                    if($input->firstChild){
                        // Insert before first <option>
                        $input->insertBefore(new \DOMElement('option', $label->nodeValue), $input->firstChild);
                    }
                }
                // Move complex fields label before input? Only if we don't delete them later of course.
                if($input && $fieldComplexed === TRUE && $repositorySettings->checkMoveLabels === TRUE && $repositorySettings->checkRemoveAllLabels === FALSE){
                    $input->parentNode->insertBefore($label, $input);
                }
                // Remove All Labels?
                if($input && $repositorySettings->checkRemoveAllLabels === TRUE){
                    // Get all labels
                    $labels = $dom->getElementsByTagName("label");
                    $labelsCurrentCount = count($labels);
                    $labelFirst2ndRound = $dom->getElementsByTagName("label")->item(0);
                    // Dangerous game padawan!
                    if($labels){
                        // Checkboxes and radios should have their labels, but the overall
                        // box label can go.
                        if($fieldCanDeletePlaceholder === TRUE){
                            foreach($labels as $labelKey => $removeLabel){
                                // Remove label
                                $removeLabel->parentNode->removeChild($removeLabel);
                            }
                        } elseif(!in_array($fieldId, $this->fieldLabelFirstRemoved)){
                            // Let's remove the first label, and save this field ID to the array,
                            // so the next irration we know, not to delete another label.
                            $labelFirst2ndRound->parentNode->removeChild($labelFirst2ndRound);
                            $this->fieldLabelFirstRemoved[] = $fieldId;
                        }
                    }
                }
                // Can we retype this field?
                if($input && $repositorySettings->checkRetypeInputs === TRUE && $fieldCanRetype === TRUE){
                    // Can we for sure?
                    if($input->hasAttribute('type')){
                        // Retype
                        $input->removeAttribute('type');
                        $input->setAttribute('type', $this->fieldHTML5Types[$fieldType]);
                    }
                }
            }
        }
        // Clear errors
        libxml_clear_errors();
        // Give back clean HTML without doctype
        return preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $dom->saveHTML());
    }
}