<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
//[Testimonial]
if(!function_exists('testimonial_sc')) {
	function testimonial_sc( $atts, $content="" ){
		extract(shortcode_atts(array(
		   'name' => 'John Doe',
		   'designation' => '',
		   'email' => 'email@email.com',
		   'url' => 'http://www.joomshaper.com/'
		), $atts));
	?>
	<div class="testimonial media">
		<div class="pull-left">
			<i style="font-size:48px" class="icon-quote-<?php echo (Helix::direction()=='rtl')? 'right':'left' ?> pull-left"></i>
		</div>
		<div class="media-body">
			<div class="testimonial-text">	
				<?php echo $content; ?>
			</div>
			<p></p>
			<div class="testimonial-author-info">	
				<img class="thumbnail pull-left" src="//1.gravatar.com/avatar/<?php echo md5($email); ?>?s=68&amp;r=pg&amp;d=mm" width="48" alt="<?php echo $name; ?>" />
				<div>
					<p><strong><?php echo $name; ?></strong><br />
					<?php echo $designation; ?><br />
					<a href="<?php echo $url; ?>"><?php echo $url; ?></a></p>
				</div>
			</div>
		</div>
	</div>
	<?php 
	}
	add_shortcode( 'testimonial', 'testimonial_sc' );
}