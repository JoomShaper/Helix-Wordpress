<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
?>
<?php while ( have_posts() ) : the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h2 class="entry-title">
			<?php if (Helix::Param(Helix::view(). '_linked_title')) { ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
			<?php } ?>	
			
			<?php the_title(); ?></a>
			
			<?php if (Helix::Param(Helix::view(). '_linked_title')) { ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
			<?php } ?>		
		</h2>

		<?php if ( is_sticky() && is_home() && ! is_paged() ) :  //Featured ?>
			<span class="label label-info"><?php _e( 'Featured', _THEME ); ?></span>
		<?php endif; ?>

		<?php // Edit Link ?>
		<?php edit_post_link( __( 'Edit', _THEME ) ); ?>
		
	</header>
	
	<?php echo Helix::content_meta();//entry meta ?>
	
	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content clearfix">
	
			<?php if (has_post_thumbnail()) : ?>
				<div class="post-thumbnail">
					<?php  the_post_thumbnail(); ?>
				</div>
			<?php endif; 
			
			the_content( '', FALSE );
		?>
		
	</div><!-- .entry-content -->
	<?php endif; ?>
	
	<footer>
		<?php //the_tags('<ul class="entry-tags unstyled"><li>','</li><li>','</li></ul>'); //tags ?>

		<?php if ( Helix::Param( Helix::view().'_show_readmore' ) ) : //readmore link ?>
			<a class="btn readmore" href="<?php the_permalink(); ?>"><?php echo Helix::Param( Helix::view().'_readmore_text' ); ?></a>
		<?php endif; ?>

		<?php 
			if ( comments_open() && Helix::Param( Helix::view().'_show_comment_link') ) : 
				comments_popup_link( __( Helix::Param( Helix::view().'_comment_text') ), __( Helix::Param( Helix::view().'_single_comment_text') ), __( Helix::Param( Helix::view().'_multiple_comment_text') ), 'btn comment-link' );
			endif; // comments_open()
		?>
	</footer>
</article>
<?php endwhile; ?>

<?php if ($wp_query->max_num_pages > 1) : ?>
	<nav class="post-nav">
		<ul class="pager">
			<?php if (get_next_posts_link()) : ?>
			<li class="previous"><?php next_posts_link(__('&larr; Older posts', _THEME)); ?></li>
			<?php endif; ?>
			<?php if (get_previous_posts_link()) : ?>
			<li class="next"><?php previous_posts_link(__('Newer posts &rarr;', _THEME)); ?></li>
			<?php endif; ?>
		</ul>
	</nav>
<?php endif;