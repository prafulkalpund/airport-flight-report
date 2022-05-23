<?php

/**
 * The plugin bootstrap file
 * @wordpress-plugin
 * Plugin Name:       Airport flight status reports 
 * Plugin URI:        http://localhost/illustrdevtest
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Praful Kalpund
 * Author URI:        http://localhost/illustrdevtest/Praful-Kalpund
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       airport-flight-status
 * Domain Path:       /languages
 */


function theme_options_panel(){
    add_menu_page(__( 'Airport Flight Status', 'airport-flight-status' ), __( 'Airport Flight Status', 'airport-flight-status' ), 'manage_options', 'afs', 'wps_theme_func');
    // add_submenu_page( 'theme-options', 'Settings page title', 'Settings menu label', 'manage_options', 'theme-op-settings', 'wps_theme_func_settings');
    // add_submenu_page( 'theme-options', 'FAQ page title', 'FAQ menu label', 'manage_options', 'theme-op-faq', 'wps_theme_func_faq');
  }
  add_action('admin_menu', 'theme_options_panel');
   
  function wps_theme_func(){
    include_once(plugin_dir_path( __FILE__ ).'admin/templates/admin-panel.php');
  }

  //Submenu code
//   function wps_theme_func_settings(){
//           echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
//           <h2>Settings</h2></div>';
//   }
//   function wps_theme_func_faq(){
//           echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
//           <h2>FAQ</h2></div>';
//   }


//Frontend


function afs_fetch_details(){

    $queryString = http_build_query([
        'access_key' => 'eead096fd179621c96778d5cb0e507a6'
      ]);
      
    //   $ch = curl_init(sprintf('%s?%s', 'http://api.aviationstack.com/v1/flights', $queryString));
    //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      
      $json = curl_exec($ch);
      curl_close($ch);
      
      $api_result = json_decode($json, true);
      echo '<pre>';
      printf($api_result);
      echo '</pre>';
      

      if(empty($api_result['data'])){

        echo 'Empty data';

      }else{
          
          foreach ($api_result['data'] as $flight) {
              if (!$flight['live']['is_ground']) {
                  echo sprintf("%s flight %s from %s (%s) to %s (%s) is in the air.",
                  $flight['airline']['name'],
                  $flight['flight']['iata'],
                  $flight['departure']['airport'],
                  $flight['departure']['iata'],
                  $flight['arrival']['airport'],
                  $flight['arrival']['iata']
                ), PHP_EOL;
            }
        }
    }    



    return 'Welcome here';
}

add_shortcode('afs', 'afs_fetch_details');