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
			<?php edit_post_link( __( 'Edit', _THEME ) ); ?>
		</header>	
		
		<?php //content area ?>
		<div class="entry-content clearfix">
			<?php the_content(); ?>
		</div>
		
		<footer>
			<?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
		</footer>	
	</article>
<?php endwhile; ?>