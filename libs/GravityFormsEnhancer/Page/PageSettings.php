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
        $this->tabbed(false);
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
                'id' => 'gravityFormsEnhancerMore',
                'title' => __('More', 'gfenhancer')
            )
        );
        // Labels
        $this->addCheckbox('gravityFormsEnhancerLabels', __('Move complex fields labels before inputs.', 'gfenhancer'));
        $this->addCheckbox('gravityFormsEnhancerLabels', __('Remove first label of complexed fields.', 'gfenhancer'));
        $this->addCheckbox('gravityFormsEnhancerLabels', __('Remove all labels.', 'gfenhancer'));
        // Placeholders
        $this->addCheckbox('gravityFormsEnhancerPlaceholders', __('Add HTML5 placeholders to fields.', 'gfenhancer'));
        $this->addCheckbox('gravityFormsEnhancerPlaceholders', __('Add Javascript placeholders to fields.', 'gfenhancer'));
        // More
        $this->addHtml('gravityFormsEnhancerMore', __('If there is a functionality you would like to see here, please don\'t hesitate and post it on the forum.', 'gfenhancer'));
        // Register
        $this->register();
    }
}