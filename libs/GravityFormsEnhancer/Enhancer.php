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
 *
 * @author Martin Picha (http://latorante.name)
 */
class Enhancer
{
    /** @var null | array | int */
    public static $form = null;
    /** @var int  */
    public static $priority = 10;


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
            // Add gravity forms HTML content filter
            Filter::add('gform_field_content', function($content, $field, $value, $lead_id, $form_id) use ($repositorySettings){
                // Apply form
                self::$form = apply_filters('gravity_forms_enhancer_form', self::$form);
                // Not for admin
                if(!is_admin()){
                    if(is_numeric(self::$form)){
                        if(self::$form == $form_id){
                            // Put placeholders for specific form
                            $content = $this->hunt($content, $field, $repositorySettings);
                        }
                    } elseif(is_array(self::$form)){
                        if(in_array($form_id, self::$form)){
                            // Put placeholders for list of forms
                            $content = $this->hunt($content, $field, $repositorySettings);
                        }
                    } else {
                        // Put placeholders for all forms
                        $content = $this->hunt($content, $field, $repositorySettings);
                    }
                }
                // Always return content
                return $content;
            }, self::$priority, 5);
        }
    }


    /**
     * Hunter that finds labels and moves
     * them around.
     *
     * TODO: add functionality for correct email, number html5 fields etc.
     *
     * @param $content
     * @param $field
     * @param EnhancerSettings $repositorySettings
     * @return mixed
     */
    private function hunt($content, $field, \GravityFormsEnhancer\EnhancerSettings $repositorySettings)
    {
        $fieldComplexed = FALSE;
        // Complexed field?
        if(isset($field['inputs']) && !empty($field['inputs'])){
            // Yup it is now.
            $fieldComplexed = true;
        }
        // Set errors off
        libxml_use_internal_errors(true);
        // DomDocument
        $dom = new \DOMDocument;
        $dom->loadHTML($content);
        $dom->preserveWhiteSpace = false;
        // Loaded HTML
        $labels = $dom->getElementsByTagName("label");
        $labelMoveTo = array();
        $labelFirst = $dom->getElementsByTagName("label")->item(0);
        // Remove first label for complexed fields?
        if($repositorySettings->checkRemoveFirstLabel == TRUE && !empty($labelFirst) && $fieldComplexed == TRUE){
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
                // Get input by given ID
                $input = $dom->getElementById($inputId);
                // If we have an input, insert label before, if it's not a checkbox | radio | submit
                if($input
                    && (($input->tagName == 'textarea')
                        || ($input->tagName == 'input'
                            && $input->getAttribute('type') !== 'checkbox'
                            && $input->getAttribute('type') !== 'radio'
                            && $input->getAttribute('type') !== 'submit'
                        )
                    )){
                    // This type of field can have placeholder
                    $fieldCanHavePlaceholder = TRUE;
                    // And can have Javascript placeholder
                    $fieldCanHavePlaceholderJavascript = TRUE;
                    if($input->tagName == 'textarea'){
                        // If it's not textarea of course
                        $fieldCanHavePlaceholderJavascript = FALSE;
                    }
                }
                // Magic begins right here
                // Add HTML5 Placeholders
                if($input && $fieldCanHavePlaceholder && $repositorySettings->checkAddPlaceholdersHTML5 == TRUE){
                    $input->setAttribute('placeholder', $label->nodeValue);
                }
                // Add Javascript Placehodlers
                if($input && $fieldCanHavePlaceholder && $fieldCanHavePlaceholderJavascript && $repositorySettings->checkAddPlaceholdersJavascript == TRUE){
                    $input->setAttribute('onfocus', 'if (this.value=="'. $label->nodeValue .'"){this.value="";}');
                    $input->setAttribute('onblur', 'if (this.value=="") {this.value="'. $label->nodeValue .'";}');
                }
                // Move complex fields label before input? Only if we don't delete them later of course.
                if($input && $fieldComplexed == TRUE && $repositorySettings->checkMoveLabels == TRUE && $repositorySettings->checkRemoveAllLabels == FALSE){
                    $input->parentNode->insertBefore($label, $input);
                }
                // Remove All Labels?
                if($input && $repositorySettings->checkRemoveAllLabels == TRUE){
                    $labels = $dom->getElementsByTagName("label");
                    // Dangerous game padawan!
                    if($labels){
                        foreach($labels as $removeLabel){
                            $removeLabel->parentNode->removeChild($removeLabel);
                        }
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