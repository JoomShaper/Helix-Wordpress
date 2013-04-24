<?php
    /**
    * @package Helix Framework
    * @author JoomShaper http://www.joomshaper.com
    * @copyright Copyright (c) 2010 - 2013 JoomShaper
    * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
    */
    add_action('in_widget_form', 'helix_showhide_widgets_form', 10, 3);
    add_filter('widget_update_callback', 'helix_showhide_widget_update_callback', 10, 3);
    add_filter('widget_display_callback', 'helix_showhide_widgets');

    $helix_current_menu_item=0;
	
    /**
    * Make Associative array to multidimensional array
    * 
    * @param array $items
    * @return array
    */
    function buildTree($items) {
        $childs = array();
        foreach($items as $item){
            $childs[$item->menu_item_parent][] = $item;
        }

        foreach($items as $item){
            if (isset($childs[$item->ID])){
                $item->children = $childs[$item->ID];
            }
        } 
        return $childs[0];
    }
    /**
    * Show tree or indent
    * 
    * @param mixed $widget
    * @param mixed $pages
    * @param mixed $items   generated tree
    * @param mixed $indent, default is 0
    */
    function showTree($widget,$pages, $items, $indent=0)
    {
        foreach($items as $menu_item){
            $url = $menu_item->ID;
            $title = $menu_item->title;
            echo '<div style="display:block; margin:5px 0"><span>'.str_repeat(' &nbsp; ', $indent).'</span><label>
            <input name="'.$widget->get_field_name('helix_menu_pages').'[]" type="checkbox"
            '. (in_array($url,$pages)?'checked':'') .' value="'.$url.'"> '.$title.'</label></div>';

            if( isset($menu_item->children) ) showTree($widget,$pages, $menu_item->children, $indent+3 );
        }
    }

    /**
    * On widget form
    * 
    * @param mixed $widget
    * @param mixed $return
    * @param mixed $instance
    */
    function helix_showhide_widgets_form($widget,$return,$instance){
        global $treehtml;
        $types = isset($instance['helix_menu_types'])?$instance['helix_menu_types']:'';
        $pages = isset($instance['helix_menu_pages'])?$instance['helix_menu_pages']:array();

        $menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
        $menu_items = array();
        foreach($menus as $menu) $menu_items[$menu->name] = wp_get_nav_menu_items($menu->term_id);

    ?>
    <hr>
    <p>
        <label for="<?php echo $widget->get_field_id('helix_menu_types'); ?>"><?php _e('Show / Hide Widget', _THEME) ?></label>
        <select name="<?php echo $widget->get_field_name('helix_menu_types'); ?>" id="<?php echo $widget->get_field_id('helix_menu_types'); ?>" class="widefat showhidewidget">
            <option value="1" <?php echo selected( $types, '1' ) ?>><?php _e('Show on all page', _THEME) ?></option> 
            <option value="2" <?php echo selected( $types, '2' ) ?>><?php _e('Show on specific page', _THEME) ?></option> 
            <option value="3" <?php echo selected( $types, '3' ) ?>><?php _e('Hide from specific page', _THEME) ?></option>
        </select>
    </p>

    <p>
        <div  class="showhidewidgetmenus" style="<?php echo ($types==2 or $types==3)?'':'display:none;'; ?>height: 200px; padding:10px; margin:5px; overflow: auto; background-color: #fff;">
            <?php
                foreach ($menu_items as $key => $menu_itemz ) {
                    echo '<div style="margin:5px 0; font-weight:bold">'.$key.'</div>';
                    $make_menu_tree = buildTree($menu_itemz);
                    showTree($widget,$pages, $make_menu_tree);
                }
            ?>
        </div>
    </p>
    <?php
    }

    /**
    * On widget update
    * 
    * @param mixed $instance
    * @param mixed $new_instance
    */
    function helix_showhide_widget_update_callback( $instance, $new_instance ) 
    {
        $instance['helix_menu_types'] = $new_instance['helix_menu_types'];
        $instance['helix_menu_pages'] = $new_instance['helix_menu_pages'];
        return $instance;
    }
    /**
    * On showing sidebar
    * 
    * @param mixed $instance
    */

    function helix_showhide_widgets( $instance ) {

        global $helix_current_menu_item;

        if( isset($instance['helix_menu_types']) )
        {
            $instance['helix_menu_types'] = (int) $instance['helix_menu_types'];
            if($instance['helix_menu_types']===2)
            {
                if( in_array($helix_current_menu_item, $instance['helix_menu_pages']) ) return $instance;
                else  return false;
            }
            elseif($instance['helix_menu_types']===3)
            {
                if( in_array($helix_current_menu_item, $instance['helix_menu_pages']) ) return false;
            }
        }
        return $instance;
    }


    add_filter( 'wp_nav_menu_objects', 'helix_current_menu_item' );
    function helix_current_menu_item( $sorted_menu_items )
    {
        global $helix_current_menu_item;
        foreach ( $sorted_menu_items as $menu_item ) {
            if ( $menu_item->current ) {
                $helix_current_menu_item = $menu_item->ID;
                break;
            }
        }
        return $sorted_menu_items;
    }

    function print_showhidewidget_inline_script() {
        global $pagenow;
        if($pagenow!='widgets.php') return;
    ?>
    <script type="text/javascript">
        jQuery(function($){
			$('#wpbody-content').on('change','select.showhidewidget', function(event){
					event.stopPropagation();
					if( $(this).val()==='1' ){
						$(this).parent().next().next().slideUp();
					}else{
						$(this).parent().next().next().slideDown();
					}
			});
        });
    </script>
    <?php
    }
add_action( 'admin_footer', 'print_showhidewidget_inline_script' );