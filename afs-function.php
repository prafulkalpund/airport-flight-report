<?php
/**
 * This file has a cron scheduled event and callback function to fetch and store data.
 * The event is schedule once in a day. Callback function trigger two API, one fetches arrivals and another fetches departure.
 * It is using flight custom post type to store in wp_post table and relevant flight data is store in wp_postmeta
 * 
 */


/**
 * Setting custom hook for cron job.
 */
add_action( 'afs_pk_cron_hook', 'afs_get_flights_from_api' );

/**
 * This is function to remove unwanted data from string to create valid post slug.
 * @param string $text string to be slugify.
 */
function afs_slugify($text){
  
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
  
    // trim
    $text = trim($text, '-');
  
    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
  
    // lowercase
    $text = strtolower($text);
  
    if (empty($text)) {
      return 'n-a';
    }
  
    return $text;
  }

  /**
   * This function add flight details in wp_postmeta table.
   * @param int $inserted_flight newly inserted post id.
   * @param array|object $flight api flight details array.
   * 
   */
    function afs_flight_post_meta_fields($inserted_flight, $flight ){
    
        if(empty($inserted_flight) || empty($flight)){
            return;
        }
    
        $flight_metadata = [
        'departure_airport' => $flight['departure']['airport'],
        'departure_timezone' => $flight['departure']['timezone'],
        'departure_iata' => $flight['departure']['iata'],
        'departure_terminal' => $flight['departure']['terminal'],
        'departure_gate' => $flight['departure']['gate'],
        'departure_scheduled' => $flight['departure']['scheduled'],
        'departure_estimated' => $flight['departure']['estimated'],
        'departure_actual' => $flight['departure']['actual'],
    
        'arrival_airport' => $flight['arrival']['airport'],
        'arrival_timezone' => $flight['arrival']['timezone'],
        'arrival_iata' => $flight['arrival']['iata'],
        'arrival_terminal' => $flight['arrival']['terminal'],
        'arrival_gate' => $flight['arrival']['gate'],
        'arrival_scheduled' => $flight['arrival']['scheduled'],
        'arrival_estimated' => $flight['arrival']['estimated'],
        'arrival_actual' => $flight['arrival']['actual'],
    
        'airline_name' => $flight['airline']['name'],
    
        'flight_number' => $flight['flight']['number'],
        'flight_iata' => $flight['flight']['iata'],
        ];
    
        foreach ($flight_metadata as $key => $value) {
            $meta_key = 'afs_'.$key;
            update_post_meta($inserted_flight, $meta_key, $value);
        }
    }

    /**
   * This function create a post for fetched data from api.
   * @param JSON $response is a json response from api.
   * 
   */
    function afs_flight_create_posts($response){
        try {     
            // Note that we decode the body's response since it's the actual JSON feed
            $response_body = $response["body"];
            $results = json_decode( $response_body, true); 
     
        } catch ( Exception $ex ) {
            $results = null;
        } // end try/catch
    

        //  check if result has data
        if( ! is_array( $results ) || empty( $results ) ){
            return false;
        }
    
        $flights = $results['data'];  

        foreach( $flights as $flight ){
            
            
            $flight_slug = afs_slugify( 'afs-'.$flight["departure"]["iata"]. '-' . $flight["departure"]["scheduled"]. '-' . $flight["arrival"]["iata"] . '-' . $flight["arrival"]["scheduled"]);

            $existing_flight = get_page_by_path( $flight_slug, 'OBJECT', 'flight' );
        
            if( $existing_flight === null  ){
                
                $inserted_flight = wp_insert_post( [
                    'post_name' => $flight_slug,
                    'post_title' => $flight_slug,
                    'post_type' => 'flight',
                    'post_status' => 'publish'
                ] );

                if( is_wp_error( $inserted_flight ) || $inserted_flight === 0 ) {
                    // die( __( 'Could not insert flight: ', 'airport-flight-status' ) . $flight_slug);
                    // error_log(  __( 'Could not insert flight: ', 'airport-flight-status' ) . $flight_slug );
                    continue;
                }
        
                afs_flight_post_meta_fields($inserted_flight, $flight );
                
            }
        }
    }
  

    /**
     * This is callback function of cron job hook.
     * This function is responsible to fetch data and save it in database.
     */
    function afs_get_flights_from_api() {

        $flights = [];
        
        $afs_default_airport_iata = get_option('afs_default_airport_iata');
        if(empty($afs_default_airport_iata)){
            $afs_default_airport_iata = 'LHR';
        }

        $afs_api_fetched_count = get_option('afs_api_fetched_count');
        if(empty($afs_api_fetched_count)){
            $afs_api_fetched_count = 2;
        }else{
            $afs_api_fetched_count = (int)$afs_api_fetched_count + 2;
        }
        update_option('afs_api_fetched_count', $afs_api_fetched_count);
        
        $queryString = http_build_query([
            'access_key' => get_option('afs_api_key'),
        ]);
		// Arrival link: http://api.aviationstack.com/v1/flights?access_key=eead096fd179621c96778d5cb0e507a6&arr_iata=LHR
        // $arrival_response = wp_remote_get(sprintf('http://api.aviationstack.com/v1/flights?%1$s&arr_iata=%2$s', $queryString, $afs_default_airport_iata ));
        // afs_flight_create_posts($arrival_response);
        
		// Departure link: http://api.aviationstack.com/v1/flights?access_key=eead096fd179621c96778d5cb0e507a6&dep_iata=LHR
        // $departure_response = wp_remote_get(sprintf('http://api.aviationstack.com/v1/flights?%1$s&dep_iata=%2$s', $queryString, $afs_default_airport_iata ));
        // afs_flight_create_posts($departure_response);
       
    }


    /**
     * Schedule event daily.
    */
    if ( ! wp_next_scheduled( 'afs_pk_cron_hook' ) ) {
        wp_schedule_event( time(), 'daily', 'afs_pk_cron_hook' );
    }

    // afs_get_flights_from_api();