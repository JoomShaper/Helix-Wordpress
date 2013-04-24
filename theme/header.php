<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"  <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"  <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"  <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->

<head>
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />
    <meta charset="<?php bloginfo( "charset" ) ?>" />
    <meta name="viewport" content="width=device-width" />
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" />
    <title><?php Helix::theTitle(); ?></title>
    <?php
		Helix::Header(); //wp_head() included
    ?>
</head>

<body id="sp-wrapper" <?php Helix::bodyClass("hfeed bg"); ?>>

<header id="sp-header-wrapper" role="banner">
    <div class="container">
		<div class="row-fluid">
			<div class="span2">
				<?php helix::addFeatures('logo'); ?>
			</div>
			<div id="sp-menu" class="span7">
				<?php helix::addFeatures('menu'); ?>
			</div>
			<div class="span3 visible-desktop">
				<?php get_search_form(); ?>
			</div>
		</div>
    </div>
</header>

<?php if ( is_home() || is_front_page() ) : ?>
	<div id="sp-feature-wrapper">
		<div class="container">
			<?php Helix::addWidgets(array('feature')); ?>
		</div>
	</div>
<?php endif; ?>

<?php if ( Helix::hasWidgets( array( 'user1', 'user2', 'user3', 'user4' ) ) ) : ?>
	<section id="sp-users-wrapper">
		<div class="container">
			<?php Helix::addWidgets( array( 'user1', 'user2', 'user3', 'user4' ) ); ?>
		</div>
	</section>
<?php endif; ?>

<section id="sp-main" role="main">

<div id="sp-main-body-wrapper">	
	<div class="container" id="content">
		
		<?php Helix::addFeatures('breadcrumbs'); ?>
	
		<div class="row-fluid">
		
			<?php if (Helix::hasWidgets(array('left'))) : ?>
				<aside id="leftsidebar" class="span<?php echo Helix::Param('left_sidebar_width'); ?>">
					<?php Helix::addWidgets(array('left')); ?>
				</aside>
			<?php endif; ?>
			
			<div class="span<?php echo Helix::mainWidth(); ?>">			