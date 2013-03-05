<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

//[video]
if(!function_exists('video_sc')) {
	function video_sc( $atts, $content="" ){

		ob_start();
		Helix::addJS('fitvids.js', true);

		$video = parse_url($content);
		
		switch($video['host']) {
			case 'youtu.be':
				$id = trim($video['path'],'/');
				$src = 'https://www.youtube.com/embed/' . $id;
			break;
			
			case 'www.youtube.com':
			case 'youtube.com':
				parse_str($video['query'], $query);
				$id = $query['v'];
				$src = 'https://www.youtube.com/embed/' . $id;
			break;
			
			case 'vimeo.com':
			case 'www.vimeo.com':
				$id = trim($video['path'],'/');
				$src = "http://player.vimeo.com/video/{$id}";
		}
		
	?>
	
	<div id="video-<?php echo $id; ?>">
		<iframe src="<?php echo $src; ?>" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
	</div>
	<script>
		jQuery(function($){
			$("#video-<?php echo $id; ?>").fitVids();
		});
	</script>
	<?php
		$data = ob_get_clean();
		return $data;
	}
	add_shortcode( 'video', 'video_sc' );
}