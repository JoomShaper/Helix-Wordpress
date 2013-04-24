<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
?>
<?php if(!is_front_page() && !is_404()) : ?>

<ul class="breadcrumb">
  <li>
	<a href="<?php bloginfo('url'); ?>" class="breadcrumb_home"><?php esc_html_e('Home',_THEME) ?></a> <span class="divider">/</span>
  </li>
  <li class="active">
  
		<?php if( is_tag() ) { ?>
				<?php esc_html_e('Posts Tagged ',_THEME) ?><span class="raquo">&quot;</span><?php single_tag_title(); echo('&quot;'); ?>
		<?php } elseif (is_day()) { ?>
			<?php esc_html_e('Posts made in',_THEME) ?> <?php the_time('F jS, Y'); ?>
		<?php } elseif (is_month()) { ?>
			<?php esc_html_e('Posts made in',_THEME) ?> <?php the_time('F, Y'); ?>
		<?php } elseif (is_year()) { ?>
			<?php esc_html_e('Posts made in',_THEME) ?> <?php the_time('Y'); ?>
		<?php } elseif (is_search()) { ?>
			<?php esc_html_e('Search results for',_THEME) ?> <?php the_search_query() ?>
		<?php } elseif (is_single()) { ?>
			<?php $category = get_the_category();
				  if ( $category ) { 
					$catlink = get_category_link( $category[0]->cat_ID );
					echo ('<a href="'.esc_url($catlink).'">'.esc_html($category[0]->cat_name).'</a> '.'<span class="raquo">&raquo;</span> ');
				  }
				echo get_the_title(); ?>
		<?php } elseif (is_category()) { ?>
			<?php single_cat_title(); ?>
		<?php } elseif (is_tax()) { ?>
			<?php 
				$helix_taxonomy_links = array();
				$helix_term = get_queried_object();
				$helix_term_parent_id = $helix_term->parent;
				$helix_term_taxonomy = $helix_term->taxonomy;
				
				while ( $helix_term_parent_id ) {
					$helix_current_term = get_term( $helix_term_parent_id, $helix_term_taxonomy );
					$helix_taxonomy_links[] = '<a href="' . esc_url( get_term_link( $helix_current_term, $helix_term_taxonomy ) ) . '" title="' . esc_attr( $helix_current_term->name ) . '">' . esc_html( $helix_current_term->name ) . '</a>';
					$helix_term_parent_id = $helix_current_term->parent;
				}
				
				if ( !empty( $helix_taxonomy_links ) ) echo implode( ' <span class="raquo">&raquo;</span> ', array_reverse( $helix_taxonomy_links ) ) . ' <span class="raquo">&raquo;</span> ';
			
				echo esc_html( $helix_term->name ); 
			?>
		<?php } elseif (is_author()) { ?>
			<?php 
				global $wp_query;
				$curauth = $wp_query->get_queried_object();
			?>
			<?php esc_html_e('Posts by ',_THEME); echo ' ',$curauth->nickname; ?>
		<?php } elseif (is_page()) { ?>
			<?php wp_title(''); ?>
	<?php } ?>  
  </li>
</ul>
<?php endif;