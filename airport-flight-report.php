<?php

/**
 * Plugin Name:       Airport flight Status reports 
 * Plugin URI:        http://localhost/illustrdevtest
 * Description:       This plugin shows the airport specific arrivals and departure. Also, It can shows the flights between two airports.
 * Version:           1.0.0
 * Author:            Praful Kalpund
 * Author URI:        http://localhost/illustrdevtest/Praful-Kalpund
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       airport-flight-status
 * Domain Path:       /languages
 */
  

define( 'AFS_TEXT_DOMAIN', 'airport-flight-status' );

/**
 *  This function creates a custom post type flight.
 */
function afs_register_flight_cpt() {
  register_post_type( 'flight', array(
    'label' => 'Flights',
    'public' => true,
    'capability_type' => 'post',
  ));
}
add_action( 'init', 'afs_register_flight_cpt' );

/**
 * Adding admin function.
 */
if ( is_admin() ) {
  require_once plugin_dir_path( __FILE__ ) . 'admin/afs_admin_functions.php';
}

/**
 * Adding public function.
 */
require_once plugin_dir_path( __FILE__ ) . 'public/afs_public_functions.php';

/**
 * Adding cron-job function.
 */
require_once plugin_dir_path( __FILE__ ) . 'afs-function.php';