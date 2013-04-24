<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

get_header(); ?>

<div id="page-404" class="clearfix">
	<article id="error-page">
		<div>
			<h1 class="error-code"><?php _e( '404', _THEME ); ?></h1>
			<p class="error-message">
				<?php _e( 'Search not found', _THEME ); ?><br />
				<?php _e( 'Go Back', _THEME ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="icon-home"></i> <?php _e('Home', _THEME); ?></a>
			</p>
		</div>
	</article>
</div>

<?php get_footer(); ?>