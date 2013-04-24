<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
?>
<?php while (have_posts()) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title page-header"><?php the_title(); ?></h1>
			<?php if ( is_sticky() && is_home() && ! is_paged() ) :  //Featured ?>
				<span class="label label-info"><?php _e( 'Featured', _THEME ); ?></span>
			<?php endif; ?>
			<?php edit_post_link( __( 'Edit', _THEME ) ); ?>
		</header>
		<?php echo Helix::content_meta();//entry meta ?>
		
		<?php //content area ?>
		<div class="entry-content clearfix">
			<?php the_content(); ?>
		</div>
		
		<?php //Author imformation ?>
		<?php if ( is_singular() && get_the_author_meta( 'description' ) && is_multi_author() ) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries. ?>
			<div class="author-info clearfix">
				<div class="author-avatar">
					<?php echo get_avatar( get_the_author_meta( 'user_email' ) ); ?>
				</div><!-- .author-avatar -->
				<div class="author-description">
					<strong><?php printf( __( 'About %s', _THEME ), get_the_author() ); ?></strong>
					<p>
						<?php the_author_meta( 'description' ); ?>
						<a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
							<?php printf( __( 'View all posts by %s', _THEME ), get_the_author() ); ?><!-- .author-link	-->
						</a>
					</p>
				</div><!-- .author-description -->
			</div><!-- .author-info -->
		<?php endif; ?>		
		
		<nav class="nav-single clearfix">
			<ul class="pager">
				<li class="previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', _THEME ) . '</span> %title' ); ?></li>
				<li class="next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', _THEME ) . '</span>' ); ?></li>
			</ul>
		</nav><!-- .nav-single -->		
		
		<footer>
			<?php the_tags('<ul class="entry-tags"><li>','</li><li>','</li></ul>'); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', _THEME ), 'after' => '</div>' ) ); ?>
		</footer>
		
		<?php comments_template( '', true ); ?>
		
	</article>
<?php endwhile; ?>