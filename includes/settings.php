<?php
if(!class_exists('Royalfolio_settings')) {

    class Royalfolio_settings {
        
        // Declare some class-level variables
        public $royalfolio_folder = null;
        
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
            
            // When class loads, run methods "royalfolio_register_settings" and "royalfolio_add_settings_page"
            add_action('admin_init', array(&$this, 'royalfolio_register_settings'));
            add_action('admin_menu', array(&$this, 'royalfolio_add_settings_page'));
            
        }

        /**
         * REGISTER SETTINGS
         * 
         * Hook into WP's admin_init action hook and register plugin settings
         * 
         * @since 1.0.0
         */
        
        public function royalfolio_register_settings() {
            
            // Register plugin settings
            register_setting('royalfolio-frontend', 'rf_max_width');
            register_setting('royalfolio-frontend', 'rf_max_height');
            register_setting('royalfolio-frontend', 'rf_basic_css');
            register_setting('royalfolio-frontend', 'rf_show_client');
            register_setting('royalfolio-frontend', 'rf_show_year');
            register_setting('royalfolio-frontend', 'rf_show_role');

            // Add "intro" settings section
            add_settings_section(
                'royalfolio-intro', 
                '', 
                array(&$this, 'royalfolio_settings_page_intro'), 
                'royalfolio'
            );
            
            // Add "front-end" settings section
            add_settings_section(
                'royalfolio-frontend', 
                __('Plugin settings', 'royalfolio'), 
                array(&$this, 'royalfolio_settings_page_frontend_intro'), 
                'royalfolio'
            );

            // Add "outro" settings section
            add_settings_section(
                'royalfolio-outro', 
                '', 
                array(&$this, 'royalfolio_settings_page_outro'), 
                'royalfolio'
            );
        	
            // Add settings field: "Max width"
            add_settings_field(
                'royalfolio-max_width', 
                __('Max width', 'royalfolio'), 
                array(&$this, 'royalfolio_field_type_text'), 
                'royalfolio', 
                'royalfolio-frontend',
                array(
                    'field' => 'rf_max_width'
                )
            );

            // Add settings field: "Max height"
            add_settings_field(
                'royalfolio-max_height', 
                __('Max height', 'royalfolio'), 
                array(&$this, 'royalfolio_field_type_text'), 
                'royalfolio', 
                'royalfolio-frontend',
                array(
                    'field' => 'rf_max_height'
                )
            );
            
            // Add settings field: "Basic CSS"
            add_settings_field(
                'royalfolio-basic_css', 
                __('Apply basic CSS to images?', 'royalfolio'), 
                array(&$this, 'royalfolio_field_type_checkbox'), 
                'royalfolio', 
                'royalfolio-frontend',
                array(
                    'field' => 'rf_basic_css'
                )
            );
            
            // Add settings field: "Show client"
            add_settings_field(
                'royalfolio-show_client', 
                __('Show client field?', 'royalfolio'), 
                array(&$this, 'royalfolio_field_type_checkbox'), 
                'royalfolio', 
                'royalfolio-frontend',
                array(
                    'field' => 'rf_show_client'
                )
            );
            
            // Add settings field: "Show year"
            add_settings_field(
                'royalfolio-show_year', 
                __('Show year field?', 'royalfolio'), 
                array(&$this, 'royalfolio_field_type_checkbox'), 
                'royalfolio', 
                'royalfolio-frontend',
                array(
                    'field' => 'rf_show_year'
                )
            );
            
            // Add settings field: "Show role"
            add_settings_field(
                'royalfolio-show_role', 
                __('Show role field?', 'royalfolio'), 
                array(&$this, 'royalfolio_field_type_checkbox'), 
                'royalfolio', 
                'royalfolio-frontend',
                array(
                    'field' => 'rf_show_role'
                )
            );

        }
        
        /**
         * SETTINGS PAGE INTRO
         * 
         * This text is displayed at the very top of the settings page (right
         * below the main title)
         * 
         * @since 1.0.0
         */
        
        public function royalfolio_settings_page_intro() {

            echo '<p>' . __("On this page you'll find a few simple settings that can be used to tweak both the appearance and functionality of this plugin.", "royalfolio") . '<br />' . __("Don't worry! Most of these settings are very simple and straight forward and you should have no problem getting things up and running.", "royalfolio") . '</p>';
            echo '<p>' . sprintf(__("However, if you do need any help setting things up - or have questions - please check the %s", "royalfolio"), '<a href="http://themeroyals.com/knowledgebase-categories/royalfolio/" target="_blank">online documentation</a>') . ' ' . sprintf(__("or the %s", "royalfolio"), '<a href="http://themeroyals.com/forums/forum/wordpress-plugin-support/royalfolio/" target="_blank">support forums</a>') . '</p>';
            echo '<br />';

        }
        
        /**
         * SETTINGS PAGE OUTRO
         * 
         * This text is displayed at the very bottom of the page
         * 
         * @since 1.0.0
         */
        
        public function royalfolio_settings_page_outro() {

            echo '<br /><br /><br /><p>' . __("Please bare in mind that even though this plugin works with any WordPress theme, it was primarily developed for usage with our custom developed themes.", "royalfolio") . '<br />' . __("It's primarily focused at theme developers, and that's why it's capabilities and overall styling is very limited.", "royalfolio") . '</p>';
            
        }
        
        /**
         * SETTINGS PAGE FRONT-END INTRO
         * 
         * This text is displayed at the very top of the front-end section
         * 
         * @since 1.0.0
         */
        
        public function royalfolio_settings_page_frontend_intro() {

            echo '<p>' . __("Detailed explanation of each setting in this section is available in the online documentation", "royalfolio") . '</p>';
            
        }
        
        /**
         * FIELD TYPE: TEXT
         * 
         * Renders a input type="text" field for the current setting
         * 
         * @since 1.0.0
         */
        
        public function royalfolio_field_type_text($args) {
            
            // Get the field name from the $args array
            $field = $args['field'];
            
            // Get the value of this setting
            $value = get_option($field);
            
            // Output a proper text input for this setting
            echo '<input type="text" name="' . $field . '" id="' . $field . '" value="' . $value . '" />';
            
        }
        
        /**
         * FIELD TYPE: CHECKBOX
         * 
         * Renders a checkbox field for the current setting
         * 
         * @since 1.0.0
         */
        
        public function royalfolio_field_type_checkbox($args) {
            
            // Get the field name from the $args array
            $field = $args['field'];
            
            // Get the value of this setting
            $value = get_option($field);
            
            // Is this checked?
            if($value == "1") {
                
                $checked = 'checked';
                
            } else {
                
                $checked = '';
                
            }
            
            // Output a proper text input for this setting
            echo '<input type="checkbox" name="' . $field . '" id="' . $field . '" value="1" ' . $checked . ' />';
            
        }
        
        /**
         * ADD SETTINGS PAGE
         * 
         * Add options page for this plugin
         * 
         * @since 1.0.0
         */
        
        public function royalfolio_add_settings_page() {
            
            // Add a page to manage this plugin's settings
            add_options_page(
                'RoyalFolio Settings',
                'RoyalFolio',
                'manage_options',
                'royalfolio',
                array(&$this, 'royalfolio_settings_page_content')
            );

        }
    
        /**
         * SETTINGS PAGE CONTENT
         * 
         * Is called by method above. Renders the settings page content.
         * 
         * @since 1.0.0
         */
        
        public function royalfolio_settings_page_content() {
            
            // Make sure user can manage options before showing them
            if(!current_user_can('manage_options')) {

                // Display WordPress error message
                wp_die(__('You do not have sufficient permissions to access this page.'));
                
            }

            // Include the settings page template
            include($this->royalfolio_folder . "/templates/settings.php");
            
        }
        
    }
    
}
