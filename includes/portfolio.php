<?php
if(!class_exists('Royalfolio_portfolio')) {

    class Royalfolio_portfolio {
        
        // Define class-level variables 
        public $royalfolio_folder = null;
        
        private $_meta	= array( // (dessa är bara demo värden)
            'meta_a',
            'meta_b',
            'meta_c',
        );
		
    	/**
         * CONSTRUCTOR
         * 
         * Runs every single time this class is initiated
         * 
         * @since 1.0.0
         */
        
    	public function __construct() {
            
            // Grab plugin folder (we're in "/includes" now)
            $this->royalfolio_folder = realpath(dirname(__FILE__) . '/..');
            
            // When this class loads, run "royalfolio_register" and "royalfolio_admin_init" methods
            add_action('init', array(&$this, 'royalfolio_register'));
            add_action('admin_init', array(&$this, 'royalfolio_admin_init'));
                
    	}

        /**
         * REGISTER
         * 
         * Hook into WP's init action hook and trigger registration of both
         * custom post type and taxonomy. Also make sure that meta box values
         * are saved when portfolio post is saved / updated.
         * 
         * @since 1.0.0
         */
        
    	public function royalfolio_register() {
            
            // Register the custom post type "portfolio"
            $this->royalfolio_register_post_type();
            
            // Tweak custom post type columns a bit
            add_filter('manage_edit-portfolio_columns', array(&$this, 'add_new_portfolio_columns'));
            add_action('manage_portfolio_posts_custom_column', array(&$this, 'manage_portfolio_columns'), 10, 2);
            
            // Register taxonomy "portfolio-categories"
            $this->royalfolio_register_taxonomy();
            
            // On "save_post", run method "save_post"
            add_action('save_post', array(&$this, 'royalfolio_save_portfolio'));
                
    	}

        /**
         * REGISTER "PORTFOLIO"
         * 
         * Register the custom post type "portfolio"
         * 
         * @since 1.0.0
         */
        
    	public function royalfolio_register_post_type() {

            // Setup labels
            $labels = array(
                'name' => _x('Portfolio', 'post type general name', 'themeroyals-portfolio'),
                'singular_name' => _x('project', 'post type singular name', 'themeroyals-portfolio'),
                'add_new' => _x('Add new project', 'project', 'themeroyals-portfolio'),
                'add_new_item' => __('Add new project', 'themeroyals-portfolio'),
                'edit_item' => __('Edit project', 'themeroyals-portfolio'),
                'new_item' => __('New project', 'themeroyals-portfolio'),
                'view_item' => __('Show project', 'themeroyals-portfolio'),
                'search_items' => __('Search project', 'themeroyals-portfolio'),
                'not_found' => __('No hits', 'themeroyals-portfolio'),
                'not_found_in_trash' => __('No hits in trash', 'themeroyals-portfolio'),
                'parent_item_colon' => ''
            );

            // Setup arguments
            $args = array(
                'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'query_var' => true,
                'menu_icon' => 'dashicons-portfolio',
                'rewrite' => array( 'slug' => 'portfolio', 'with_front' => false ),
                'capability_type' => 'post',
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title','editor','thumbnail','comments')
            );

            // Register custom post type "portfolio" with supplied arguments
            register_post_type('portfolio' , $args);
                
    	}
        
        /**
         * TWEAK CUSTOM POST TYPE COLUMNS
         * 
         * Tweaks the column layout for posts when user clicks "Portfolio"
         * 
         * @since 1.0.0
         */
        
        public function add_new_portfolio_columns($gallery_columns) {
            
            // Define columns
            $new_columns['cb'] = '<input type="checkbox" />';
            $new_columns['id'] = __('ID', 'royalfolio');
            $new_columns['title'] = _x('Project', 'column name');
            $new_columns['client'] = __('Client', 'royalfolio');
            $new_columns['year'] = __('Year', 'royalfolio');
            $new_columns['author'] = __('Author', 'royalfolio');

            // Return to WordPress
            return $new_columns;
    
        }
        
        /**
         * ADD CUSTOM COLUMN VALUES
         * 
         * Adds values to the custom columns added above
         * 
         * @since 1.0.0
         */
        
        public function manage_portfolio_columns($column_name, $id) {
            
            // Grab global post variable
            global $post;
            
            // Fetch meta
            $meta_values = get_post_custom($post->ID);
    
            switch($column_name) {
            
                case 'id':
                    echo $id;
                break;
                case 'client':
                    echo $meta_values["royalfolio_client"][0];
                break;
 
                case 'year':
                    echo $meta_values["royalfolio_year"][0];
                break;
    
                default:
                break;

            }
            
        }
        
        /**
         * REGISTER "PORTFOLIO-CATEGORIES"
         * 
         * Register the taxonomy "portfolio-categories"
         * 
         * @since 1.0.0
         */
        
    	public function royalfolio_register_taxonomy() {
            
            // Add portfolio categories
            register_taxonomy("portfolio-categories", array("portfolio"), array("hierarchical" => true, "label" => "Categories", "singular_label" => "Category", "rewrite" => true));
    
        }

        /**
         * SAVE PORTFOLIO
         * 
         * Saves meta box values when a portfolio item is being saved / updated
         * 
         * @since 1.0.0
         */
        
    	public function royalfolio_save_portfolio($post_id) {
            
            // If this is an autosave - do nothing, just return
            if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {

                return;
                
            }
            
            // This user lacks proper permission for this
            if(!current_user_can('edit_post', $post_id)) {
                
                return;
                
            }
            
            // Make sure its the proper post type
            if(!isset($_POST['post_type']) OR $_POST['post_type'] != 'portfolio') {

                return;
                
            }
            
            // Make sure nonce is set
            if(!isset($_POST['royalfolio_metabox_nonce'])) {

                return;

            }

            // Verify that the nonce is valid
            if(!wp_verify_nonce($_POST['royalfolio_metabox_nonce'], 'royalfolio_metabox')) {

                return;

            }
            
            // Update client
            if(isset($_POST['royalfolio_client'])) {

                // Sanitize user input
                $royalfolio_client = sanitize_text_field($_POST['royalfolio_client']);

                // Update the meta fields in the database
                update_post_meta($post_id, 'royalfolio_client', $royalfolio_client);

            }
            
            // Update year
            if(isset($_POST['royalfolio_year'])) {

                // Sanitize user input
                $royalfolio_year = sanitize_text_field($_POST['royalfolio_year']);

                // Remove all non-numeric characters
                $royalfolio_year = preg_replace('/\D/', '', $royalfolio_year);
                
                // Update the meta fields in the database
                update_post_meta($post_id, 'royalfolio_year', $royalfolio_year);

            }
            
            // Update role
            if(isset($_POST['royalfolio_role'])) {

                // Update the meta fields in the database
                update_post_meta($post_id, 'royalfolio_role', implode("\n", array_map('sanitize_text_field', explode("\n", $_POST['royalfolio_role']))));

            }
            
            // Update images
            if(isset($_POST['royalfolio_image_urls'])) {

                // Grab selected images as string and sanitize it
                $royalfolio_image_urls = sanitize_text_field($_POST['royalfolio_image_urls']);

                // Explode into array
                $royalfolio_image_urls = explode(",", $royalfolio_image_urls);

                // Update the meta fields in the database
                update_post_meta($post_id, 'royalfolio_gallery', $royalfolio_image_urls);

            }
            
    	}

        /**
         * ADMIN_INIT
         * 
         * Hook into WP's admin_init action hook and add meta boxes
         * 
         * @since 1.0.0
         */
        
    	public function royalfolio_admin_init() {
            
            // On "admin_init", add meta boxes by running method "royalfolio_add_metaboxes"
            add_action('add_meta_boxes', array(&$this, 'royalfolio_add_metaboxes'));
            
    	}
			
    	/**
         * ADD META BOXES
         * 
         * Add meta boxes when triggered by "add_meta_boxes" in method above
         * 
         * @since 1.0.0
         */
        
    	public function royalfolio_add_metaboxes() {
            
            // Meta box: Project details
            add_meta_box('royalfolio_details_section', __('Project details', 'themeroyals-portfolio'), array(&$this, 'royalfolio_metabox_details_content'), 'portfolio', 'side', 'default');
            
            // Meta box: Image gallery
            add_meta_box('royalfolio_gallery_section', __('Image gallery', 'themeroyals-portfolio'), array(&$this, 'royalfolio_metabox_gallery_content'), 'portfolio', 'normal', 'high');

    	}

        /**
         * META BOX CONTENT: DETAILS
         * 
         * Renders meta box content for project details
         * 
         * @since 1.0.0
         */
        
        public function royalfolio_metabox_details_content($post) {
            
            // Include the meta box content
            include($this->royalfolio_folder . "/templates/metabox-details.php");

        }
        
        /**
         * META BOX CONTENT: GALLERY
         * 
         * Renders meta box content for image gallery
         * 
         * @since 1.0.0
         */
        
        public function royalfolio_metabox_gallery_content($post) {
            
            // Include the meta box content
            include($this->royalfolio_folder . "/templates/metabox-gallery.php");

        }

    }
        
}