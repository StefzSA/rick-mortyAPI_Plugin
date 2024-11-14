<?php
/* *
 * Plugin Name: Rick & Morty API
 * Description: Let's you insert a Search bar for characters using a simple shortcode!
 * Version:     1.0.0
 * Author:      Stefano Strippoli
 * License:     GPLv2 or later
 * License URI: http:/* www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: RM_API
 */

/* Define plugin directory*/
define('RM_DIR', plugin_dir_path(__FILE__));

require_once(RM_DIR . 'inc/functions.php');


/* Function to create admin menu pages */
function rm_admin_menu(){
  add_menu_page('Rick & Morty API Settings ','Rick & Morty API Settings','manage_options','rm_settings','rm_settings_page', plugin_dir_url(__FILE__).'admin/img/icon.svg', 80);
}

function rm_register_settings() {
  register_setting( 'rm_settings', 'rm_recaptcha_site_key', '' );
  register_setting( 'rm_settings', 'rm_recaptcha_secret_key', '' );
}

/* Function to enqueue scripts and styles using add action func with a hook*/
function rm_enqueue_scripts(){
  wp_enqueue_style('rm-search-style', plugins_url('/inc/css/styles.css', __FILE__), array(), '1.0.0');
  wp_enqueue_script('rm-search-script', plugins_url('/inc/js/scripts.js', __FILE__), array('jquery'), '1.0.0', true);

  wp_localize_script( 'rm-search-script', 'ajaxSearch', array(
    'url'    => admin_url( 'admin-ajax.php' ),
    'nonce'  => wp_create_nonce( 'rm_search_nonce' ),
    'action' => 'rm_submit_search'
  ));

  wp_localize_script( 'rm-search-script', 'ajaxPage', array(
    'url'    => admin_url( 'admin-ajax.php' ),
    'nonce'  => wp_create_nonce( 'rm_search_page_nonce' ),
    'action' => 'rm_submit_search_page'
  ));
}

add_action( 'admin_menu', 'rm_admin_menu' ); 
add_action( 'admin_init', 'rm_register_settings' );
add_action( 'admin_enqueue_scripts', 'rm_enqueue_scripts' ); 
add_action( 'wp_enqueue_scripts', 'rm_enqueue_scripts' );

add_action('wp_ajax_nopriv_rm_submit_search', 'rm_submit_search');
add_action('wp_ajax_rm_submit_search', 'rm_submit_search');
add_action('wp_ajax_nopriv_rm_submit_search_page', 'rm_submit_search_page');
add_action('wp_ajax_rm_submit_search_page', 'rm_submit_search_page');

require_once(RM_DIR . 'inc/form.php');
?>
