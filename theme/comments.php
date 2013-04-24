<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

//Comment List
if(!function_exists('helix_comment_list')) {
	function helix_comment_list($comment, $args, $depth) {
		include ('comment-list.php');
	}
}
?>

<div id="comments" class="comments-area">
	<?php //Password protected ?>
	<?php if ( post_password_required() ) : ?>
		<p class="alert alert-error"><?php _e( 'This post is password protected. Enter the password to view any comments.', _THEME ); ?></p>
		</div>
		<?php
			return;
		endif;
	?>

	<?php if ( have_comments() ) : ?>
		<h3 id="comments-title">
			<?php
				printf( _n( 'One comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), _THEME ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h3>

		<ol class="commentlist">
			<?php
				/* Loop through and list the comments. Tell wp_list_comments() */
				wp_list_comments( array( 'callback' => 'helix_comment_list', 'style' => 'ol' ) );
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav">
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', _THEME ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', _THEME ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

		<?php
		/* If there are no comments and comments are closed, let's leave a little note, shall we?
		 * But we only want the note on posts and pages that had comments in the first place.
		 */
		if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="alert alert-error"><?php _e( 'Comments are closed.', _THEME ); ?></p>
		<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php comment_form(''); ?>

</div><!-- #comments -->