<?php
    /**
    * @package Helix Framework
    * @author JoomShaper http://www.joomshaper.com
    * @copyright Copyright (c) 2010 - 2013 JoomShaper
    * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
    */
    add_action( 'in_widget_form','helix_customclass_widgets_form', 10, 3 );
    add_filter( 'widget_update_callback', 'helix_customclass_widget_update_callback', 10, 2 );
    add_filter( 'dynamic_sidebar_params', 'helix_customclass_dynamic_sidebar_params' );

    /**
    * On widget form
    * 
    * @param mixed $widget
    * @param mixed $return
    * @param mixed $instance
    */
    function helix_customclass_widgets_form($widget,$return,$instance){

        $value = isset($instance['helix_class'])?$instance['helix_class']:'';
        $form = '<p>
        <label for="widget-'.$widget->get_field_id('helix_class').'">Classes:</label> 
        <input class="widefat" id="widget-'.$widget->get_field_id('helix_class').'" 
        name="'.$widget->get_field_name('helix_class').'" type="text" value="'.$value.'">
        </p>';

        echo $form;
    }

    /**
    * On widget update
    * 
    * @param mixed $instance
    * @param mixed $new_instance
    */
    function helix_customclass_widget_update_callback( $instance, $new_instance ) {
        $instance['helix_class'] = $new_instance['helix_class'];
        return $instance;
    }
    /**
    * On showing sidebar
    * 
    * @param mixed $params
    */
    function helix_customclass_dynamic_sidebar_params( $params ) {

        global $wp_registered_widgets;
        $widget_id     = $params[0]['widget_id'];
        $widget_obj    = $wp_registered_widgets[$widget_id];
        $widget_opt    = get_option($widget_obj['callback'][0]->option_name);
        $widget_num    = $widget_obj['params'][0]['number'];
        $value         = isset( $widget_opt[$widget_num]['helix_class'] )?$widget_opt[$widget_num]['helix_class']:'';
        $settings = $widget_opt[$widget_num];
        // if has class
        if(preg_match( '/class="/', $params[0]['before_widget'])){
            $params[0]['before_widget'] =  preg_replace( '/class="/', "class=\"{$value} ", $params[0]['before_widget'], 1 );
        } else {
            $params[0]['before_widget'] = preg_replace( '/(\<[a-zA-Z]+)(.*?)(\>)/', "$1 $2  class=\"{$value}\" $3", $params[0]['before_widget'], 1 );
        }
	
        //  wraping widget content wrapper
         if ( $params[0][ 'after_widget' ] == '</div></div>' and isset($settings['title']) and empty($settings['title']) ){

            $params[0][ 'before_widget' ] .= '<div class="sp-widget-content">';
        } 

        return $params;
	}