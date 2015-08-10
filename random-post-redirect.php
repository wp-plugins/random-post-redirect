<?php
/**
* Plugin Name: Random Post Redirect
* Plugin URI: http://www.wpcube.co.uk
* Version: 1.0.1
* Author: <a href="http://www.n7studios.co.uk">n7 Studios</a>, <a href="http://www.wpcube.co.uk">WP Cube</a>
* Description: Redirects to a random Post on your WordPress site
* License: GPL2
*/

/*  Copyright 2015 n7 Studios, WP Cube (email : tim@n7studios.co.uk)

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

/**
* Random Post Class
* 
* @package WordPress
* @subpackage Random Post
* @author Tim Carr
* @version 1.0.1
* @copyright WP Cube
*/
class RandomPost {

    /**
    * Constructor.
    */
    function __construct() {

        // Plugin Details
        $this->plugin = new stdClass;
        $this->plugin->name         = 'random-post-redirect'; // Plugin Folder
        $this->plugin->displayName  = 'Random Post Redirect'; // Plugin Name
        $this->plugin->version      = '1.0.1';
        $this->plugin->folder       = plugin_dir_path( __FILE__ );
        $this->plugin->url          = plugin_dir_url( __FILE__ );
        $this->plugin->settingsUrl  = get_bloginfo('url') . '/wp-admin/admin.php?page=' . $this->plugin->name; 

        // Dashboard Submodule
        if ( ! class_exists( 'WPCubeDashboardWidget' ) ) {
            require_once( $this->plugin->folder . '/_modules/dashboard/dashboard.php' );
        }
        $dashboard = new WPCubeDashboardWidget( $this->plugin );  
        
        // Actions and Filters
        add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts_css') );
        add_action( 'admin_menu', array( &$this, 'admin_menu') );
        add_action( 'plugins_loaded', array( &$this, 'load_language_files' ) );

        // Frontend Actions
        if ( ! is_admin() ) {
            add_action( 'init', array( &$this, 'frontend_header') );
        }

    }
    
    /**
    * Register and enqueue any JS and CSS for the WordPress Administration
    */
    function admin_scripts_css() {

        // CSS
        wp_enqueue_style( $this->plugin->name . '-admin-css', $this->plugin->url . 'css/admin.css' );

    }
    
    /**
    * Adds a single option panel to the WordPress Administration
    */
    function admin_menu() {

        add_menu_page( $this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array( &$this, 'admin_screen' ), 'dashicons-update' );
   
    }
    
    /**
    * Outputs the plugin Admin Panel in WordPress Administration
    */
    function admin_screen() {

        // Save Settings
        if ( isset( $_POST['submit'] ) ) {
            // Check nonce
            if ( ! isset( $_POST[ $this->plugin->name . '_nonce' ] ) ) {
                // Missing nonce    
                $this->errorMessage = __( 'nonce field is missing. Settings NOT saved.', $this->plugin->name );
            } elseif ( ! wp_verify_nonce( $_POST[ $this->plugin->name . '_nonce' ], $this->plugin->name ) ) {
                // Invalid nonce
                $this->errorMessage = __( 'Invalid nonce specified. Settings NOT saved.', $this->plugin->name );
            } else {            
                if ( isset( $_POST[ $this->plugin->name ] ) ) {
                    update_option( $this->plugin->name, $_POST[ $this->plugin->name ] );
                    $this->message = __( 'Settings Updated.', $this->plugin->name );
                }
            }
        }
        
        // Get latest settings
        $this->settings = get_option( $this->plugin->name );

        // List the categories for the user to select
        $args = array(
            'type' => 'post',
            'hierarchical' => 0,
            'taxonomy' => 'category',
        );
        $this->plugin->categories = get_categories( $args );
        
        // Get Pages
        $pages = new WP_Query(array(
            'post_type' => array('page'),
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));
        
        // Load Settings Form
        include_once( $this->plugin->folder . 'views/settings.php' );   

    }

    /**
    * Loads plugin textdomain
    */
    function load_language_files() {

        load_plugin_textdomain( $this->plugin->name, false, $this->plugin->name . '/languages/' );

    }
    
    /**
    * Checks the loaded URL, and if it is the random page, redirect to a random post
    */
    function frontend_header() {

        // Get latest settings
        $this->settings = get_option( $this->plugin->name );
        if ( ! isset( $this->settings ) || ! isset( $this->settings['page'] ) || empty( $this->settings['page'] ) ) {
            return;
        }
        
        // Check if loaded URL slug matches setting
        $homeURL = home_url();
        $requestedURL = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $slug = str_replace( '/', '', str_replace( $homeURL, '', $requestedURL ) );
        if ( $slug != $this->settings['page'] ) {
            return;
        }

        // Build WP_Query Args
        wp_cache_flush();
        $args = array(
            'post_type'     => array( 'post' ),
            'posts_per_page'=> 1,
            'orderby'       => 'rand',
        );
        if ( isset( $this->settings['exclude'] ) ) {
            $args['category__not_in'] = $this->settings['exclude'];
        }

        // Get Post        
        $randomPost = new WP_Query( $args );
        if ( count( $randomPost->posts ) == 0 ) {
            return;
        }

        // Redirect
        header( 'HTTP/1.1 307 Temporary Redirect' );
        header( 'Cache-Control: no-cache, no-store, must-revalidate, max-age=0' ); // HTTP 1.1.
        header( 'Pragma: no-cache' ); // HTTP 1.0.
        header( 'Expires: 0' ); // Proxies.
        header( 'Location: ' . get_permalink( $randomPost->posts[0]->ID ) ); // Redirect
        die();
        
    }

}

$randomPost = new RandomPost();