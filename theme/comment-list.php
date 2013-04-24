<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
	
$GLOBALS['comment'] = $comment;
switch ( $comment->comment_type ) :
	case 'pingback' :
	case 'trackback' :
	// Display trackbacks differently than normal comments.
?>
<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
	<p><?php _e( 'Pingback:', _THEME ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', _THEME ), '<span class="edit-link">', '</span>' ); ?></p>
<?php
		break;
	default :
	// Proceed with normal comments.
	global $post;
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	<article id="comment-<?php comment_ID(); ?>" class="comment">
		<header class="comment-meta comment-author vcard">
			<?php
				echo get_avatar( $comment, 48 ); //Comment author avatar
				echo '<strong>' . get_comment_author_link() . '</strong>';//Comment author link
			?>
			<time datetime="<?php echo get_comment_date(); ?>">
				<i class="icon-time"></i> <?php echo get_comment_date(); ?> <?php echo get_comment_time(); ?>
			</time>
			<?php edit_comment_link( __( 'Edit', _THEME ), '<small class="pull-right edit-link">', '</small>' ); //edit link ?>
		</header><!-- .comment-meta -->

		<?php if ( '0' == $comment->comment_approved ) :  //Comment moderation ?>
			<p></p>
			<section class="alert alert-info"><?php _e( 'Your comment is awaiting moderation.', _THEME ); ?></section>
		<?php endif; ?>

		<section class="comment-content comment">
			<?php comment_text(); //Comment text ?>
		</section><!-- .comment-content -->

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', _THEME ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</article><!-- #comment-## -->
<?php
	break;
endswitch; // end comment_type check