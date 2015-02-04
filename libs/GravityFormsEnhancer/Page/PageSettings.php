<?php
/**
 * This file is part of the Gravity Forms Enhancer plugin.
 *
 * Copyright (c) 2015 latorante.name
 *
 * For the full copyright and license information, please view
 * the GravityFormsEnhacner.php file in root directory of this plugin.
 */

namespace GravityFormsEnhancer\Page;

use \GravityFormsEnhancer\WordPress\Settings;

/**
 * Class PageSettings
 * @package GravityFormsEnhancer\Page
 * @author Martin Picha (http://latorante.name)
 */
class PageSettings extends \GravityFormsEnhancer\WordPress\Settings
{
    /** @var \GravityFormsEnhancer\RepositorySettings */
    var $repositarySettings;


    /**
     * @param \GravityFormsEnhancer\RepositorySettings $repositorySettings
     */
    public function __construct(\GravityFormsEnhancer\RepositorySettings $repositorySettings)
    {
        $this->repositarySettings = $repositorySettings;
        $this->settings();
    }


    /**
     * Settings
     */
    public function settings()
    {
        // Set class
        $this->setClass('gravityFormsEnhancer');
        // Set title
        $this->setTitle(__('Enhancements', 'gfenhancer'));
        // Tabbed?
        $this->tabbed(FALSE);
        // Sections
        $this->addSection(
            array(
                'id' => 'gravityFormsEnhancerLabels',
                'title' => __('Labels', 'gfenhancer')
            )
        );
        $this->addSection(
            array(
                'id' => 'gravityFormsEnhancerPlaceholders',
                'title' => __('Placeholders', 'gfenhancer')
            )
        );
        $this->addSection(
            array(
                'id' => 'gravityFormsEnhancerHTML',
                'title' => __('Field types', 'gfenhancer')
            )
        );
        $this->addSection(
            array(
                'id' => 'gravityFormsEnhancerShortcodes',
                'title' => __('Shortcodes', 'gfenhancer')
            )
        );
        $this->addSection(
            array(
                'id' => 'gravityFormsEnhancerMore',
                'title' => __('More', 'gfenhancer')
            )
        );
        // Labels
        $this->addCheckbox('gravityFormsEnhancerLabels', __('Move complex fields labels before inputs.', 'gfenhancer'));
        $this->addCheckbox('gravityFormsEnhancerLabels', __('Remove first label of complexed fields.', 'gfenhancer'));
        $this->addCheckbox('gravityFormsEnhancerLabels', __('Remove all labels.', 'gfenhancer'));
        // Placeholders
        $this->addCheckbox('gravityFormsEnhancerPlaceholders', __('Add HTML5 placeholders to fields.', 'gfenhancer'), NULL, '<code>&lt;input <strong>placeholder="&hellip;"</strong> /&gt;</code>');
        $this->addCheckbox('gravityFormsEnhancerPlaceholders', __('Add Javascript placeholders to fields.', 'gfenhancer'), NULL, '<code>&lt;input <strong>onblur="&hellip;"</strong>  <strong>onfocus="&hellip;"</strong> <strong>value="&hellip;"</strong> /&gt;</code>');
        $this->addCheckbox(
            'gravityFormsEnhancerPlaceholders',
            __('Add placeholders to select dropdowns.', 'gfenhancer'),
            NULL,
            __('Adds empty first option to the dropdown lists, with a field label.', 'gfenhancer')
            . '<br /><code>&lt;select&gt;&lt;option value=""&gt;<strong>Placeholder&hellip;</strong>&lt;/option&gt;&lt;/select&gt;</code>'
        );
        // Html
        $this->addCheckbox(
            'gravityFormsEnhancerHTML',
            __('Retype HTML5 inputs to correct types.', 'gfenhancer'),
            NULL,
            __('Retypes all instances of HTML5 possible inputs to correct types: date, email, tel, url.', 'gfenhancer')
            . '<br /><code>&lt;input type="<strong>date|email|tel|url</strong>" /&gt;</code>'
        );
        // Shortcodes
        $this->addHtml(
            'gravityFormsEnhancerShortcodes',
            __('Filter enchacements only for certain forms.', 'gfenhancer'),
            '<span class="description only">' .
            __('To use Form enchacements only one one specific form, use this filter:', 'gfenhancer')
            . '<br /><code>add_filter(\'gravity_forms_enhancer_form\', function(){ return 1; }, 10, 1);</code>'
            . '<strong>'. __('Where number 1, is the Gravity Form ID.', 'gfenhancer') .'</strong>'
            . '</span>'
        );
        $this->addHtml(
            'gravityFormsEnhancerShortcodes',
            __('Filter enchacements only for multiple forms.', 'gfenhancer'),
            '<span class="description only">' .
            __('To use Form enchacements on multiple forms use this filter:', 'gfenhancer')
            . '<br /><code>add_filter(\'gravity_forms_enhancer_form\', function(){ return array(1, 3, 4); }, 10, 1);</code>'
            . '<strong>'. __('Where the array consits the ID\'s of forms.', 'gfenhancer') .'</strong>'
            . '</span>'
        );
        // More
        $this->addHtml(
            'gravityFormsEnhancerMore',
            __('Request more!', 'gfenhancer'),
            '<span class="description only">' .
            __('If there is a functionality you would like to see here, please don\'t hesitate and post it on the forum. If it\'s going to be something cool and funky, I will add it!', 'gfenhancer')
            . ' <a target="_blank" href="https://wordpress.org/support/topic/wishlist-10?replies=1">Forum Whishlist.</a>'
            . '</span>'
        );
        // Register
        $this->register();
    }
}