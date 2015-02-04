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

use GravityFormsEnhancer\RepositorySettings;
use GravityFormsEnhancer\Enhancer;

/**
 * Class Frontend
 * @package GravityFormsEnhancer
 * @author Martin Picha (http://latorante.name)
 */
class Frontend
{
    /** @var \GravityFormsEnhancer\EnhancerSettings  */
    private $repositorySettings;
    /** @var \GravityFormsEnhancer\Enhancer */
    private $enhancer;


    /**
     * @param EnhancerSettings $repositorySettings
     */
    public function __construct(\GravityFormsEnhancer\EnhancerSettings $repositorySettings)
    {
        // Repository
        $this->repositorySettings = $repositorySettings;
        // Register Enhancer
        $this->enhancer = new Enhancer($this->repositorySettings);
    }
}