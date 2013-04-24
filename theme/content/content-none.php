<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
?>

<article id="post-0" class="post alert alert-error">
	<header class="article-header">
		<h1 class="title"><?php _e( 'Nothing Found', _THEME ); ?></h1>
	</header>

	<div class="article-content">
		<p><?php _e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', _THEME ); ?></p>
		<?php get_search_form(); ?>
	</div>
</article>