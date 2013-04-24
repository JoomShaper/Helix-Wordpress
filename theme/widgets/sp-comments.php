<?php
    /**
    * SP Comment Widget	
    * @package Helix Framework
    * @author JoomShaper http://www.joomshaper.com
    * @copyright Copyright (c) 2010 - 2013 JoomShaper
    * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
    */
    class SP_Comments_Widget extends WP_Widget {

        /**
        * Register widget with WordPress.
        */
        public function __construct() {
            parent::__construct(
                'sp_comments_widget', // Base ID
                'SP Comments', // Name
                array( 'description' => __( 'Comments display widget', _THEME ), ) // Args
            );
        }

        /**
        *
        * Limit Comment Text 
        *
        *
        **/
        function cText($text, $limit = 10, $sep='...') {

            $text = strip_tags($text);
            $text = explode(' ',$text);
            $sep = (count($text)>$limit) ? '...' : '';
            $text=implode(' ', array_slice($text,0,$limit)) . $sep;

            return $text;
        }

        /**
        * Front-end display of widget.
        *
        * @see WP_Widget::widget()
        *
        * @param array $args     Widget arguments.
        * @param array $instance Saved values from database.
        */
        public function widget( $args, $instance ) {
            extract( $args );

            $title = apply_filters('widget_title', empty($instance['title']) ? __( 'Latest Comments', _THEME ) : $instance['title'], $instance, $this->id_base);

            $avatar_size = empty($instance['avatar_size']) ? 48 : $instance['avatar_size'];
            $word_limit = empty($instance['word_limit']) ? 20 : $instance['word_limit'];
            $count = empty($instance['count']) ? 5 : $instance['count'];

            echo $before_widget;

            echo $before_title . $title . $after_title;

            $comments_args = array(
                'status' => 'approve',
                'order' => 'DESC',
                'number' => $count
            );
            $comments = get_comments($comments_args);

            foreach ($comments as $key=>$comment) {
                $link = get_permalink( $comment->comment_post_ID ).'#comment-'.$comment->comment_ID;

            ?>
            <div class="media sp-comment <?php echo ($key%2)? 'even': 'odd'; ?>">
                <div class="pull-left">
                    <?php echo get_avatar($comment->comment_author_email, $avatar_size); ?>
                </div>
                <div class="media-body">
                    <a href="<?php echo $link; ?>"><?php echo $this->cText($comment->comment_content, $word_limit); ?></a>
                    <div class="clearfix"></div>
                    <small class="muted">
                        <?php 
                            $time = strtotime($comment->comment_date);
                            echo date('d F Y', $time); 
                        ?>
                    </small>
                </div>
            </div>			
            <?php
            }	

            echo $after_widget;
        }

        /**
        * Sanitize widget form values as they are saved.
        *
        * @see WP_Widget::update()
        *
        * @param array $new_instance Values just sent to be saved.
        * @param array $old_instance Previously saved values from database.
        *
        * @return array Updated safe values to be saved.
        */
        public function update( $new_instance, $old_instance ) {
            $instance 					= array();
            $instance['title'] 			= strip_tags( $new_instance['title'] );
            $instance['avatar_size']	= strip_tags( $new_instance['avatar_size'] );
            $instance['count'] 			= strip_tags( $new_instance['count'] );
            $instance['word_limit'] 	= strip_tags( $new_instance['word_limit'] );

            return $instance;
        }

        /**
        * Back-end widget form.
        *
        * @see WP_Widget::form()
        *
        * @param array $instance Previously saved values from database.
        */
        public function form( $instance ) {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
            $avatar_size = isset($instance['avatar_size']) ? esc_attr($instance['avatar_size']) : '48';
            $count = isset($instance['count']) ? esc_attr($instance['count']) : '5';
            $word_limit = isset($instance['word_limit']) ? esc_attr($instance['word_limit']) : '20';

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', _THEME ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Count:', _THEME ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'word_limit' ); ?>"><?php _e( 'Word Limit:', _THEME ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'word_limit' ); ?>" name="<?php echo $this->get_field_name( 'word_limit' ); ?>" type="text" value="<?php echo esc_attr( $word_limit ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'avatar_size' ); ?>"><?php _e( 'Avatar Size:', _THEME ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'avatar_size' ); ?>" name="<?php echo $this->get_field_name( 'avatar_size' ); ?>" type="text" value="<?php echo esc_attr( $avatar_size ); ?>" />
        </p>
        <?php
        }

    } // class Sp_Comments_Widget

    // register SP Comments widget
add_action( 'widgets_init', create_function( '', 'register_widget( "sp_comments_widget" );' ) );