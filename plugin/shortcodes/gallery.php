<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

//[Gallery]
if(!function_exists('gallery_sc')) {
	$galleryArray = array();
	function gallery_sc( $atts, $content="" ){
		
		
		
		$tags = array();
		
		extract(shortcode_atts(array(
			'order'     => 'ASC',
			'orderby'   => 'menu_order ID',
			'ids' 		=> '',
			'columns' 	=> 3,
			'modal' 	=> 'yes',
			'filter' 	=> 'no'
		), $atts));		
		
		$_posts = get_posts( array('include' => $ids, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		
		$tags = '';
		
		foreach ($_posts as $key=>$item) $tags .= ',' . $item->post_excerpt;
		
		$tags = explode(' ', $tags);
		$tags = implode(',', $tags);
		$tags = ltrim($tags, ',');
		$tags = explode(',', $tags);
		$newtags = array();
		foreach($tags as $tag) $newtags[] = trim($tag);
		$tags = array_unique($newtags);
		
		ob_start();
		
		//Add gallery.css file
		Helix::addCSS('gallery.css', true);
		//isotope
		if($filter=='yes')
			Helix::addJS('jquery.isotope.min.js', true);
		
		if($filter=='yes') {
		?>
		
		<div class="gallery-filters btn-group">
			<a class="btn active" href="#" data-filter="*"><?php _e('Show All', _THEME); ?></a>
			<?php foreach ($tags as $tag) { ?>		  
				<a class="btn" href="#" data-filter=".<?php echo trim($tag) ?>"><?php echo ucfirst(trim($tag)) ?></a>
			<?php } ?>
		</div>
		<?php } ?>
		
		<ul class="gallery">
			<?php foreach ($_posts as $key=>$item) { ?>	
				<li style="width:<?php echo round(100/$columns); ?>%" class="<?php echo str_replace(',', ' ', $item->post_excerpt) ?>">
					<a class="img-polaroid" data-toggle="modal" href="<?php echo ($modal=='yes')? '#modal-' . $key . '':'#' ?>">
						<?php
							$att = wp_get_attachment_image_src($item->ID, 'thumbnail');
							echo '<img alt=" " src="' . $att[0] . '" width="100%" />';
						?>
						
						<?php if($item->post_content !='') { ?>
							<div>
								<div>
									<h4><?php echo $item->post_title; ?></h4>
									<p><?php echo do_shortcode($item->post_content); ?></p>
								</div>
							</div>
						<?php } ?>
					
					</a>
				</li>
				
				<?php if($modal=='yes') { ?>
				<div id="modal-<?php echo $key; ?>" class="modal hide fade" tabindex="-1">
					<a class="close-modal" href="javascript:;" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></a>
					<div class="modal-body">
						<?php
							$att = wp_get_attachment_image_src($item->ID, 'large');
							echo '<img alt=" " src="' . $att[0] . '" width="100%" style="max-height:400px" />';
						?>
					</div>
				</div>
				<?php } ?>
				
			<?php } ?>
		</ul>
				
		<?php if($filter=='yes') { ?>
			<script type="text/javascript">
			
			
				jQuery(function($){
				
					$(window).load(function() {
					
						$gallery = $('.gallery');
						$gallery.isotope({
						  // options
						  itemSelector : 'li',
						  layoutMode : 'fitRows'
						});
						
						$filter = $('.gallery-filters');
						$selectors = $filter.find('>a');
						
						$filter.find('>a').click(function(){
							var selector = $(this).attr('data-filter');
						
							$selectors.removeClass('active');
							$(this).addClass('active');
							
							$gallery.isotope({ filter: selector });
						  return false;
						});
						
					});
					
				});
			</script>
		<?php } 
		return ob_get_clean();
	}
	
	add_shortcode( 'gallery', 'gallery_sc' );
}