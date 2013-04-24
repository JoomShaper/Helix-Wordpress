<?php
    /**
    * @package Helix
    */
    /*
    Plugin Name: Helix Framework
    Plugin URI: http://www.joomshaper.com/helix/wordpress
    Description: Plugin for developing Helix Framework based Theme.
    Author: JoomShaper
    Version: 1.0.2
    Author URI: http://www.joomshaper.com/
    Copyright (c) 2010 - 2013 JoomShaper
    License http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later	
    */		

    class Helix {

        private $importedFiles=array();
        private $importedJS=array();
        private static $_instance;
        private static $_less;

        /**
        * making self object for singleton method
        *
        */
        final public static function getInstance()
        {

            if( !self::$_instance ){
                self::$_instance = new self();

                //Initiate widget
                add_action('init', 'Helix::init');

                //Initiate widgets
                add_action( 'widgets_init', 'Helix::widgets_init' );

                //Options
                if( is_admin() ) self::getInstance()->import('options');	

                //Initiate widgets
                self::getInstance()->importWidgets();

                //Initiate shortcodes
                self::getInstance()->shortCodes();

                //Support shortcode in text widget
                add_filter('widget_text', 'do_shortcode');

                //Import all required files	
                self::getInstance()->import('classes/menu');
                self::getInstance()->import('classes/custom-class-widget');
                self::getInstance()->import('classes/show-hide-widgets');

                //Initiate less
                self::getInstance()->lessInit();

                //Show or Hide Front Admin Bar
                if( self::getInstance()->Param('showadminbar')=='0' ) add_filter( 'show_admin_bar', '__return_false' );

                // This theme uses a custom image size for featured images, displayed on "standard" posts.
                add_theme_support( 'post-thumbnails' );
                set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop

                //register menus
                register_nav_menus(
                    array(
                        'primary' => __( 'Primary Menu' )
                    )
                );

                //add required css and js files
                if( !is_admin() ){
                    self::getInstance()->bootstrap();
                    self::getInstance()->addJS(array('helix.core.js','modernizr.min.js'));

                    if(self::getInstance()->isIE(8)) {
                        self::getInstance()->addJS(array('selectivizr-min.js','respond.min.js'));
                    }
                }

            }
            return self::$_instance;
        }	

        /**
        * Get theme path
        *
        */
        public static function themePath()
        {
            return get_template_directory();
        }

        /**
        * Get theme path
        *
        */
        public static function themeURI()
        {
            return get_template_directory_uri();
        }

        /**
        * Get Helix path
        *
        */		
        public static function pluginPath()
        {
            return  dirname(__FILE__);
        }

        /**
        * Get Helix url
        *
        */
        public static function pluginURI()
        {
            return plugins_url() . '/'.basename(dirname(__FILE__));
        }		

        /**
        * Add unfiltered html option for custom text widget
        * 
        * @return self
        */
        public static function init(){
            $user_id = get_current_user_id();
            $user = new WP_User( $user_id );
            $user->add_cap( 'unfiltered_html' );
            return self::getInstance();
        }


        /**
        * widgets_init
        * 
        * @return self
        */		
        public static function widgets_init() {

            foreach (self::getInstance()->Positions() as $position) {
                register_sidebar( array(
                        'name' => $position,
                        'id' => $position,
                        'before_widget' => '<div id="%1$s" class="sp-widget %2$s">',
                        'before_title' => '<h3>',
                        'after_title' => '</h3><div class="sp-widget-content">',
						'after_widget' => '</div></div>',
                    )); 		
            }
            return self::getInstance();
        }

        /*
        * Blog Title	
        *
        * @retun self
        */
        public static function theTitle(){
            global $page, $paged;
            if (Helix::Param('show_blog_title') && Helix::Param('blog_title_position')=='left')  bloginfo( 'name' );

            wp_title( (Helix::Param('show_blog_title')) ? Helix::Param('blog_title_separator') : '', true, Helix::Param('blog_title_position'));

            if (Helix::Param('show_blog_title') && Helix::Param('blog_title_position')=='right') bloginfo( 'name' );

            // Add the blog description for the home/front page.
            if (Helix::Param('show_tagline')) {
                $tagline = get_bloginfo( 'description', 'display' );
                if ( $tagline && ( is_home() || is_front_page() ) )
                    echo ' '. Helix::Param('blog_title_separator') . ' ' . $tagline;
            }
            // Add a page number if necessary:
            if ( $paged >= 2 || $page >= 2 )
                echo ' ' . Helix::Param('blog_title_separator'). sprintf( __( ' Page %s', _THEME ), max( $paged, $page ) );

            return self::getInstance();	
        }		

        /**
        * Theme Options
        * 
        * @return self
        */	
        public static function Options()
        {
            settings_fields( _THEME.'_options' );
            $xml = simplexml_load_file(self::getInstance()->themePath(). '/theme_options.xml');
            $xml = $xml->config[0];
            return self::getInstance()->makeFormField($xml);
        }

        /*
        * Widget positions
        *
        * @return array
        */
        public static function Positions()
        {
            $xml = simplexml_load_file(self::getInstance()->themePath(). '/theme_options.xml');
            $positions = (array)$xml->positions[0];
            return $positions['position'];
        }

        /*
        * Wordpress Header
        *
        * @return self	
        */
        public static function Header() {

            /* We add some JavaScript to pages with the comment form
            * to support sites with threaded comments (when in use).
            */
            if ( is_singular() && get_option( 'thread_comments' ) )
                wp_enqueue_script( 'comment-reply' );

            wp_head();//initiate worpress head

            self::getInstance()->getFonts();

            if (self::getInstance()->Param('layout_mode')!='responsive') {
                echo '<style tyle="text/css">.container {max-width: ' . self::getInstance()->Param('layout_width') . 'px;}</style>';
            }

            return self::getInstance();
        }

        /*
        * Get current view
        *
        * @return string	
        */
        public static function view() {
            $prefix = '';
            if (is_category()) {
                $prefix = 'category';
            } else if (is_search()) {
                $prefix = 'search';
            } else if (is_tag()) {
                $prefix = 'tag';
            } else if (is_author()) {
                $prefix = 'author';			
            } else if (is_archive()) {
                $prefix = 'archive';
            } else if (is_single()) {
                $prefix = 'post';
            } else if (is_page()) {
                $prefix = 'page';
            } else if (is_home() || is_front_page()) {
                $prefix = 'blog';
            }
            return $prefix;
        }

        /*
        * Content Tags
        *
        * @return self
        */
        public static function content_tags() {
            echo get_the_tag_list( '<div class="content-tags"><i class="icon-tags"></i> ', '', '</div>' );
            return self::getInstance();
        }

        /*
        * Content Metas
        *
        * @return self
        */		
        public static function content_meta($cls='') {

            $str = self::getInstance()->Param( self::getInstance()->view(). '_meta' );
            if (self::getInstance()->Param(self::getInstance()->view(). '_show_meta')) {

                $str = preg_replace("/\{icon-(.*?)\}/", '<i class="icon-$1"></i>', $str);

                $category_list = get_the_category_list( __( ', ', _THEME ) );

                $comment = '';

                $date = sprintf( '<time datetime="%1$s">%2$s</time>',
                    esc_attr( get_the_date( 'c' ) ),
                    esc_html( get_the_date( self::getInstance()->Param( self::getInstance()->view(). '_date_format' ) ) )
                );

                $author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" role="author">%3$s</a></span>',
                    esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                    esc_attr( sprintf( __( 'View all posts by %s', _THEME ), get_the_author() ) ),
                    get_the_author()
                );

                if ( comments_open() ) : 

                    $num_comments = get_comments_number();

                    if ( $num_comments == 0 ) {
                        $_comments = __('0 Comment', _THEME);
                    } elseif ( $num_comments > 1 ) {
                        $_comments = $num_comments . __(' Comments', _THEME);
                    } else {
                        $_comments = __('1 Comment', _THEME);
                    }

                    $comment = '<a href="' . get_comments_link() . '">' . $_comments. '</a>';
                    endif; // comments_open()

                $arr = array(
                    '{category}' => $category_list,
                    '{date}' => $date,
                    '{author}' => $author,
                    '{comment}' => $comment
                );
                return '<div class="entry-meta muted '.$cls.'" role="contentinfo">' . strtr($str,$arr) . '</div>';
            }
        }

        /*
        * Content Navigation
        *
        * @return self
        */		
        public static function content_nav( $id='' ) {
            global $wp_query;

            $id = esc_attr( $id );

            if ( $wp_query->max_num_pages > 1 ) : ?>
            <nav id="<?php echo $id; ?>" class="navigation">
                <div class="nav-previous pull-left"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', _THEME ) ); ?></div>
                <div class="nav-next pull-right"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', _THEME ) ); ?></div>
            </nav>
            <?php endif;
            return self::getInstance();
        }		

        /*
        * Theme Params
        *
        * @return object
        */
        public static function Params($is_array=false) {
            if ($is_array) return get_option( _THEME.'_theme_options' );
            return (object) get_option( _THEME.'_theme_options' );
        }

        /*
        * Theme Param
        *
        * @return array
        */
        public static function Param($key=false) {
            $options = get_option( _THEME.'_theme_options' );

            if(false===$key) return $options;

            if (isset($options[$key])) {
                return $options[$key];
            } else {
                return '';
            }
        }

        /**
        * Body Class
        * 
        * @param mixed $class
        * @return string
        */
        public static function bodyClass($class='')
        {
            global $_preset, $_direction;
            $classes = $_preset . ' ' . $_direction . ' ';
            $classes .= self::getInstance()->Param('layout_type') . ' ';
            body_class($classes.' '.$class);
            return self::getInstance();
        }	

        /*
        * Get Fonts
        *
        * @return self
        */
        public static function getFonts(){
            if (Helix::Param('body_font') && Helix::Param('body_font_area'))
                Helix::GoogleFont(Helix::Param('body_font'), Helix::Param('body_font_area'));

            if (Helix::Param('header_font') && Helix::Param('header_font_area'))
                Helix::GoogleFont(Helix::Param('header_font'), Helix::Param('header_font_area'));

            if (Helix::Param('others_font') && Helix::Param('others_font_area'))
                Helix::GoogleFont(Helix::Param('others_font'), Helix::Param('others_font_area'));

            return self::getInstance();	
        }

        /**
        * Add Google Font
        *
        */
        public static function GoogleFont($name, $field	) {
            $font_name = explode(':', $name);
            $font_name = str_replace('+', ' ', $font_name[0] );
            echo '<link rel="stylesheet"  href="http://fonts.googleapis.com/css?family=' . $name . '" type="text/css" media="screen" />';
            echo "<style type='text/css'>$field{font-family:'" . $font_name . "'}</style>";
            return self::getInstance();
        }	

        private static function resetCookie($name)
        {
            if(isset($_GET['reset']) && $_GET['reset']==1)
                setcookie( $name, '', time() - 3600, '/');
        }		

        /**
        * Set Presets
        * 
        */
        public static function Preset() {
            $name = get_template() . '_preset';
            self::getInstance()->resetCookie($name);

            $require = isset($_GET['preset'])?$_GET['preset']:'';

            if( !empty( $require ) ){
                setcookie( $name, $require, time() + 3600, '/');
                $current = $require;
            } 
            elseif( empty( $require ) and  isset( $_COOKIE[$name] )) {
                $current = $_COOKIE[$name];
            } else {
                $current = self::getInstance()->Param('preset');
            }
            return $current;
        }

        public static function PresetParam($name) {
            return self::getInstance()->param( self::getInstance()->Preset().$name );
        }

        /**
        * Set Direction
        * 
        */
        public static function direction() {

            $name = get_template() . '_direction';

            self::getInstance()->resetCookie($name);
            $require = isset($_GET['direction'])?$_GET['direction']:'';

            if( !empty( $require ) ){
                setcookie( $name, $require, time() + 3600, '/');
                $current = $require;
            } 
            elseif( empty( $require ) and  isset( $_COOKIE[$name] )) {
                $current = $_COOKIE[$name];
            } else {
                $current = self::getInstance()->Param('direction');
            }
            return $current;
        }

        /**
        * Convert string to slug
        *
        */
        public static function slug($text)
        {
            return preg_replace('/[^a-z0-9_]/i','-', strtolower($text));
        }

        /**
        * Initiate Less
        *
        */
        public static function lessInit() {
            self::getInstance()->import('classes/lessc.inc');//import less class file
            self::$_less = new lessc();
            return self::getInstance();
        }

        /**
        * Instance of Less
        */
        public static function less() {
            return self::$_less;
        }	

        /**
        * Set less variable using name and value
        * 
        * @param mixed $name
        * @param mixed $value
        * @return self
        */
        public static function setLessVariable($name, $value){
            self::getInstance()->less()->setVariables( array($name=>$value) );
            return self::getInstance();
        }

        /**
        * Set Less Variables using array key and value
        * 
        * @param mixed $array
        * @return self
        */
        public static function setLessVariables($array){
            self::getInstance()->less()->setVariables( $array );
            return self::getInstance();
        }

        /**
        * Auto compile less
        * 
        * @param mixed $less
        * @param mixed $css
        * @return self
        */		
        private static function autoCompileLess($less, $css) {
            // load the cache
            $cachePath = self::getInstance()->themePath().'/less/cache';
            $cacheFile = $cachePath.'/'.basename($css.".cache");

            if (file_exists($cacheFile)) {
                $cache = unserialize(file_get_contents($cacheFile));
            } else {
                $cache = $less;
            }

            $lessInit = self::getInstance()->less();
            $newCache = $lessInit->cachedCompile($cache);

            if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {

                if(!file_exists($cachePath)){
                    mkdir($cachePath);
                }

                file_put_contents($cacheFile, serialize($newCache));
                file_put_contents($css, $newCache['compiled']);
            }

            return self::getInstance();
        }		

        /**
        * Add Less
        * 
        * @param mixed $less
        * @param mixed $css
        * @return self
        */
        public static function addLess($less, $css) {
            $themepath = self::getInstance()->themePath();
            $plugpath = self::getInstance()->pluginPath();


            if( self::getInstance()->Param('less') and self::getInstance()->Param('less')=='1' ){

                if( file_exists( $themepath. "/less/".$less.".less" ) ){
                    self::getInstance()->autoCompileLess($themepath. "/less/".$less.".less", $themepath ."/css/".$css.".css");
                } 
                elseif( file_exists( $plugpath. "/less/".$less.".less") ) {
                    self::getInstance()->autoCompileLess($plugpath. "/less/".$less.".less", $plugpath ."/css/".$css.".css");
                } else {
                    return self::getInstance();
                }
            }
            self::getInstance()->addCSS( $css.'.css');
            return self::getInstance();
        }

        /**
        * Import required file/files
        *
        * @param mixed $paths
        * @return self
        */
        public static function Import($paths)
        {
            if( is_array($paths) )
                foreach((array) $paths as $file) self::_Import( $file );

            else
                self::_Import( $paths );

            return self::getInstance();
        }

        /**
        * Single file import
        *
        * @param string $path
        * @return self
        */
        private static function _Import($path)
        {
            $extension = '.php';
            $intheme  = self::getInstance()->themePath() . '/' . $path . $extension;
            $inplugin = self::getInstance()->pluginPath() . '/' . $path. $extension;

            if( file_exists( $intheme ) ){
                self::getInstance()->importedFiles[] = $intheme;
                require_once $intheme;
            } elseif( file_exists( $inplugin ) ){
                self::getInstance()->importedFiles[] = $inplugin;
                require_once $inplugin;
            }
            return self::getInstance();
        }

        /**
        * Get Imported file
        * @return array
        */
        public static function getImportedFiles()
        {
            return self::getInstance()->importedFiles;
        }

        /**
        * Add Features
        *
        */
        public static function addFeatures ($files) {
            $features         = explode(",",$files);
            foreach ($features as $feature) {
                $feature     = trim($feature);//remove whitespace
                self::getInstance()->Import("features/$feature");
            }

            return self::getInstance();
        }


        private static $resources = array();

        /**
        * Add Javascript file
        * 
        * @param mixed $sources
        * @return self
        */
        public static function addJS($sources, $direct = false, $separator=',') {

            $srcs = array();

            if( is_string($sources) ) $sources = explode($separator,$sources);
            if(!is_array($sources)) $sources = array($sources);

            foreach ($sources as $src) {
                $src = trim( $src );

                //cheack in template path
                if( file_exists( self::getInstance()->themePath() . '/js/'. $src)) {
                    $path =  self::getInstance()->themeURI().'/js/'. $src;
                }
                //if not found, then check from helix path
                elseif( file_exists( self::getInstance()->pluginPath() . '/js/' . $src ) ) {
                    $path =  self::getInstance()->pluginURI().'/js/'. $src;
                }
                else return false;

                if ($direct && !defined('js_'.$src)) {
                    echo "<script id='{$src}'  src='{$path}' type='text/javascript'></script>";
                    define('js_'.$src, 1);
                    return;
                }

                self::getInstance()->resources['js'][] = array( $src=> $path);

                add_action("init", array('Helix','loadResources'));

            }

            return self::getInstance();
        }

        public static function loadResources()
        {
            wp_enqueue_script('jquery');

            foreach( self::getInstance()->resources['js'] as $files )
            {
                foreach($files as $handle=>$src)
                {
                    wp_register_script($handle, $src);
                    wp_enqueue_script($handle);
                }
            }

            foreach( self::getInstance()->resources['css'] as $files ){
                foreach($files as $handle=>$src)
                {
                    wp_register_style($handle, $src);
                    wp_enqueue_style($handle);
                }
            }
        }

        /**
        * Add CSS
        *
        */
        public static function addCSS($sources, $direct = false, $separator=',') {

            $srcs = array();

            if( is_string($sources) ) $sources = explode(',',$sources);
            if(!is_array($sources)) $sources = array($sources);

            foreach ($sources as $src) {
                $src = trim( $src );

                //cheack in template path
                if( file_exists( self::getInstance()->themePath() . '/css/'. $src)) {
                    $path =  self::getInstance()->themeURI().'/css/'. $src;
                }
                //if not found, then check from helix path
                elseif( file_exists( self::getInstance()->pluginPath() . '/css/' . $src ) ) {
                    $path =  self::getInstance()->pluginURI().'/css/'. $src;
                }
                else return false;

                if ($direct && !defined('css_'.$src)) {
                    echo "<link rel='stylesheet' id='{$src}'  href='{$path}' type='text/css' media='all' />";
                    define('css_'.$src, 1);
                    return;
                }

                self::getInstance()->resources['css'][] = array( $src=> $path);

                add_action("init", array('Helix','loadResources') );

            }
            return self::getInstance();
        }


        /**
        * Import all shortcodes
        * 
        * @return self
        */
        public static function shortCodes()
        {

            $shortcodes = array();

            $themeshortcodes = glob( self::getInstance()->themePath().'/shortcodes/*.php' );
            $pluginshortcodes = glob( self::getInstance()->pluginPath().'/shortcodes/*.php');

            foreach((array)$themeshortcodes as $value)  $shortcodes[] =  str_ireplace( '.php','',basename($value));
            foreach((array)$pluginshortcodes as $value)  $shortcodes[] =   str_ireplace( '.php','',basename($value));


            $shortcodes = array_unique($shortcodes);

            foreach($shortcodes as $shortcode  ) self::getInstance()->Import('shortcodes/'.$shortcode);

            return self::getInstance();
        }

        /**
        * Import all widgets
        * 
        * @return self
        */
        public static function importWidgets()
        {

            $widgets = array();

            $themewidgets = glob( self::getInstance()->themePath().'/widgets/*.php' );

            if ( is_array($themewidgets) )
                foreach((array)$themewidgets as $value)  $widgets[] =  str_ireplace( '.php','',basename($value));

            $widgets = array_unique($widgets);

            foreach($widgets as $widget  ) self::getInstance()->Import('widgets/'.$widget);

            return self::getInstance();
        }


        /**
        * Add Bootstrap
        * 
        * @return self
        */
        public static function bootstrap() {
            global $_direction;

            if (basename($_SERVER['SCRIPT_FILENAME'])=='wp-login.php') return self::getInstance(); // Prevent load boostrap in login page

            self::getInstance()->addCSS( array('bootstrap.min.css', 'font-awesome.css'));

            if (self::getInstance()->Param('layout_type')=='responsive') //responsive
                self::getInstance()->addCSS( array('bootstrap-responsive.min.css'));

            if (self::getInstance()->direction()=='rtl' && !is_admin())//RTL
                self::getInstance()->addCSS( array('bootstrap-rtl.css'));

            self::getInstance()->addJS(array('bootstrap.min.js'));
            return self::getInstance();
        }

        /**
        * Has widgets
        * 
        * @param mixed $positions
        * @return boolean
        */
        public static function hasWidgets ($positions) {

            $pos = self::getInstance()->getWidgets($positions);
            return !empty($pos);
        }

        /**
        * Widget Parameters
        * 
        * @param int $widget_id
        * @return boolean
        */		
        private static function widgetParams($widget_id){

            global $wp_registered_widgets;
            $widget_obj    = $wp_registered_widgets[$widget_id];
            $widget_opt    = get_option($widget_obj['callback'][0]->option_name);
            $widget_num    = $widget_obj['params'][0]['number'];
            return isset( $widget_opt[$widget_num] )?$widget_opt[$widget_num]:false;
        }

        /**
        * Get Active Widgets
        * 
        * @param mixed $positions
        * @return array
        */

        public static function getWidgets($positions) {

            global $helix_current_menu_item;
            $activePos = array();
            $sidebars_widgets = wp_get_sidebars_widgets();

            foreach ($positions as $pos) {

                $total_active_sidebars = 1; 

                if (is_active_sidebar($pos)) {
                    $activePos[] = $pos;
                }
                $sidebar = $sidebars_widgets[$pos];
                if (is_array($sidebar)) {
                    $total_active_sidebars = count($sidebar);
                    foreach($sidebar as $widget){

                        $instance = self::getInstance()->widgetParams($widget);

                        if( isset($instance['helix_menu_types']) ){

                            $instance['helix_menu_types'] = (int) $instance['helix_menu_types'];

                            if($instance['helix_menu_types']===2){

                                if( !in_array($helix_current_menu_item, $instance['helix_menu_pages']) ){
                                    if(($key = array_search($pos, $activePos)) !== false) {
                                        $total_active_sidebars--;
                                        if( $total_active_sidebars<1 ) unset($activePos[$key]);
                                    }
                                }
                            } elseif($instance['helix_menu_types']===3) {
                                if( in_array($helix_current_menu_item, $instance['helix_menu_pages']) ){
                                    if(($key = array_search($pos, $activePos)) !== false) {
                                        $total_active_sidebars--;
                                        if( $total_active_sidebars<1 ) unset($activePos[$key]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $activePos;
        }

        /**
        * Main area width
        * 
        * @return int
        */
        public static function mainWidth() {
            $left 	= self::getInstance()->Param('left_sidebar_width');
            $right 	= self::getInstance()->Param('right_sidebar_width');
            $width = 12;

            if (self::getInstance()->hasWidgets(array('left')) && self::getInstance()->hasWidgets(array('right'))) {
                $width = 12 - ($left + $right);
            } else if (self::getInstance()->hasWidgets(array('left')) || self::getInstance()->hasWidgets(array('right'))) {

                if (self::getInstance()->hasWidgets(array('left'))) {
                    $width = 12 - $left;
                } else {
                    $width = 12 - $right;
                }
            } else {
                $width = 12;
            }
            return 	$width;
        }


        /**
        * Dynamically add widgets
        * 
        * @param mixed $modules
        * @return self
        */	
        public static function addWidgets($modules) {

            $activeMods = self::getInstance()->getWidgets($modules);

            if (count($activeMods)==1) {
                foreach ($activeMods as $pos) {
                ?>
                <div id="sp-position-<?php echo self::getInstance()->slug($pos) ; ?>">
                    <?php  dynamic_sidebar($pos); ?>
                </div>
                <?php	
                }
            } elseif (count($activeMods)>1) {
                echo '<div class="row-fluid">';
                foreach ($activeMods as $pos) {
                ?>
                <div id="sp-position-<?php echo self::getInstance()->slug($pos) ; ?>" class="span<?php echo round(12/count($activeMods)) ?>">
                    <?php  dynamic_sidebar($pos); ?>
                </div>
                <?php
                }
                echo '</div>';
            }

            return self::getInstance();
        }

        /**
        * Detect IE Version
        *
        */
        public static function isIE($version = false) {   
            $agent=$_SERVER['HTTP_USER_AGENT'];  
            $found = strpos($agent,'MSIE ');  
            if ($found) { 
                if ($version) {
                    $ieversion = substr(substr($agent,$found+5),0,1);   
                    if ($ieversion == $version) return true;
                    else return false;
                } else {
                    return true;
                }

            } else {
                return false;
            }
            if (stristr($agent, 'msie'.$ieversion)) return true;
            return false;        
        }	

        //Form Fields
        public static function makeFormField($item)
        {

            $html='';

            if( isset($item->fieldset) )
            {

                $inc1 = 0;
                $inc2 = 0;

                $html .= '<ul class="nav nav-tabs">';
                foreach($item->fieldset as $key=>$fieldset)
                {
                    $html .= '<li class="'.(($inc1==0)?'active':''). ((isset($fieldset->fields))?' dropdown':'').'">
                    <a href="#' . ((isset($fieldset->fields))?'': self::getInstance()->slug($fieldset['name']) ) . '" data-toggle="'. ((isset($fieldset->fields))?'dropdown':'tab').'" class="'. ((isset($fieldset->fields))?'dropdown-toggle':'').'"><i class="' . $fieldset['icon'] . '"></i> '. ucfirst($fieldset['name']) .'

                    '. ((isset($fieldset->fields))?'<span class="caret"></span>':'').'

                    </a>';

                    if( isset($fieldset->fields) )
                    {

                        $html .= '<ul class="dropdown-menu">';
                        foreach($fieldset->fields as $field)
                        {
                            $html .= '<li><a href="#'.self::getInstance()->slug($field['name']).'" data-toggle="tab">'. ucfirst( $field['name'] ) .'</a></li>';
                        }
                        $html .= '</ul>';
                    }

                    $html .='</li>';
                    $inc1++;
                }
                $html .= '</ul>';

                $html .='<div class="tab-content">';
                foreach($item->fieldset as $key=>$fieldset)
                {
                    $html .= '<div class="tab-pane fade '.(($inc2==0)?'in active':'').'" id="'.self::getInstance()->slug($fieldset['name']).'">';
                    $html .= self::getInstance()->generateField( $fieldset );
                    $html .= '</div>';

                    if( isset($fieldset->fields) ) {
                        foreach($fieldset->fields as $field) {
                            $html .= '<div class="tab-pane fade" id="'.self::getInstance()->slug($field['name']).'">';
                            $html .= self::getInstance()->generateField( $field );
                            $html .= '</div>';
                        }
                    }

                    $inc2++;
                }

                $html .='</div>';

            }

            return $html;
        }

        private static function generateField($fieldset)
        {
            $html = '';
            $name = '';

            foreach($fieldset->field as $field)
            {

                $fieldvalue = (self::getInstance()->Param((string)$field['name'])!='') ? self::getInstance()->Param((string)$field['name']) : (string)$field['default'];
                $fieldname = _THEME.'_theme_options['. $field['name'] .']';

                switch((string)$field['type'])
                {

                    case 'text':
                        $html .= '
                        <div class="row-fluid">
                        <div class="span2">
                        <div class="sp-inner">
                        <label rel="tooltip" for="'.self::getInstance()->slug($field['name']).'" title="'.$field['description'].'"> '.$field['label'].'
                        </label>
                        </div>
                        </div>

                        <div class="span10"><div class="sp-inner">';

                        $field['unit'] = (string) $field['unit'];

                        if( !empty($field['unit']) )
                        {


                            $html .= '<div class="input-append">
                            <input type="text"
                            id="'.self::getInstance()->slug($field['name']).'"
                            class="span3 '.$field['class'].'"
                            name="'.$fieldname.'"
                            style="'.$field['style'].'"
                            value="'.$fieldvalue.'"><span class="add-on">'.$field['unit'].'</span>
                            </div>';

                        } else {

                            $html .= '<input type="text"
                            id="'.self::getInstance()->slug($field['name']).'"
                            class="span3 '.$field['class'].'"
                            name="'.$fieldname.'"
                            style="'.$field['style'].'"
                            value="'.$fieldvalue.'">';
                        }

                        $html .= '
                        </div>
                        </div>
                        </div>';
                        break;

                    case 'checkbox':

                        $html .= '<li>';
                        $html .= '<label class="hasTip" for="'.$field['id'].'" title="'.$field['label'].'::'.$field['description'].'"> <input type="checkbox"
                        id="'.$field['id'].'"
                        class="'.$field['class'].'"
                        name="'.$fieldname.'"
                        '.(($fieldvalue==$field['value'])?' checked="checked" ':'').'
                        style="'.$field['style'].'"
                        value="'.$field['value'].'">  '.$field['label'].'  </label>   </li>';
                        break;

                    case 'list':

                        $html .= '
                        <div class="row-fluid">
                        <div class="span2">
                        <div class="sp-inner">
                        <label rel="tooltip" for="'.self::getInstance()->slug($field['name']).'" title="'.$field['description'].'"> '.$field['label'].'
                        </label>
                        </div>
                        </div>

                        <div class="span10"><div class="sp-inner">';

                        $html .= '<select
                        id="'.self::getInstance()->slug($field['name']).'"
                        class="'.$field['class'].'"
                        name="'.$fieldname.'"
                        '.(($field['multiple']=='1')?' multiple="multiple" ':'').'
                        '.(($field['size']>1)?' size="'.$field['size'].'" ':'').'
                        style="'.$field['style'].'">';

                        foreach($field->option as $option)
                        {
                            $html .= '<option ';
                            $html .= (($fieldvalue==$option['value'])?' selected="selected" ':'');
                            $html .='value="'.$option['value'].'">'.$option.'</option>';
                        }
                        $html .= '</select>';

                        $html .= '
                        </div>
                        </div>
                        </div>';

                        break;

                    case 'category':

                        $html .= '
                        <div class="row-fluid">
                        <div class="span2">
                        <div class="sp-inner">
                        <label rel="tooltip" for="'.self::getInstance()->slug($field['name']).'" title="'.$field['description'].'"> '.$field['label'].'
                        </label>
                        </div>
                        </div>

                        <div class="span10"><div class="sp-inner">';

                        $html .= '<select
                        class="'.$field['class'].'"
                        name="'.(($field['multiple']=='1')?$fieldname.'[]':$fieldname).'"
                        '.(($field['multiple']=='1')?' multiple="multiple" ':'').'
                        '.(($field['size']>1)?' size="'.$field['size'].'" ':'').'
                        style="'.$field['style'].'">';

                        $categories = get_categories( array(
                                'type'                     => 'post',
                                'child_of'                 => 0,
                                'parent'                   => '',
                                'orderby'                  => 'name',
                                'order'                    => 'ASC',
                                'hide_empty'               => 1,
                                'hierarchical'             => 1,
                                'exclude'                  => '',
                                'include'                  => '',
                                'number'                   => '',
                                'taxonomy'                 => 'category',
                                'pad_counts'               => false 
                            ) );

                        if( $field['multiple']=='1' ){
                            $fieldvalue = (self::getInstance()->Param((string)$field['name'])!='') ? (array) self::getInstance()->Param((string)$field['name']) : explode(',',(string)$field['default']);
                        }

                        foreach($categories as $category)
                        {
                            $html .= '<option ';
                            if( $field['multiple']=='1' ){
                                $html .= ( in_array($category->term_id,$fieldvalue)?' selected="selected" ':'');
                            } else {
                                $html .= (($fieldvalue==$category->term_id)?' selected="selected" ':''); 
                            }

                            $html .='value="'.$category->term_id.'">'.$category->name.'</option>';
                        }
                        $html .= '</select>';

                        $html .= '
                        </div>
                        </div>
                        </div>';

                        break;

                    case 'radio':

                        $html .= '
                        <div class="row-fluid">
                        <div class="span2">
                        <div class="sp-inner">
                        <label rel="tooltip" for="'.self::getInstance()->slug($field['name']).'" title="'.$field['description'].'"> '.$field['label'].'
                        </label>
                        </div>
                        </div>

                        <div class="span10"><div class="sp-inner">';

                        $html .= '<fieldset id="'.self::getInstance()->slug($field['name']).'" class="radio btn-group">';

                        $i=0;
                        foreach($field->option as $key=>$option)
                        {
                            $i++;

                            $option['value'] = (string) $option['value'];

                            $input = '<input style="display:none" type="radio" id="'.self::getInstance()->slug($field['name'].$i).'"
                            name="'.$fieldname.'" value="'.$option['value'].'"';
                            $input .= (($fieldvalue==$option['value'])?' checked="checked" ':'');
                            $input .='>';

                            $html .= '<label class="';
                            $html .= (($fieldvalue==$option['value'])?' active btn-success':'');

                            $html .= '">'.$option. $input.  '</label>';
                        }
                        $html .= ' </fieldset>';
                        $html .= '</div></div>
                        </div>';
                        break;

                    case 'textarea':

                        $html .= '
                        <div class="row-fluid">
                        <div class="span2"><div class="sp-inner">
                        <label rel="tooltip" for="'.self::getInstance()->slug($field['name']).'" title="'.$field['description'].'"> '.$field['label'].'
                        </label>
                        </div>
                        </div>

                        <div class="span10"><div class="sp-inner">';

                        $html .= '<textarea
                        id="'.self::getInstance()->slug($field['name']).'"
                        class="span5 '.$field['class'].'"
                        name="'.$fieldname.'"
                        style="'.$field['style'].'">'.$fieldvalue.'</textarea> ';

                        $html .= '</div></div>
                        </div>';

                        break;

                    case 'picker':

                        $html .='<script type="text/javascript">
                        jQuery(function($){

                        $("#' . self::getInstance()->slug($field['name']) . '").spectrum({
                        color: "' . $fieldvalue . '",
                        showInput:true,
                        showAlpha:false,
                        showPalette: true,
                        clickoutFiresChange: true,
                        palette: [["'. $field['default'] .'", "'.$fieldvalue.'" ]]
                        });

                        });							
                        </script>';

                        $html .= '
                        <div class="row-fluid">
                        <div class="span2">
                        <div class="sp-inner">
                        <label rel="tooltip" for="'.self::getInstance()->slug($field['name']).'" title="'.$field['description'].'"> '.$field['label'].'
                        </label>
                        </div>
                        </div>

                        <div class="span10"><div class="sp-inner">';

                        $html .= '<input type="text"
                        id="'.self::getInstance()->slug($field['name']).'"
                        class="span3 color-picker '.$field['class'].'"
                        name="'.$fieldname.'"
                        style="'.$field['style'].'"
                        value="'.$fieldvalue.'">';

                        $html .= '
                        </div>
                        </div>
                        </div>';
                        break;

                    case 'button':

                        $html .= '
                        <div class="row-fluid">
                        <div class="span2">
                        <div class="sp-inner">
                        <label rel="tooltip" for="'.self::getInstance()->slug($field['name']).'" title="'.$field['description'].'"> '.$field['label'].'
                        </label>
                        </div>
                        </div>

                        <div class="span10"><div class="sp-inner">';

                        $html .= '<button class="btn" type="button"
                        '.(isset($field['id'])?' id="'.$field['id'].'"':'').'
                        '.(isset($field['data-complete-text'])?' data-complete-text="'.$field['data-complete-text'].'"':'').'
                        '.(isset($field['data-loading-text'])?' data-loading-text="'.$field['data-loading-text'].'"':'').'>'. (isset($field['icon'])?'<i class="'.$field['icon'].'"></i> ':'') . $field['label'] .'</button>';

                        $html .= '
                        </div>
                        </div>
                        </div>';
                        break;

                    case 'preset':

                        $html .= '
                        <div class="row-fluid">
                        <div class="span2">
                        <div class="sp-inner">
                        <label rel="tooltip" for="'.self::getInstance()->slug($field['name']).'" title="'.$field['description'].'"> '.$field['label'].'
                        </label>
                        </div>
                        </div>

                        <div class="span10"><div class="sp-inner">';

                        $html .= '<ul
                        id="'.self::getInstance()->slug($field['name']).'"
                        class="'.$field['class'].'" style="'.$field['style'].'">';

                        foreach($field->option as $option)
                        {
                            $html .= '<li data-preset="'.$option['value'].'" class="presets';
                            $html .= (($fieldvalue==$option['value'])?' active-preset':'');
                            $html .= '">';


                            $html .= '<div class="preset-title">'. $option .'</div>';
                            $html .= '<div data-preset="'.$option['value'].'" class="preset-contents"><label>';
                            $html .= '
                            <input style="display:none" ';
                            $html .= (($fieldvalue==$option['value'])?' checked="checked" ':'');
                            $html .= ' value="'.$option['value'].'" type="radio" name="'.$fieldname.'">
                            <img src="'.self::getInstance()->themeURI().'/images/presets/'.$option['value'].'/thumbnail.png" alt="'. $option .'">';
                            $html .= '</label></div>';

                            $html .= '</li>';

                        }
                        $html .= '</ul>';

                        $html .= '
                        </div>
                        </div>
                        </div>';

                        $html .='<script type="text/javascript">
                        jQuery(function($){

                        $("#preset > li.presets").on("click", function(event){
                        event.stopImmediatePropagation();

                        $(this).closest("#preset").find(">.presets").removeClass("active-preset");
                        $(this).addClass("active-preset");

                        $(".presetoptions > ul").hide();
                        $(".presetoptions > ul."+$(this).data("preset")+"").show();

                        });

                        });                            
                        </script>';

                        break; 

                    case 'presetoption':

                        $html .= '
                        <div class="row-fluid">
                        <div class="span2">
                        <div class="sp-inner">
                        <label rel="tooltip" title="'.$field['description'].'"> '.$field['label'].'
                        </label>
                        </div>
                        </div>

                        <div class="span10"><div class="sp-inner presetoptions">';

                        foreach($field->select as $select)
                        {
                            $html .= '<ul
                            class="'.$select['class'].' '.self::getInstance()->slug($select['name']).'"
                            style="display:';
                            $html .= ($fieldvalue==$select['name'])?'block':'none';
                            $html .= '" data-fieldvalue="'.$fieldvalue.'" >';
                            foreach($select->option as $option)
                            {
                                $html .= '<li>';
                                $name = (string) $select['name'].'_'.$option['name'];
                                $value = (self::getInstance()->Param($name) ? self::getInstance()->Param($name) : (string)$option['value']);
                                $html .= '<label class="">'.$option.'</label>';
                                $fieldname = _THEME.'_theme_options['.$select['name'].'_'.$option['name'].']';
                                $html .= '<input id="'.$name.'" type="text" name="'.$fieldname.'" 
                                value="'.$value.'">';

                                $html .='<script type="text/javascript">
                                jQuery(function($){
                                $("#' . $name . '").spectrum({
                                color: "' . $value . '",
                                showInput:true,
                                showAlpha:false,
                                showPalette: true,
                                clickoutFiresChange: true,
                                palette: [[ "'. $option['value'] .'", "'.$value.'" ]]
                                });

                                });                            
                                </script>';
                                $html .= '</li>';
                            }
                            $html .= '</ul>';
                        }
                        $html .= '
                        </div>
                        </div>
                        </div>';

                        break;

                    case 'include':
                        $file = $field['file'];
                        $tpl_path = self::getInstance()->themePath() . '/' . $file;
                        $helix_path = self::getInstance()->pluginPath() . '/' . $file;

                        ob_start();

                        if( file_exists( $tpl_path ) ) include $tpl_path;
                        else if ( file_exists( $helix_path ) ) include $helix_path;

                            $html .= ob_get_clean();

                        break;
                }
            }

            return $html ;

        }		

}