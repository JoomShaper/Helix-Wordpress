<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
?>

<div class="mobile-menu pull-right btn hidden-desktop" id="sp-moble-menu">
	<i class="icon-align-justify"></i>
</div>

<nav id="sp-main-menu" class="visible-desktop pull-left" role="navigation">
	<?php
		wp_nav_menu(
			array(
				'theme_location' 	=> 'primary',
				'menu_class' 		=> 'sp-menu level-0',
				'walker' 			=> new HelixMenuWalker(),
				'container'       	=> 'div',
				'container_class' 	=> 'main-navigation'
			)
		);
	?>   
</nav>

<script type="text/javascript">
	jQuery(function($){
		mainmenu();
		
		$(window).on('resize',function(){
			mainmenu();
		});
		
		function mainmenu() {
			$('.sp-menu').spmenu({
				startLevel: 0,
				direction:'<?php echo Helix::Param('direction'); ?>',
				initOffset: {
					x:<?php echo Helix::Param('init_x'); ?>,
					y:<?php echo Helix::Param('init_y'); ?>
				},
				subOffset: {
					x:<?php echo Helix::Param('sub_x'); ?>,
					y:'<?php echo Helix::Param('sub_y'); ?>'
				},
				center:<?php echo Helix::Param('submenu_position'); ?>
			});
		}
		
		//Mobile Menu
		$('#sp-main-menu > > ul').mobileMenu({
			defaultText:'<?php _e('--Select Menu--', _THEME); ?>',
			appendTo: '#sp-moble-menu'
		});
		
	});
	
</script>