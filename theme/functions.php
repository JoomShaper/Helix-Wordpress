<?php
    /**
    * @package Helix Framework
    * @author JoomShaper http://www.joomshaper.com
    * @copyright Copyright (c) 2010 - 2013 JoomShaper
    * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
    */

    define('_THEME', get_template()); //Define _THEME

    $require_helix_text = '"'.wp_get_theme().'" required Helix plugin. <a target="_blank" href="http://www.joomshaper.com/helix/wordpress">Please install and activate Helix plugin.</a>';

    function showMessage()
    {
        global $require_helix_text;
        echo '<div id="message" class="error">';
        echo "<p><strong>$require_helix_text</strong></p></div>";
    }

    if( !class_exists('Helix') ){
        if( is_admin() and $pagenow=='customize.php' ){
            wp_die( $require_helix_text );
        }
        elseif(!is_admin()){
            wp_die( $require_helix_text );
        } else {
            add_action('admin_notices', 'showMessage');
            return;
        }
    }

    Helix::getInstance();
    $_preset = Helix::preset(); 
    $_direction = Helix::direction(); 

    if(!is_admin()) {
        Helix::setLessVariables(array(
                'preset'=> $_preset,
                'bg_color'=> Helix::PresetParam('_bg'),
                'header_color'=> Helix::PresetParam('_header'),
                'text_color'=> Helix::PresetParam('_text'),
                'link_color'=> Helix::PresetParam('_link')
            ))
        ->addLess('master', 'theme')
        ->addLess( 'presets',  'presets/'. $_preset );
        Helix::addJS('menu.js');
    }

    //add_theme_support( 'post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat') ); //for future use