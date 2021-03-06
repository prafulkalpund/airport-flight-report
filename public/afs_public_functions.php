<?php

   /**
    * This function is callback function for arrival flights shortcode
    * @param array $attr parameters of the shortcode.
    *
    */
    function afs_fetch_arrivals_details($attr) {
        $flights = [];
        $afs_default_airport_iata = get_option('afs_default_airport_iata');
        if(empty($afs_default_airport_iata)){
            $afs_default_airport_iata = 'LHR';
        }

        $args = shortcode_atts( array('arrival_iata' => $afs_default_airport_iata), $attr );

        $datetime = new DateTime();
        $query_args = array(
            'post_type'  => 'flight',
            'post_status'   => 'publish',
            'fields'        => 'ids',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'   => 'afs_arrival_iata', // Custom field key.
                    'value' => $afs_default_airport_iata, // Order of values doesn't matter.
                    'compare' => 'LIKE'
                ),
                array(
                    'key'   => 'afs_departure_scheduled', // Custom field key.
                    'value' => $datetime->format(DateTime::ISO8601), // Order of values doesn't matter.
                    'compare' => '>='
                ),
            )
        );

        // The query
        $meta_query = new WP_Query( $query_args );
        if ($meta_query->have_posts()){
            $flights = $meta_query->posts;
        }

        if(empty($flights)){
            _e('No data found', 'airport-flight-status' );
            return;
        }
        
        ob_start();
        include_once(plugin_dir_path( __DIR__ ).'public/templates/flights-list-page.php');
        return ob_get_clean();
      }
      
      add_shortcode('afs_arrivals', 'afs_fetch_arrivals_details');

    /**
    * This function is callback function for departure flights shortcode.
    * @param array $attr parameters of the shortcode.
    *
    */
    function afs_fetch_departure_details($attr) {
        $flights = [];

        $afs_default_airport_iata = get_option('afs_default_airport_iata');
        if(empty($afs_default_airport_iata)){
            $afs_default_airport_iata = 'LHR';
        }

        $args = shortcode_atts( array('departure_iata' => $afs_default_airport_iata), $attr );

        $datetime = new DateTime();
        $query_args = array(
            'post_type'  => 'flight',
            'post_status'   => 'publish',
            'fields'        => 'ids',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'   => 'afs_departure_iata', // Custom field key.
                    'value' => $afs_default_airport_iata, // Order of values doesn't matter.
                    'compare' => 'LIKE'
                ),
                array(
                    'key'   => 'afs_departure_scheduled', // Custom field key.
                    'value' => $datetime->format(DateTime::ISO8601), // Order of values doesn't matter.
                    'compare' => '>='
                ),
            )
        );

        // The query
        $meta_query = new WP_Query( $query_args );
        if ($meta_query->have_posts()){
            $flights = $meta_query->posts;
        }
    
        if(empty($flights)){
            _e('No data found', 'airport-flight-status' );
            return;
        }
        
        // $flights = $wpdb->get_col($results);
        ob_start();
        include_once(plugin_dir_path( __DIR__ ).'public/templates/flights-list-page.php');
        return ob_get_clean();
    }

    add_shortcode('afs_departure', 'afs_fetch_departure_details');
  
    /**
    * This function has query that fetch posts between two airports.
    * @param string $first_airport first airport iata.
    * @param string $second_airport second airport iata.
    *
    */
    function afs_get_flights_between_airports($first_airport, $second_airport){
        $datetime = new DateTime();
        $args = array(
            'post_type'  => 'flight',
            'post_status'   => 'publish',
            'fields'        => 'ids',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'   => 'afs_departure_iata', // Custom field key.
                    'value' => array( $first_airport, $second_airport ), // Order of values doesn't matter.
                ),
        
                array(
                    'key'   => 'afs_arrival_iata', // Custom field key.
                    'value' => array( $first_airport, $second_airport ), // Order of values doesn't matter.
                ),
        
                array(
                    'key'   => 'afs_departure_scheduled', // Custom field key.
                    'value' => $datetime->format(DateTime::ISO8601), // Order of values doesn't matter.
                    'compare' => '<=',
                ),
            )
        );

        // The query
        $meta_query = new WP_Query( $args );
        if ($meta_query->have_posts()){
            $posts = $meta_query->posts;
            return $posts;
        }

        return false;
    }


    function afs_fetch_all_airports_names_and_iata(){

        global $wpdb;

        $query = "SELECT a.iata, b.airport from (SELECT DISTINCT meta_value as iata, post_id FROM wp_postmeta WHERE meta_key LIKE '%afs_arrival_iata%' GROUP BY meta_value) as a JOIN (SELECT DISTINCT meta_value as airport, post_id FROM wp_postmeta WHERE meta_key LIKE '%afs_arrival_airport%' GROUP BY meta_value) as b on a.post_id=b.post_id";

        $results = $wpdb->prepare($query);
        $departure_airports = $wpdb->get_results($results, ARRAY_A);

        $query = "SELECT a.iata, b.airport from (SELECT DISTINCT meta_value as iata, post_id FROM wp_postmeta WHERE meta_key LIKE '%afs_arrival_iata%' GROUP BY meta_value) as a JOIN (SELECT DISTINCT meta_value as airport, post_id FROM wp_postmeta WHERE meta_key LIKE '%afs_arrival_airport%' GROUP BY meta_value) as b on a.post_id=b.post_id";

        $results = $wpdb->prepare($query);
        $arrival_airports = $wpdb->get_results($results, ARRAY_A );

        $obj_merged = array_merge($arrival_airports, $departure_airports);
        $airports_name_and_codes = array_unique($obj_merged, SORT_REGULAR);

        return $airports_name_and_codes;
    }

    /**
    * This function is callback function for flights between two airports shortcode.
    * @param array $attr parameters of the shortcode.
    *
    */
    function afs_fetch_airport_details($attr) {

        $afs_default_airport_iata = get_option('afs_default_airport_iata');
        if(empty($afs_default_airport_iata)){
            $afs_default_airport_iata = 'LHR';
        }

        $args = shortcode_atts( array(
                'airport_first' => $afs_default_airport_iata,
                'airport_second' => 'DOH',
                'airport_filter' => 'Applicable',
                ), $attr );
    
        if(!empty($_REQUEST['destination']) ){
            $args['airport_second'] = $_REQUEST['destination'];
        }
        
        $flights = afs_get_flights_between_airports($args['airport_first'], $args['airport_second']);

        if($args['airport_filter'] === 'Applicable'){
           
            // Here all the airports name and iata code is getting fetched to create dropdown list.
           $airports_name_and_codes = afs_fetch_all_airports_names_and_iata();
        }
        
        ob_start();
        // include_once(plugin_dir_path( __DIR__ ).'public/templates/flights-list-page.php');
        include_once(plugin_dir_path( __DIR__ ).'public/templates/airport-flights.php');
        return ob_get_clean();
    } 
    
    add_shortcode('afs_airport', 'afs_fetch_airport_details');
  
    
    /**
     * Ajax
     * This is callback function of ajax to fetch the flight details.
     */
    function afs_fetch_flight_details() {

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "afs_fetch_flight_details_nonce")) {
            exit("Wrong request");
        }   

        $post_id = $_REQUEST['post_id'];

        $result['departure_iata'] = get_post_meta($post_id, 'afs_departure_iata', true);
        $result['departure_airport'] = get_post_meta($post_id, 'afs_departure_airport', true);
        $result['departure_terminal'] = get_post_meta($post_id, 'afs_departure_terminal', true);
        $result['departure_gate'] = get_post_meta($post_id, 'afs_departure_gate', true);
        $departure_scheduled = get_post_meta($post_id, 'afs_departure_scheduled', true);

        $dt = new DateTime($departure_scheduled);
        $flight_date = $dt->format('m/d/Y');
        $result['departure_scheduled_flight_date'] = $flight_date;
        $result['departure_scheduled_time'] = $dt->format('H:i:s');

        $departure_estimated = get_post_meta($post_id, 'afs_departure_estimated', true);

        $dte = new DateTime($departure_estimated);
        $result['departure_estimated_flight_date'] = $dte->format('m/d/Y');
        $result['departure_estimated_time'] = $dte->format('H:i:s');

        $result['departure_actual'] = get_post_meta($post_id, 'afs_departure_actual', true);



        $result['arrival_iata'] = get_post_meta($post_id, 'afs_arrival_iata', true);
        $result['arrival_airport'] = get_post_meta($post_id, 'afs_arrival_airport', true);
        $result['arrival_terminal'] = get_post_meta($post_id, 'afs_arrival_terminal', true);
        $result['arrival_gate'] = get_post_meta($post_id, 'afs_arrival_gate', true);
        $arrival_scheduled = get_post_meta($post_id, 'afs_arrival_scheduled', true);

        $dt = new DateTime($arrival_scheduled);
        $flight_date = $dt->format('m/d/Y');
        $result['arrival_scheduled_flight_date'] = $flight_date;
        $result['arrival_scheduled_time'] = $dt->format('H:i:s');

        $arrival_estimated = get_post_meta($post_id, 'afs_arrival_estimated', true);

        $dte = new DateTime($arrival_estimated);
        $result['arrival_estimated_flight_date'] = $dte->format('m/d/Y');
        $result['arrival_estimated_time'] = $dte->format('H:i:s');

        $result['arrival_actual'] = get_post_meta($post_id, 'afs_arrival_actual', true);
        
        $afs_default_airport_iata = get_option('afs_default_airport_iata');
        if(empty($afs_default_airport_iata)){
            $afs_default_airport_iata = 'LHR';
        }

        if($result['arrival_iata'] == $afs_default_airport_iata){
        $result['flight_type'] = 'departure';
        }else{
        $result['flight_type'] = 'arrival';
        }

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result['type'] = 'success';
            $result = json_encode($result);
            echo $result;
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        die();

    }

    add_action("wp_ajax_afs_fetch_flight_details", "afs_fetch_flight_details");
    add_action("wp_ajax_nopriv_afs_fetch_flight_details", "afs_fetch_flight_details");


    /**
     * Ajax
     * This is callback function to fetch flights between airports
     */
    function afs_flight_between_airports() {

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "afs_flight_between_airports_nonce")) {
            exit("Wrong request");
        }   

        $destination = $_REQUEST['destination'];

        $afs_default_airport_iata = get_option('afs_default_airport_iata');
        if(empty($afs_default_airport_iata)){
            $afs_default_airport_iata = 'LHR';
        }

        $flights = afs_get_flights_between_airports($afs_default_airport_iata, $destination);
        
        $afs_html = '
        <table id="afs_flight_between_airports" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Flight</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($flights as $key => $value) {
        
            $departure_iata = get_post_meta($value, 'afs_departure_iata', true);
            $departure_airport = get_post_meta($value, 'afs_departure_airport', true);
            $departure_actual = get_post_meta($value, 'afs_departure_actual', true);
    
            $dt = new DateTime($departure_actual);
            $flight_date = $dt->format('m/d/Y');
            $departure_actual_flight_date = $flight_date;
            $departure_actual_time = $dt->format('H:i:s');
    
            $arrival_iata = get_post_meta($value, 'afs_arrival_iata', true);
            $arrival_airport = get_post_meta($value, 'afs_arrival_airport', true);
            $arrival_actual = get_post_meta($value, 'afs_arrival_actual', true);
    
            $dt = new DateTime($arrival_actual);
            $flight_date = $dt->format('m/d/Y');
            $arrival_actual_flight_date = $flight_date;
            $arrival_actual_time = $dt->format('H:i:s');
    
            $airline_name = get_post_meta($value, 'afs_airline_name', true);    
    
            $flight_number = get_post_meta($value, 'afs_flight_number', true);
            $flight_iata = get_post_meta($value, 'afs_flight_iata', true);
            $afs_html .='<tr>
            <td>
            <div class="container">
                <div class="card text-center">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h5 class="card-title">'.$departure_iata.'</h5>
                            </div>
                            <div class="col-md-4">
                                <span class="dashicons dashicons-airplane"></span>
                            </div>
                            <div class="col-md-4">
                                <h5 class="card-title">'.$arrival_iata.'</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <h6>'.$departure_airport.'</h6>
                                <p>'.__( 'Departure date', 'airport-flight-status' ).' <h6>'.$departure_actual_flight_date.'</h6></p>
                                <p>'.__( 'Time', 'airport-flight-status' ).' <h6>'.$departure_actual_time.'</h6></p>
                            </div>
                            <div class="col-md-2">
                                <h6>'.$airline_name.'</h6>
                            </div>
                            <div class="col-md-5">
                            <h6>'.$arrival_airport.'</h6>
                                <p>'.__( 'Arrived date ', 'airport-flight-status' ).'<h6>'.$arrival_actual_flight_date.'</h6></p>
                                <p>'.__( 'Time ', 'airport-flight-status' ).' <h6>'.$arrival_actual_time.'</h6></p>
                            </div>
                    </div>
                </div>
            </div>
            </div>
            </td>
            </tr>';
        }


        $afs_html .= '</tbody></table></div>';

        // ob_start();
        // include_once(plugin_dir_path( __DIR__ ).'public/templates/airport-flights.php');
        // $result['afs_html'] = ob_get_flush();

        $result['afs_html'] = $afs_html;
        
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result['type'] = 'success';
            $result = json_encode($result);
            echo $result;
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        die();

    }

    add_action("wp_ajax_afs_flight_between_airports", "afs_flight_between_airports");
    add_action("wp_ajax_nopriv_afs_flight_between_airports", "afs_flight_between_airports");

    
/**
 * Map start here
 */

    /**
    * This function is callback function for departure flights shortcode.
    * @param array $attr parameters of the shortcode.
    *
    */
    function afs_plot_map() {
        ob_start();
        include_once(plugin_dir_path( __DIR__ ).'public/templates/airports-map.php');
        return ob_get_clean();
    }

    add_shortcode('afs_map', 'afs_plot_map');


    /**
    * This function has query that fetch posts between two airports.
    * @param string $airport airport iata to fetch flights.
    * @param string $type departure or departure, Default departure
    * @param string $afs_compare_by compare by > or <=. Default >
    *
    */
    function afs_get_departure_or_arrival_flights_from_airport($airport, $type='departure', $afs_compare_by = '>'){
        $airport_type = 'afs_departure_iata';
        $airport_type_compare = 'afs_departure_scheduled';

        if($type == 'arrival'){
            $airport_type = 'afs_arrival_iata';
            $airport_type_compare = 'afs_arrival_scheduled';
        }

        $datetime = new DateTime();
        $args = array(
            'post_type'  => 'flight',
            'post_status'   => 'publish',
            'fields'        => 'ids',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'   => $airport_type, // Custom field key.
                    'value' => $airport, // Order of values doesn't matter.
                ),
        
                array(
                    'key'   => $airport_type_compare, // Custom field key.
                    'value' => $datetime->format(DateTime::ISO8601), // Order of values doesn't matter.
                    'compare' => $afs_compare_by,
                ),
            )
        );

        // The query
        $meta_query = new WP_Query( $args );
        if ($meta_query->have_posts()){
            $posts = $meta_query->posts;
            return $posts;
        }

        return false;
    }

    // Comparison function
    function afs_date_compare($element1, $element2) {
        $datetime1 = strtotime($element1['datetime']);
        $datetime2 = strtotime($element2['datetime']);
        return $datetime1 - $datetime2;
    } 


    /**
     * Ajax
     * This is callback function of ajax to fetch the flight details from the pined airport.
     */
    function afs_fetch_all_flights_from_airport() {

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "afs_fetch_all_flights_from_airport_nonce")) {
            exit("Wrong request");
        }

        $airport_iata = $_REQUEST['airport_iata'];

        //Another approch could be call same function of afs_airport shortcode.
        $departure_flights = afs_get_departure_or_arrival_flights_from_airport(
            $airport_iata
        );
        
        //Create an new array
        $all_flights = [];
        
        if(!empty($departure_flights)){

            foreach ($departure_flights as $key => $value) {
                $temp_array = [];
                $ddt = '';
                
                $temp_array['flight_type'] = 'Departure';
                $temp_array['flight_number'] = get_post_meta($value, 'afs_flight_number', true);
                $temp_array['airline_name'] = get_post_meta($value, 'afs_airline_name', true);
                $departure_actual = get_post_meta($value, 'afs_departure_actual', true);
                $ddt = new DateTime($departure_actual);
                $temp_array['flight_time'] = $ddt->format("H:i");
                $all_flights[] = $temp_array;                
            }
        }

        $arrival_flights = afs_get_departure_or_arrival_flights_from_airport(
            $airport_iata, 
            'arrival'
        );

        if(!empty($arrival_flights)){

            foreach ($arrival_flights as $key => $value) {
                $temp_array = [];
                $adt = '';
                
                $temp_array['flight_type'] = 'Arrival';
                $temp_array['airline_name'] = get_post_meta($value, 'afs_airline_name', true);
                $temp_array['flight_number'] = get_post_meta($value, 'afs_flight_number', true);
                $arrival_actual = get_post_meta($value, 'afs_arrival_actual', true);
                $adt = new DateTime($arrival_actual);
                $temp_array['flight_time'] = $adt->format("H:i");
                $all_flights[] = $temp_array;                
            }
        }

        // Sort the array 
        usort($all_flights, 'date_compare');


        $result['flights'] = $all_flights;

        // Adding first and second airport iata into $result variable.
        $result['airport_iata'] = $airport_iata;

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result['type'] = 'success';
            $result = json_encode($result);
            echo $result;
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        die();

    }

    add_action("wp_ajax_afs_fetch_all_flights_from_airport", "afs_fetch_all_flights_from_airport");
    add_action("wp_ajax_nopriv_afs_fetch_all_flights_from_airport", "afs_fetch_all_flights_from_airport");

    /**
     * Enqueue scripts and styles
     */
    function afs_wpdocs_scripts() {
        wp_enqueue_style( 'bootstrap-css', plugin_dir_url( __DIR__ ) .'public/css/bootstrap.min.css');
        wp_enqueue_style( 'datatable-css', plugin_dir_url( __DIR__ ) .'public/css/jquery.dataTables.min.css');
        wp_enqueue_style( 'ajs-public-css', plugin_dir_url( __DIR__ ) .'public/css/afs-public.css');
        wp_enqueue_script( 'jquery', plugin_dir_url( __DIR__ ) .'public/js/jquery-3.2.1.slim.min.js', array(), '3.2.1', true );
        wp_enqueue_script( 'popper', plugin_dir_url( __DIR__ ) .'public/js/popper.min.js', array(), '1.0.0', true );
        wp_enqueue_script( 'bootstrap', plugin_dir_url( __DIR__ ) .'public/js/bootstrap.min.js', array(), '1.0.0', true );
        wp_enqueue_script( 'datatable', plugin_dir_url( __DIR__ ) .'public/js/jquery.dataTables.min.js', array(), '1.12.0', true );
        wp_enqueue_script( 'afs-public-js', plugin_dir_url( __DIR__ ) .'public/js/afs-public.js', array(), '1.12.0', true );
        wp_localize_script(
            'afs-public-js',
            'ajs_extra_param',
            array( 
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'plane_img' => plugin_dir_url( __DIR__ ) . 'plane-red.png',
                'all_flight_airport_name_iata' => afs_fetch_all_airports_names_and_iata(),
            )
        );        
    
    }
    add_action( 'wp_enqueue_scripts', 'afs_wpdocs_scripts' );