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
use GravityFormsEnhancer\WordPress\Notice;


abstract class Settings
{
    /** if true, displays erros as notices, if false, displays them underneath the field */
    const DISPLAY_NOTICE = false;
    /** @var array */
    private $sections = array();
    /** @var array */
    private $fields = array();
    /** @var string */
    public $title = 'Settings';
    /** @var string */
    public $class = 'enhanced';
    /** @var bool */
    public $tabbed = true;


    /**
     * Constructor
     */
    public function register()
    {
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueueScripts'));
        add_action('admin_init', array($this, 'adminInit'));
    }


    /**
     * Enqueue scripts and styles
     */
    public function adminEnqueueScripts()
    {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('thickbox');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('jquery');
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_media();
    }


    /**
     * Set settings sections
     *
     * @param $sections
     * @return Settings
     */
    public function setSections($sections){ $this->sections = $sections; return $this; }


    /**
     * Add a single section
     *
     * @param $section
     * @return Settings
     */
    public function addSection($section){ $this->sections[] = $section; return $this; }


    /**
     * Set settings fields
     *
     * @param $fields
     * @return Settings
     */
    public function addFields($fields){ $this->fields = $fields; return $this; }


    /**
     * Set Title
     *
     * @param $title
     */
    public function setTitle($title){ $this->title = $title; }


    /**
     * Set Class
     *
     * @param $class
     */
    public function setClass($class){ $this->class = $class; }


    /**
     * Add single field
     *
     * @param $section
     * @param $field
     * @return Settings
     */
    public function addField($section, $field)
    {
        $defaults = array(
            'name' => '',
            'label' => '',
            'desc' => '',
            'type' => 'text'
        );
        $arg = wp_parse_args($field, $defaults);
        $this->fields[$section][] = $arg;
        return $this;
    }


    /**
     * Add Html
     *
     * @param $section
     * @param $label
     * @param null $name
     */
    public function addHtml($section, $label, $name = null)
    {
        $this->addField($section,
            array(
                'name' => $name == null ? Strings::webalize($label) : $name,
                'type' => 'html',
                'label' => $label
            )
        );
    }


    /**
     * Add Text Field
     *
     * @param $section
     * @param $label
     * @param string $defautlt
     * @param string $desc
     * @param null $name
     */
    public function addText($section, $label, $defautlt = '', $desc = '', $name = null)
    {
        $this->addField($section,
            array(
                'name' => $name == null ? Strings::webalize($label) : $name,
                'label' => $label,
                'type' => 'text',
                'default' => $defautlt,
                'desc' => $desc
            )
        );
    }


    /**
     * Add Textarea
     *
     * @param $section
     * @param $label
     * @param string $defautlt
     * @param string $desc
     * @param null $name
     */
    public function addTextarea($section, $label, $defautlt = '', $desc = '', $name = null)
    {
        $this->addField($section,
            array(
                'name' => $name == null ? Strings::webalize($label) : $name,
                'label' => $label,
                'type' => 'textarea',
                'default' => $defautlt,
                'desc' => $desc
            )
        );
    }


    /**
     * Add Select
     *
     * @param $section
     * @param $label
     * @param array $options
     * @param string $desc
     * @param null $name
     */
    public function addSelect($section, $label, $options = array(), $desc = '', $name = null)
    {
        $this->addField($section,
            array(
                'name' => $name == null ? Strings::webalize($label) : $name,
                'label' => $label,
                'type' => 'select',
                'options' => $options,
                'desc' => $desc
            )
        );
    }


    /**
     * Add Select
     *
     * @param $section
     * @param $label
     * @param array $options
     * @param mixed $default
     * @param string $desc
     * @param null $name
     */
    public function addMulticheck($section, $label, $options = array(), $default = null, $desc = '', $name = null)
    {
        $this->addField($section,
            array(
                'name' => $name == null ? Strings::webalize($label) : $name,
                'label' => $label,
                'type' => 'multicheck',
                'options' => $options,
                'default' => $default,
                'desc' => $desc
            )
        );
    }


    /**
     * Add checkbox
     *
     * @param $section
     * @param $label
     * @param null $default
     * @param string $desc
     * @param null $name
     */
    public function addCheckbox($section, $label, $default = null, $desc = '', $name = null)
    {
        $this->addField($section,
            array(
                'name' => $name == null ? Strings::webalize($label) : $name,
                'label' => $label,
                'type' => 'checkbox',
                'default' => $default,
                'desc' => $desc
            )
        );
    }


    /**
     * Tabbed interface?
     *
     * @param bool $yes
     */
    public function tabbed($yes = true){ $this->tabbed = $yes; }


    /**
     * Initialize and registers the settings sections and fileds to WordPress
     * Usually this should be called at `admin_init` hook.
     *
     * This function gets the initiated settings sections and fields. Then
     * registers them to WordPress and ready for use.
     */
    public function adminInit()
    {
        //register settings sections
        foreach ($this->sections as $section){
            if (false == get_option($section['id'])){ add_option($section['id']); }
            if (isset($section['desc']) && !empty($section['desc'])){
                $section['desc'] = '<div class="inside">'.$section['desc'].'</div>';
                $callback = create_function('', 'echo "'.str_replace('"', '\"', $section['desc']).'";');
            } else {
                $callback = '__return_false';
            }
            add_settings_section($section['id'], $section['title'], $callback, $section['id']);
        }
        //register settings fields
        foreach($this->fields as $section => $field){
            foreach ($field as $option){
                if(!empty($option)){
                    $type = isset($option['type']) ? $option['type'] : 'text';
                    $args = array(
                        'type' => $type,
                        'id' => $option['name'],
                        'desc' => isset($option['desc']) ? $option['desc'] : null,
                        'name' => $option['label'],
                        'section' => $section,
                        'options' => isset($option['options']) ? $option['options'] : '',
                        'std' => isset($option['default']) ? $option['default'] : '',
                        'attr' => isset($option['attr']) ? $option['attr'] : ''
                    );
                    $label = $type !== 'html' ? '<label for="'. $section .'-'. $option['name'] .'">' . $option['label'] . '</label>' : '<h4>' . $option['label'] . '</h4>';
                    add_settings_field(
                        $section . '[' . $option['name'] . ']',
                        $label,
                        array($this, 'renderer'),
                        $section,
                        $section,
                        $args
                    );
                }
            }
        }
        // creates our settings in the options table
        foreach ($this->sections as $section){
            register_setting($section['id'], $section['id'], array($this, 'validate'));
        }
    }


    /**
     * Renderer
     *
     * @param $args
     */
    public function renderer($args)
    {
        // prep
        $fieldHtml = '';
        $fieldValue = esc_attr($this->repositarySettings->getOption($args['id'], $args['section'], $args['std']));
        $fieldValueUn = $this->repositarySettings->getOption($args['id'], $args['section'], $args['std']);
        $fieldClass = $this->hasError($args['id']) ? ' genooError' : '';
        $fieldError = $this->hasError($args['id']) ? $this->getError($args['id']) : false;
        $fieldErrorHtml = $fieldError ? '<br /><div class="clear"></div><span class="genooDescriptionError">' . $fieldError . '</span>' : '';
        $fieldDesc = $args['desc'] ? sprintf('<br /><div class="clear"></div><span class="description"> %s</span>', $args['desc']) : null;
        $fieldAttr = '';
        // add attributes
        if(is_array($args['attr'])){
            foreach($args['attr'] as $key => $value){
                $fieldAttr .= ' ' . $key . '="' . $value .'"';
            }
        }
        // switch renderer
        switch($args['type']){
            case 'text':
            case 'password':
                $fieldHtml .= sprintf('<input type="%1$s" class="regular-text %2$s" id="%3$s-%4$s" name="%3$s[%4$s]" value="%5$s" %6$s />', $args['type'], $fieldClass, $args['section'], $args['id'], $fieldValue, $fieldAttr);
                break;
            case 'checkbox':
                $fieldHtml .= sprintf('<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id']);
                $fieldHtml .= sprintf('<input type="checkbox" class="checkbox" id="%1$s-%2$s" name="%1$s[%2$s]" value="on"%4$s %5$s />', $args['section'], $args['id'], $fieldValue, checked($fieldValue, 'on', false), $fieldAttr);
                $fieldHtml .= sprintf('<label for="%1$s[%2$s]"> %3$s</label>', $args['section'], $args['id'], $args['desc']);
                break;
            case 'multicheck':
                foreach ($args['options'] as $key => $label){
                    $checked = isset($fieldValueUn[$key]) ? $fieldValueUn[$key] : '0';
                    $fieldHtml .= sprintf('<input type="checkbox" class="checkbox" id="%1$s-%2$s-%3$s" name="%1$s[%2$s][%3$s]" value="%3$s"%4$s />', $args['section'], $args['id'], $key, checked($checked, $key, false));
                    $fieldHtml .= sprintf('<label for="%1$s[%2$s][%4$s]"> %3$s</label><br />', $args['section'], $args['id'], $label, $key);
                }
                break;
            case 'radio':
                foreach ($args['options'] as $key => $label){
                    $fieldHtml .= sprintf('<input type="radio" class="radio" id="%1$s-%2$s-%3$s" name="%1$s[%2$s]" value="%3$s"%4$s %5$s />', $args['section'], $args['id'], $key, checked($fieldValueUn, $key, false), $fieldAttr);
                    $fieldHtml .= sprintf('<label for="%1$s[%2$s][%4$s]"> %3$s</label><br />', $args['section'], $args['id'], $label, $key);
                }
                $fieldDesc = sprintf( '<br /><div class="clear"></div><span class="description"> %s</label>', $args['desc']);
                break;
            case 'select':
                $fieldHtml .= sprintf('<select class="regular %1$s" name="%2$s[%3$s]" id="%2$s-%3$s" %4$s>', $fieldClass, $args['section'], $args['id'], $fieldAttr);
                foreach ($args['options'] as $key => $label){
                    $fieldHtml .= sprintf( '<option value="%s"%s>%s</option>', $key, selected($fieldValue, $key, false), $label);
                }
                $fieldHtml .= sprintf('</select>');
                break;
            case 'textarea':
                $fieldHtml .= sprintf('<textarea rows="12" cols="55" class="regular-text %1$s" id="%2$s-%3$s" name="%2$s[%3$s]" %5$s>%4$s</textarea>', $fieldClass, $args['section'], $args['id'], esc_textarea($fieldValueUn), $fieldAttr);
                break;
            case 'html':
                $fieldHtml .= sprintf('<div id="%1$s-%2$s">%3$s</div>', $args['section'], $args['id'], $args['desc']);
                break;
            case 'color':
                $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
                $fieldHtml .= sprintf('<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s-%3$s" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" %6$s />', $size, $args['section'], $args['id'], $fieldValue, $args['std'], $fieldAttr);
                break;
            case 'file':
                $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
                $id = $args['section']  . '[' . $args['id'] . ']';
                $js_id = $args['section']  . '\\\\[' . $args['id'] . '\\\\]';
                $fieldHtml .= sprintf( '<input type="text" class="%1$s-text" id="%2$s-%3$s" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $fieldValue);
                $fieldHtml .= '<input type="button" class="button wpsf-browse" id="'. $id .'_button" value="Browse" />
                            <script type="text/javascript">
                            jQuery(document).ready(function($){
                                $("#'. $js_id .'_button").click(function() {
                                    tb_show("", "media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true");
                                    window.original_send_to_editor = window.send_to_editor;
                                    window.send_to_editor = function(html) {
                                        var url = $(html).attr(\'href\');
                                        if ( !url ) {
                                            url = $(html).attr(\'src\');
                                        };
                                        $("#'. $js_id .'").val(url);
                                        tb_remove();
                                        window.send_to_editor = window.original_send_to_editor;
                                    };
                                    return false;
                                });
                            });
                            </script>';
                break;
            case 'desc':
                $fieldDesc = '';
                $fieldHtml = '<span class="description descriptionAbsolute">'.  $args['desc'] .'</span>';
                break;
            // wysiwyg is bit different
            case 'wysiwyg':
                echo '<div style="width: 500px;">';
                    wp_editor(wpautop($fieldValueUn), $args['section'] . '[' . $args['id'] . ']', array('teeny' => true, 'textarea_rows' => 10));
                echo '</div>';
                echo $fieldHtml . (!self::DISPLAY_NOTICE ? $fieldErrorHtml : '') . $fieldDesc;
                break;
        }
        // render, if not wysiwyg, echo
        echo $args['type'] != 'wysiwyg' ? ($fieldHtml . (!self::DISPLAY_NOTICE ? $fieldErrorHtml : '') . $fieldDesc) : '';
    }


    /**
     * Has tabs?
     *
     * @return bool
     */
    private function hasTabs()
    {
        if(count($this->sections) > 1){
            return true;
        }
        return false;
    }


    /**
     * Has section form?
     *
     * @param $section
     * @return bool
     */
    private function hasForm($section)
    {
        $has = FALSE;
        if(!empty($this->fields[$section])){
            foreach($this->fields[$section] as $field){
                if($field['type'] !== 'html'){
                    $has = TRUE;
                }
            }
        }
        return $has;
    }


    /**
     * Show the section settings forms
     *
     * This function displays every sections in a different form
     */
    public function render()
    {
        $class = $this->class . ' ' .  ($this->tabbed == true ? ' tabbed' : 'not-tabbed');
        echo '<div class="wrap '. $class .'"><h2>' . $this->title . '</h2>';
        // render before
        if($this->hasTabs() && $this->tabbed){
            echo '<h2 class="nav-tab-wrapper">';
            foreach ($this->sections as $tab){
                echo sprintf('<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title']);
            }
            echo '</h2>';
        }

        // Display errors as notices, if set
        if(self::DISPLAY_NOTICE){
            foreach(get_settings_errors() as $err){
                Notice::type('error')->text($err['message']);
            }
        }

        // Render content
        if($this->tabbed == true){
            echo '<div class="metabox-holder">';
            echo '<div class="postbox">';
        }

        // Go thru sections
        foreach($this->sections as $form){

            if($this->tabbed == false){
                echo '<div class="metabox-holder">';
                echo '<div class="postbox">';
            }

            echo '<div id="'. $form['id'] .'" class="group">';
            echo '<form method="post" action="options.php">';

            do_action('wsa_form_top_' . $form['id'], $form);
            settings_fields($form['id']);
            do_settings_sections($form['id']);
            do_action('wsa_form_bottom_' . $form['id'], $form);

            if($this->hasForm($form['id'])){
                echo '<div style="padding-left: 10px">';
                submit_button();
                echo '</div>';
            }

            echo '</form>';
            echo '</div>';

            if($this->tabbed == false){
                echo '</div>';
                echo '</div>';
            }
        }
        if($this->tabbed == true){
            echo '</div>';
            echo '</div>';
        }
        // Render after
        echo '<script type="text/javascript">';
            echo 'jQuery(document).ready(function($){';
                echo '$(\'.wp-color-picker-field\').wpColorPicker();';
                if($this->hasTabs() && $this->tabbed){
                    echo '$(".group").hide();var activetab="";if(typeof(localStorage)!="undefined"){activetab=localStorage.getItem("activetab")}if(activetab!=""&&$(activetab).length){$(activetab).fadeIn()}else{$(".group:first").fadeIn()}$(".group .collapsed").each(function(){$(this).find("input:checked").parent().parent().parent().nextAll().each(function(){if($(this).hasClass("last")){$(this).removeClass("hidden");return false}$(this).filter(".hidden").removeClass("hidden")})});if(activetab!=""&&$(activetab+"-tab").length){$(activetab+"-tab").addClass("nav-tab-active")}else{$(".nav-tab-wrapper a:first").addClass("nav-tab-active")}$(".nav-tab-wrapper a").click(function(a){$(".nav-tab-wrapper a").removeClass("nav-tab-active");$(this).addClass("nav-tab-active").blur();var b=$(this).attr("href");if(typeof(localStorage)!="undefined"){localStorage.setItem("activetab",$(this).attr("href"))}$(".group").hide();$(b).fadeIn();a.preventDefault()});';
                }
            echo '});';
        echo '</script>';
        echo '<style type="text/css" scoped="scoped">.form-table th{padding: 20px 10px} #wpbody-content .metabox-holder {padding-top: 5px}</style>';
        echo '</div>';
    }

    /**
     * Validate | Sanatize
     *
     * @param $options
     * @return mixed
     */
    public function validate($options)
    {
        // check fields
        foreach($options as $key => $value){
            switch($key){
                // default, if it's not array, sanatize
                default:
                    if(!is_array($value)){
                        $options[$key] = sanitize_text_field($value);
                    }
                    break;
            }
        }
        return $options;
    }


    /**
     * Has error
     *
     * @param $key
     * @return bool
     */
    private function hasError($key)
    {
        foreach(get_settings_errors() as $err){
            if($key == $err['setting']){
                return true;
            }
        }
        return false;
    }


    /**
     * Get error
     *
     * @param $key
     * @return bool
     */
    private function getError($key)
    {
        foreach(get_settings_errors() as $err){
            if($key == $err['setting']){
                return $err['message'];
            }
        }
        return false;
    }


    /**
     * Add error
     *
     * @param $key
     * @param $error
     * @return mixed
     */
    public function addError($key, $error){ return add_settings_error($key, 'genooId' . $key, $error, 'error'); }
}