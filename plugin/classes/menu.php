<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

// Menu output mods
class HelixMenuWalker extends Walker_Nav_Menu {

	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		
		$depth = $depth+1;
		
		$sub_level_child = ($depth>1) ? 'sub-level-child ' : 'sub-level ';
		
		$output .= "\n$indent<div class=\"sp-submenu-wrap level-$depth\"><ul class=\"sp-menu sp-submenu level-$depth\">\n";
	}

	function start_el(&$output, $item, $depth, $args)
	{
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		if(in_array('current-menu-item', $classes)){
			$classes[] = 'active';
		}

		if(in_array('current_page_parent', $classes)){
			$classes[] = 'active';
		}

		$class_names .= join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );

		$output .= $indent . '<li id="menu-item-'. $item->ID . '" class="' . esc_attr( $class_names ) .'" >';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a class="menu-item"'. $attributes .'>';
		$item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
		$item_output .= $args->link_after;
		
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output )
	{
		$id_field = $this->db_fields['id'];
		if ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
		}

		// $id_field = $this->db_fields['id'];
		if ( !empty( $children_elements[ $element->$id_field ] ) ) {
			$element->classes[] = 'parent';
		}

		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}