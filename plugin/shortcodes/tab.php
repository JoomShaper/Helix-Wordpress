<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
	
//[Tab]
if(!function_exists('tab_sc')) {
	$tabArray = array();
	function tab_sc( $atts, $content="" ){
		global $tabArray;
		
		$params = shortcode_atts(array(
			  'button' => 'nav-tabs',
			  'id' => 'tab'
		 ), $atts);
		
		do_shortcode( $content );
		
		$id = $params['id'];
		
		$html = '<div class="tab">';
		
		$html .= '<div class="' . $atts['class'] . '">';
		
		//Tab Title
		$html .='<ul class="nav ' . $params['button'] . '" id="' . $id . '">';
		foreach ($tabArray as $key=>$tab) {
			$html .='<li class="'. ( ($key==0) ? "active" : "").'"><a href="#'.$id.'-tab-'.$key.'" data-toggle="tab">'. $tab['title'] .'</a></li>';
		}
		$html .='</ul>';
		
		//Tab Content
		$html .='<div class="tab-content">';
		foreach ($tabArray as $key=>$tab) {
			$html .='<div class="tab-pane fade'. ( ($key==0) ? " active in" : "").'" id="'.$id.'-tab-'.$key.'">' . $tab['content'] .'</div>';
		}
		$html .='</div>';
		
		$html .='</div>';
		
		$html .='</div>';
		
		$tabArray = array();
		
		return $html;
	}
	add_shortcode( 'tab', 'tab_sc' );
	
	//Tab Items
	function tab_item_sc( $atts, $content="" ){
		global $tabArray;
		$tabArray[] = array('title'=>$atts['title'], 'content'=>$content);
	}

	add_shortcode( 'tab_item', 'tab_item_sc' );	
}