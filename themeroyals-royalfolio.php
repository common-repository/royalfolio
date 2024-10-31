<?php
/*
Plugin Name: RoyalFolio
Plugin URI: https://www.themeroyals.com/knowledgebase-categories/royalfolio-plugin/
Description: A simple and easy to use plugin for managing your portfolio
Version: 1.0.0
Author: ThemeRoyals
Author URI: https://www.themeroyals.com
License: GPL2
*/

/*
Copyright 2014 Martin Sidén (martin@themeroyals.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(!class_exists('Royalfolio')) {

    class Royalfolio {
        
        // Declare some class-level variables
        public $royalfolio_folder = null;
        public $royalfolio_file = null;
        public $royalfolio_url = null;
        
        /**
         * CONSTRUCTOR
         * 
         * Runs every single time this class is initiated
         * 
         * @since 1.0.0
         */
        
        public function __construct() {
            
            // Grab the plugin folder path
            $this->royalfolio_folder = dirname(__FILE__);
            
            // Grab the plugin file path
            $this->royalfolio_file = plugin_basename(__FILE__);
            
            // Grab the plugin URL
            $this->royalfolio_url = trailingslashit(plugin_dir_url( __FILE__ ));
            
            // Load language
            load_plugin_textdomain('royalfolio', false, $this->royalfolio_folder . '/languages' );
            
            // Enqueue scripts and stylesheets for admin interface
            add_action('admin_enqueue_scripts', array($this, 'royalfolio_script_assets'));
            add_action('admin_enqueue_scripts', array($this, 'royalfolio_style_assets'));
            
            // If they want basic CSS applied - load front-end stylesheet as well
            $rf_basic_css = get_option('rf_basic_css');
            
            if(!empty($rf_basic_css)) {
                
                if($rf_basic_css == "1") {

                    add_action('wp_enqueue_scripts', array($this, 'royalfolio_frontend_script_assets'));
                    add_action('wp_enqueue_scripts', array($this, 'royalfolio_frontend_style_assets'));

                }
            
            }
            
            // Include settings file and fire up settings class
            require_once($this->royalfolio_folder . "/includes/settings.php");
            $royalfolio_settings = new Royalfolio_settings();

            // Add custom post type "portfolio", taxonomy "portfolio-categories" and meta boxes
            require_once($this->royalfolio_folder . "/includes/portfolio.php");
            $royalfolio_portfolio = new Royalfolio_portfolio();
            
            // Include widgets file and fire up widget class
            require_once($this->royalfolio_folder . "/includes/widget.php");
            $royalfolio_widget = new Royalfolio_widget();

            // Add settings link (this shows up right below plugin title in list of plugins view)
            add_filter("plugin_action_links_$this->royalfolio_file", array($this, 'royalfolio_settings_link'));
            
            // Add shortcodes
            add_shortcode('royalfolio', array($this, 'royalfolio_shortcode_portfolio'));
            add_shortcode('royalfolio-gallery', array($this, 'royalfolio_shortcode_gallery'));
            
        }

        /**
         * ACTIVATE
         * 
         * Runs when plugin is activated
         * 
         * @since 1.0.0
         */
        
        public static function royalfolio_activate() {

        }

        /**
         * DEACTIVATE
         * 
         * Runs when plugin is deactivated
         * 
         * @since 1.0.0
         */
        
        public static function royalfolio_deactivate() {

        }
        
        /**
         * SCRIPT ASSETS
         * 
         * Enqueue scripts
         * 
         * @since 1.0.0
         */
        
        function royalfolio_script_assets() {

            wp_enqueue_script('royalfolio', $this->royalfolio_url . 'assets/js/royalfolio.js', array('jquery'), false, '1.0', true);
            
        }
        
        /**
         * STYLESHEET ASSETS
         * 
         * Enqueue stylesheet
         * 
         * @since 1.0.0
         */
        
        function royalfolio_style_assets() {

            wp_enqueue_style('royalfolio', $this->royalfolio_url . 'assets/css/royalfolio.css', false);
            
        }
        
        /**
         * SCRIPT ASSETS: FRONT-END
         * 
         * Enqueue scripts for front-end (if they want it)
         * 
         * @since 1.0.0
         */
        
        function royalfolio_frontend_script_assets() {

            wp_enqueue_script('royalfolio-rebox', $this->royalfolio_url . 'assets/js/jquery-rebox.js', array('jquery'), false, '1.0', true);
            wp_enqueue_script('royalfolio-frontend-scripts', $this->royalfolio_url . 'assets/js/royalfolio-frontend.js', array('jquery'), false, '1.0', true);
            
        }
        
        /**
         * STYLESHEET ASSETS: FRONT-END
         * 
         * Enqueue stylesheet for front-end (if they want it)
         * 
         * @since 1.0.0
         */
        
        function royalfolio_frontend_style_assets() {

            wp_enqueue_style('royalfolio-frontend', $this->royalfolio_url . 'assets/css/royalfolio-frontend.css', false);
            
        }

        /**
         * ADD SETTINGS LINK
         * 
         * Builds hyperlink towards settings page, adds it to $links array
         * and returns it to WordPress.
         * 
         * @since 1.0.0
         * @param $links array
         * @return $links array
         */
        
        function royalfolio_settings_link($links) {
            
            // Build hyperlink
            $settings_link = '<a href="options-general.php?page=royalfolio">Settings</a>';
            
            // Insert the new hyperlink to links array
            array_unshift($links, $settings_link);
            
            // Return links array
            return $links;
            
        }
        
        /**
         * ADD SHORTCODE: PORTFOLIO
         * 
         * Adds shortcode [royalfolio]
         * 
         * @since 1.0.0
         */
        
        function royalfolio_shortcode_portfolio($atts) {

            // Grab global post data
            global $post;
            
            // They want to display a specific category
            if(is_array($atts) AND array_key_exists("cat", $atts)) {
                
                // Grab category slug
                $rf_cat_slug = $atts['cat'];
                
                // Query arguments
                $rf_args = array(
                    'post_type' => 'portfolio',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'portfolio-categories',
                            'field' => 'slug',
                            'terms' => $rf_cat_slug
                        )
                    )
                );
                
            // They want to display all
            } else {
                
                // Query arguments
                $rf_args = array(
                    'post_type' => 'portfolio',
                );
                
            }

            // Query database
            query_posts($rf_args);
            
            echo '<ul class="royalfolio-list">';
            
            // Loop and output list
            while(have_posts()) {
                
                the_post();
                
                echo '<li>';
                if(has_post_thumbnail()) {
                    
                    echo '<a href="';
                    the_permalink();
                    echo '">';
                    the_post_thumbnail('full', array('class' => 'royalfolio-image'));
                    echo '</a>';
                    
                }
                echo '<h2><a href="';
                the_permalink();
                echo '">';
                the_title();
                echo '</a></h2><ul class="royalfolio-meta">';
                if(get_post_meta($post->ID, 'royalfolio_client', true) != "") {
                    echo '<li>' . __("Client", "royalfolio") . ': ' . get_post_meta($post->ID, 'royalfolio_client', true) . '</li>';
                }
                if(get_post_meta($post->ID, 'royalfolio_year', true) != "") {
                    echo '<li>' . __("Year", "royalfolio") . ': ' . get_post_meta($post->ID, 'royalfolio_year', true) . '</li>';
                }
                echo '</ul>';
                the_excerpt();
                echo '</li>';
                
            }
            
            wp_reset_query();
            
            echo '</ul>';
            
        }
        
        /**
         * ADD SHORTCODE: GALLERY
         * 
         * Adds shortcode [royalfolio-gallery]
         * 
         * @since 1.0.0
         */
        
        function royalfolio_shortcode_gallery($atts) {

            // Use global post data
            global $post;
            
            // They want to display a specific post ID
            if(is_array($atts) AND array_key_exists("post", $atts)) {
                
                // Quick sanitize (only allow numbers)
                $rf_show_post = preg_replace('/\D/', '', $atts['post']);
                
                // Grab gallery for this post
                $rf_post_gallery = get_post_meta($rf_show_post, 'royalfolio_gallery', true);
                
            // They want THIS post ID
            } else {
            
                // Grab gallery for this post
                $rf_post_gallery = get_post_meta($post->ID, 'royalfolio_gallery', true);
            
            }
            
            // If there's image attached to this item
            if(is_array($rf_post_gallery) AND !empty($rf_post_gallery)) {
                
                // Variable to hold output
                $rf_output = '<div class="royalfolio-wrapper">';
                
                // Loop them
                foreach($rf_post_gallery as $key => $value) {
                    
                    // Are we suposed to link to images?
                    if(is_array($atts) AND array_key_exists("link", $atts) AND $atts['link'] == "yes") {
                        $rf_output .= '<a href="' . $value . '" target="_blank" class="royalfolio-link">';
                    }
                    
                    $rf_output .= '<img src="' . $value . '" alt="" class="royalfolio-image" style="max-width: ' . get_option('rf_max_width') . 'px; max-height: ' . get_option('rf_max_height') . 'px;" />';
                    
                    // Are we suposed to link to images?
                    if(is_array($atts) AND array_key_exists("link", $atts) AND $atts['link'] == "yes") {
                        $rf_output .= '</a>';
                    }
                    
                }
                
                $rf_output .= '</div>';

            // There are no images for this post yet
            } else {
                
                $rf_output = __("This portfolio item has no images yet.", "royalfolio");
                
            }
            
            // Return output
            return $rf_output;
            
        }

    }
        
}

if(class_exists('Royalfolio')) {
    
    // Activation and deactivation hooks
    register_activation_hook(__FILE__, array('Royalfolio', 'royalfolio_activate'));
    register_deactivation_hook(__FILE__, array('Royalfolio', 'royalfolio_deactivate'));
    
    // Ready? Let's go!
    $royalfolio = new Royalfolio();

}