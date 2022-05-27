<?php

/**
 * Creating an submenu settings to add api key and choose airport page id.
 */
function afs_register_setting_page() {
    add_submenu_page( 'edit.php?post_type=flight', __( 'Settings', 'airport-flight-status' ),  __( 'Settings', 'airport-flight-status' ), 'manage_options', 'afs-settings', 'afs_register_setting_page_callback' ); 
}
  
function afs_register_setting_page_callback() {
    $afs_API_KEY = get_option('afs_api_key');
    $afs_airport_page = get_option('afs_airport_page_id');
    $afs_default_airport_iata = get_option('afs_default_airport_iata');

    if(isset($_POST['submitted'])) {
        $afs_API_KEY = $_POST['afs_API_KEY'];
        $afs_airport_page = $_POST['afs_airport_page'];
        $afs_default_airport_iata = $_POST['afs_default_airport_iata'];
        
        update_option('afs_api_key', $afs_API_KEY);
        update_option('afs_airport_page_id', $afs_airport_page);
        update_option('afs_default_airport_iata', $afs_default_airport_iata);
    }
  
    ob_start();
    include_once(plugin_dir_path( __FILE__ ).'templates/admin-panel.php');
    ob_get_flush();
  }
  
  add_action('admin_menu', 'afs_register_setting_page');

  /**
   * Enqueue scripts and styles
   */
  function afs_wpdocs_scripts_admin(){
        if(isset($_GET['post_type']) && 'flight' == $_GET['post_type'] ){
          wp_enqueue_style( 'bootstrap-css', plugin_dir_url( __DIR__  ) .'public/css/bootstrap.min.css');
          wp_enqueue_style( 'afs-admin-css', plugin_dir_url( __DIR__  ) .'admin/css/afs-admin.css');
          wp_enqueue_script( 'bootstrap', plugin_dir_url( __DIR__  ) .'public/js/bootstrap.min.js', array(), '1.0.0', true );
          wp_enqueue_script( 'jquery', plugin_dir_url( __DIR__  ) .'public/js/jquery-3.2.1.slim.min.js', array(), '3.2.1', true );
      }
  }
  
  add_action( 'admin_enqueue_scripts', 'afs_wpdocs_scripts_admin' );