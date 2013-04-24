<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
?>

<?php if (Helix::Param('show_helix_logo')) : ?>
	<div class="helix-framework">
		<a class="helix-logo" target="_blank" title="<?php _e('Powered by Helix Framework', _THEME) ?>" href="<?php echo esc_url( 'http://www.joomshaper.com/helix-wp' ); ?>">
			<?php _e('Powered by ', _THEME) ?> <?php _e('Helix', _THEME) ?>
		</a>
	</div>
<?php endif; ?>

<?php 
if (Helix::Param('showcp'))
	echo '<span class="copyright">'.str_replace('{year}', date('Y'), Helix::Param('copyright')).'</span>';
?>

<span class="designed-by">Designed <a target="_blank" title="Premium Joomla Templates and Wordpress Themes" href="http://www.joomshaper.com">JoomShaper</a></span>		

<?php if (Helix::Param('wpcredit')) : ?>
	<span class="powered-by"><?php _e('Powered by ') ?><a target="_blank" href="<?php echo esc_url( __( 'http://wordpress.org/' ) ); ?>"><?php _e( 'Wordpress' ); ?></a></span>
<?php endif; ?>

<?php if(Helix::Param('validator')) : ?>
	<span class="validation-link"><?php _e('Valid', _THEME) ?> <a target="_blank" href="http://validator.w3.org/check/referer">XHTML</a> <?php _e('and', _THEME) ?> <a target="_blank" href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">CSS</a></span>
<?php endif;