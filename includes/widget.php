<?php
if(!class_exists('Royalfolio_widget')) {

    class Royalfolio_widget extends WP_Widget {

        /**
         * CONSTRUCTOR
         * 
         * Runs every single time this class is initiated
         * 
         * @since 1.0.0
         */
        
        function __construct() {

            // Pull parents constructor along with some values for this widget
            parent::__construct('royalfolio_widget', __('Latest projects', 'royalfolio'), array('description' => __('Displays a list of your latest portfolio projects.', 'royalfolio')));
            
        }

        /**
         * WIDGET FRONT-END
         * 
         * Output what the user should see on front-end
         * 
         * @since 1.0.0
         */
        
        public function widget($args, $instance) {

            // Grab the title provided by admin
            $title = apply_filters('widget_title', $instance['title']);
            
            // Grab the num items provided by admin
            $num_projects = apply_filters('widget_title', $instance['num_projects']);

            // Make sure to apply "before_widget" specified by current theme
            echo $args['before_widget'];

            // If title is provided
            if(!empty($title)) {
                
                // Make sure to apply "before_title / after_title" title specified by current theme
                echo $args['before_title'] . $title . $args['after_title'];
                
            }
            
            // Query arguments
            $rf_args = array(
                'post_type' => 'portfolio',
                'posts_per_page' => $num_projects
            );
            
            // Query database
            query_posts($rf_args);
            
            echo '<ul>';
            
            // Loop and output list
            while(have_posts()) {
                
                the_post();
                
                echo '<li><a href="';
                the_permalink();
                echo '">';
                the_title();
                echo '</a></li>';
                
            }
            
            wp_reset_query();
            
            echo '</ul>';
            
            // Make sure to apply "after_widget" specified by current theme
            echo $args['after_widget'];

        }
		
        /**
         * WIDGET BACK-END
         * 
         * Output what the admin should see on back-end when adding the widget
         * 
         * @since 1.0.0
         */

        public function form( $instance ) {

            // If they provided a title
            if(isset($instance['title'])) {

                $title = $instance['title'];

            // Define standard title
            } else {

                $title = __('Latest projects', 'royalfolio');

            }
            
            // If they provided a number
            if(isset($instance['num_projects'])) {

                $num_projects = preg_replace('/\D/', '', $instance['num_projects']); // Only numbers allowed

            // Define standard title
            } else {

                $num_projects = 5;

            }
?>

<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'royalfolio'); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
</p>

<p>
    <label for="<?php echo $this->get_field_id('num_projects'); ?>"><?php _e('Number of projects to show:', 'royalfolio'); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id('num_projects'); ?>" name="<?php echo $this->get_field_name('num_projects'); ?>" type="text" value="<?php echo esc_attr($num_projects); ?>" />
</p>

<?php 
}
	
        /**
         * UPDATE WIDGET
         * 
         * Updating widget replacing old instances with new
         * 
         * @since 1.0.0
         */

        public function update($new_instance, $old_instance) {

            // Create empty array
            $instance = array();

            // If title has been set - update it (after stripping any tags)
            $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
            
            // If num items has been set - update it (after preg_replace)
            $instance['num_projects'] = (!empty($new_instance['num_projects'])) ? preg_replace('/\D/', '', $new_instance['num_projects']) : '';

            // Return to WordPress
            return $instance;

        }


    }

    /**
     * REGISTER WIDGET
     * 
     * Register and load widget using the "widgets_init" hook
     * 
     * @since 1.0.0
     */
    
    function royalfolio_load_widget() {

        register_widget('royalfolio_widget');
        
    }
    
    add_action('widgets_init', 'royalfolio_load_widget');

}