<?php
/*
Plugin Name: Comments On
Plugin URI: http://www.seanhayes.biz/wordpress/plugins/comments-on
Description: Shows a handy column in the All Posts/All Pages view showing the comment status of individual posts or pages
Version: 0.1
Author: Sean Hayes
Author URI: www.seanhayes.biz
Author Email: sean@seanhayes.biz
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

  Copyright 2013 sean@seanhayes.biz

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

if ( !class_exists('comments_on') ) {
    class CommentsOn {
	 
	 	public static $instance = NULL;
	 
        /*--------------------------------------------*
         * Constructor
         *--------------------------------------------*/

        /**
         * Initializes the plugin by setting localization, filters, and administration functions.
         */
        function __construct() {

            // Load plugin text domain
            add_action( 'init', array( $this, 'plugin_textdomain' ) );

            // Register admin styles and scripts

            // Comments On doesn't have specific admin css - this hook is included for completeness but commented out
            // add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );

            // Comments On doesn't have specific admin js - this hook is included for completeness but commented out
            // add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

            // Register site styles and scripts

            // Comments On doesn't have specific front end css - this hook is included for completeness but commented out
            // add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );

            // Comments On doesn't have specific front end js - this hook is included for completeness but commented out
            // add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

            // Register these hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
            // Right now nothing special (in fact nothing at all) happens on these hooks
            // These hooks are listed here for completeness
            
            register_activation_hook(   __FILE__, array( $this, 'activate' ) );
            register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
            register_uninstall_hook(    __FILE__, 'uninstall' );

            
            // We want this content to be displayed when the column is up for display : on All Posts admin screen
            add_action('manage_posts_custom_column',    array( $this, 'comments_on_display'), 10, 2);
            
            // We want our column to be displayed here:
            add_filter('manage_posts_columns',          array( $this, 'comments_on_add_column') );
            
            // We want this content to be displayed when the column is up for display : on All Pages admin screen
            add_action('manage_pages_custom_column',    array( $this, 'comments_on_display'), 10, 2);
			
			// We want our column to be displayed here:
            add_filter('manage_pages_columns',          array( $this, 'comments_on_add_column') );

            // For taxonomies:

            $taxonomies=get_taxonomies('','names');

            foreach ($taxonomies as $taxonomy ) {
                add_action( "manage_{$taxonomy}_posts_custom_column" ,  array( $this, 'comments_on_display'), 10, 2 );
                add_filter( "manage_{$taxonomy}_posts_columns" ,        array( $this, 'comments_on_add_column') );
            }


        } // end constructor

        /**
         * @return CommentsOn|null
         *
         * Function to return new instance of the CommentsOn class or the existing instance
         *
         */

        public static function getInstance() {
          if(!isset(self::$instance)) {
            self::$instance = new CommentsOn();
          }
          return self::$instance;
        }

        /**
         *
         * This is the core code for the plugin
         * When the admin screen is rendered:
         * Look for our custom column
         * and when found display the comment status straight from WordPress
         * Status values include:
         * "open", "closed" or "registered_only"
         *
         * @param $column_name
         * @param $post_id
         *
         */
        public function comments_on_display($column_name, $post_id) {
            switch ($column_name) {
            case 'comments-on':
               $current_post = get_post($post_id);
               echo $current_post->comment_status;
                   break;
            default:
                break;
            } // end switch
        }

        /**
         * @param $columns
         * @return array
         *
         * This is the hook function telling WordPress to display our additional column
         * Add our comments column to All Posts view
         */
        public function comments_on_add_column($columns){
            return array_merge( $columns ,
                array('comments-on' => __('Comments On', 'comments-on-locale' ) )
            );
        }

        /**
         * Fired when the plugin is activated.
         * Comments On does not use this function - it is included for completeness
         *
         * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
         */
        public function activate( $network_wide ) {
        } // end activate

        /**
         * Fired when the plugin is deactivated.
         * Comments On does not use this function - it is included for completeness
         *
         * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
         */
        public function deactivate( $network_wide ) {
        } // end deactivate

        /**
         * Fired when the plugin is uninstalled.
         * Comments On does not use this function - it is included for completeness
         *
         * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
         */
        static function uninstall( $network_wide ) {
        } // end uninstall

        /**
         * Loads the plugin text domain for translation
         */
        public function plugin_textdomain() {

            load_plugin_textdomain( 'comments-on-locale', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

        } // end plugin_textdomain

        /**
         * Registers and enqueues admin-specific styles.
         */
        public function register_admin_styles() {

            wp_enqueue_style( 'comments-on-admin-styles', plugins_url( 'comments-on/css/admin.css' ) );

        } // end register_admin_styles

        /**
         * Registers and enqueues admin-specific JavaScript.
         *
         */
        public function register_admin_scripts() {

            // Comments On doesn't have specific admin js - this hook is included for completeness
            wp_enqueue_script( 'comments-on-admin-script', plugins_url( 'comments-on/js/admin.js' ) );

        } // end register_admin_scripts

        /**
         * Registers and enqueues plugin-specific styles.
         */
        public function register_plugin_styles() {

            wp_enqueue_style( 'comments-on-plugin-styles', plugins_url( 'comments-on/css/display.css' ) );

        } // end register_plugin_styles

        /**
         * Registers and enqueues plugin-specific scripts.
         */
        public function register_plugin_scripts() {

            wp_enqueue_script( 'comments-on-plugin-script', plugins_url( 'comments-on/js/display.js' ) );

        } // end register_plugin_scripts

    } // end class
}
$comments_on = CommentsOn::getInstance();
