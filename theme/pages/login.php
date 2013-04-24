<?php
/**
 * Template Name: Login Form
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

get_header(); ?>
<div id="page-login" class="row-fluid">

	<header class="entry-header">
		<h1 class="entry-title page-header"><?php the_title(); ?></h1>
		<?php edit_post_link( __( 'Edit', _THEME ) ); ?>
	</header>
	<?php the_post(); ?>
	
	<?php the_content(); ?>
	
	<?php wp_get_current_user(); ?>
		
	<?php if (is_user_logged_in()) { ?>
		<?php _e('Hi', _THEME); ?> <?php echo $current_user->user_login; ?> <br />
		<a class="btn" href="<?php echo wp_logout_url( get_bloginfo('url') ); ?>">Logout</a>
	<?php } else { ?>
		<!-- Login form strat here -->
		<form name="loginform" action="<?php echo home_url('/wp-login.php'); ?>" method="post" name="login" id="form-login" class="form-horizontal">
		  <div class="control-group">
			<label class="control-label" for="inputEmail">Email</label>
			<div class="controls">
			  <input type="text" name="log" id="inputEmail" placeholder="<?php _e('Username', _THEME); ?>" />
			</div>
		  </div>
		  <div class="control-group">
			<label class="control-label" for="inputPassword">Password</label>
			<div class="controls">
				<input type="password" id="inputPassword" name="pwd" placeholder="<?php _e('Password', _THEME); ?>" />
			</div>
		  </div>
		  <div class="control-group">
			<div class="controls">
			  <label class="checkbox">
				<input type="checkbox"> Remember me
			  </label>
			  <button type="submit" class="btn" name="wp-submit" value="<?php _e('Login', _THEME); ?>">Sign in</button>
				
				<input type="hidden" name="redirect_to" value="<?php echo home_url('/wp-admin'); ?>">
				
				<input type="hidden" name="testcookie" value="1">
			</div>
		  </div>
		  <div class="controls">
			<ul>
				<li>
					<a href="<?php echo home_url(); ?>/wp-login.php?action=lostpassword" title="<?php _e('Password Lost and Found', _THEME); ?>"><?php _e('Lost your password?', _THEME); ?></a>
				</li>
				
				<?php if(get_option('users_can_register')) { ?>
				<li>
					<a href="<?php echo home_url(); ?>/wp-login.php?action=register" title="<?php _e('Not a member? Register', _THEME); ?>"><?php _e('Register', _THEME); ?></a>
				</li>
				<?php } ?>
			</ul>
		  </div>
	</form>
	<?php } ?>
</div>
<?php get_footer(); ?>