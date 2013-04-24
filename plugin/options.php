<?php
/**
 * @package Helix Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2013 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

add_action( 'admin_init', 'theme_options_init' );//Initiate theme option
add_action( 'admin_menu', 'theme_options_add_page' );//Initiate Menu

//CSS and JS to Admin
if( isset($_GET['page']) && $_GET['page']=='theme_options') {
	Helix::bootstrap()
	->addCSS(array('admin/master.css', 'admin/spectrum.css'))
	->addJS(array('admin/spectrum.js', 'admin/helix.admin.js'));
}

/**
* Init plugin options to white list our options
*/
function theme_options_init(){
	register_setting( _THEME.'_options', _THEME.'_theme_options' );
}

/**
* Load up the menu page
*/
function theme_options_add_page() {
	add_menu_page(
		__( wp_get_theme()->Name .' Settings'), //Page Title
		__( wp_get_theme()->Name ), //Menu Title
		'edit_theme_options',
		'theme_options',
		'theme_options_form',
		Helix::pluginURI(). '/images/helix-icon.png',
		61
	);
}

/**
* Create the options page
*/
function theme_options_form() { ?>

    <form method="post" action="options.php">

        <div id="helix-options" class="wrap clearfix">
            <div class="navbar clearfix">
                <ul class="unstyled">
                    <li><a href="#" onClick="jQuery(this).closest('form').submit(); return false;"><i class="icon-ok-sign color5"></i> Save</a></li>
                    <li><a href="<?php echo admin_url() ?>"><i class="icon-remove-sign color4"></i> Close</a></li>
                    <li><a target="_blank" href="http://www.joomshaper.com/helix/wordpress"><i class="icon-question-sign color3"></i> Help</a></li>
                </ul>
            </div>

            <?php if (isset($_GET['settings-updated']) && false !== $_GET['settings-updated'] ) : ?>
				
				<?php
					//delete all less cache files
					$lessCaches = glob( Helix::themePath().'/less/cache/*.cache' );
					
					foreach($lessCaches as $lessCache) {
						unlink($lessCache);
					}
				?>				
			
                <div class="alert alert-success fade in" style="margin:10px;">
                    <button type="button" class="close" data-dismiss="alert"> &times; </button>
                    <strong><?php _e( 'Options saved', _THEME ); ?></strong>
                </div>
                <?php endif; ?>

				<div class="row-fluid">
					<div class="sp-theme-title clearfix">
						<?php echo "<h2 style='display:block;margin:0 10px 10px'>" . wp_get_theme() . __( ' Theme Options', _THEME ) . "</h2>"; ?>
					</div>
				</div>
            <?php
                echo Helix::Options();
            ?>
        </div>
    </form>
    <?php
}