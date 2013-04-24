<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

get_header();
	
if (Helix::view()=='blog') { //order filter

	$blog_category_display = (int)  Helix::Param( Helix::view().'_category' );
	$blog_categories = Helix::Param( Helix::view().'_categories' );
	
	if(isset($blog_category_display) and $blog_category_display===0 ){
	
		query_posts( array( 
			'orderby' => Helix::Param( Helix::view().'_order_by' ), 
			'order' => Helix::Param( Helix::view().'_order' ) ,
			'category__not_in' => $blog_categories
		) );
		
	} elseif(isset($blog_category_display) and $blog_category_display===1){
	
		query_posts( array( 
			'orderby' => Helix::Param( Helix::view().'_order_by' ), 
			'order' => Helix::Param( Helix::view().'_order' ) ,
			'category__in' => $blog_categories
		) );
		
	} else {
		query_posts( array( 
			'orderby' => Helix::Param( Helix::view().'_order_by' ), 
			'order' => Helix::Param( Helix::view().'_order' ) 
		) );
	
	}
}

if ( have_posts() ) {
	if ( is_page() ) {
		$format = 'page';
	} elseif ( is_single() ) {
		$format = 'single';
	} else {
		$format = get_post_format();
	}
} else {
	$format = 'none';
}

get_template_part( 'content/content',  $format);

get_footer();