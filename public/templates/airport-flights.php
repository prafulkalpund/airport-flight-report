<?php

if(empty($flights)){
    echo '<h2>No data found</h2>';
    return;
}

$afs_default_airport_iata = get_option('afs_default_airport_iata');
if(empty($afs_default_airport_iata)){
    $afs_default_airport_iata = 'LHR';
}

// Here all the airports name and iata code is getting fetched to create dropdown list.
global $wpdb;

$query = "SELECT a.iata, b.airport from (SELECT DISTINCT meta_value as iata, post_id FROM wp_postmeta WHERE meta_key LIKE '%afs_arrival_iata%' GROUP BY meta_value) as a JOIN (SELECT DISTINCT meta_value as airport, post_id FROM wp_postmeta WHERE meta_key LIKE '%afs_arrival_airport%' GROUP BY meta_value) as b on a.post_id=b.post_id";

$results = $wpdb->prepare($query);
$departure_airports = $wpdb->get_results($results, ARRAY_A);


$query = "SELECT a.iata, b.airport from (SELECT DISTINCT meta_value as iata, post_id FROM wp_postmeta WHERE meta_key LIKE '%afs_arrival_iata%' GROUP BY meta_value) as a JOIN (SELECT DISTINCT meta_value as airport, post_id FROM wp_postmeta WHERE meta_key LIKE '%afs_arrival_airport%' GROUP BY meta_value) as b on a.post_id=b.post_id";

$results = $wpdb->prepare($query);
$arrival_airports = $wpdb->get_results($results, ARRAY_A );

$obj_merged = array_merge($arrival_airports, $departure_airports);
$airports_name_and_codes =array_unique($obj_merged, SORT_REGULAR);

$afs_airport_options = '';

foreach ($airports_name_and_codes as $key => $value) {
    $selected = '';
    $iata = $value['iata'];
    if($iata == $afs_default_airport_iata){
        continue;
    }

    if($iata == $args['airport_second']){
        $selected = ' selected ';
    }
    $airport = $value['airport'];
    $afs_airport_options .= '<option value="'.$iata.'" '.$selected.'>'.$airport.'</option>';
}

// Nonce for ajax
$afs_nonce = wp_create_nonce("afs_flight_between_airports_nonce");
?>

<div class="container" id="afs_airport_flights_list">
<input type="hidden" id="afs_default_airport_iata" value="<?php echo $afs_default_airport_iata;?>">
    <div class="row">
        <div class="col-md-4">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php  echo $afs_default_airport_iata;?>
                </button>
            </div>
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <select class="form-select afs-airports-dropdown" aria-label="Default select example" data-nonce="<?php echo $afs_nonce;?>">
                <option selected><?php _e( 'Choose another airport', 'airport-flight-status' );?></option>
                <?php echo $afs_airport_options;?>
            </select>
        </div>
    </div>
</div>

<?php 

if(empty($flights)){
    _e('No data found', 'airport-flight-status' );
    return;
}

?>
<div id="afs_flight_between_airports_main_container">
<table id="afs_flight_between_airports" class="display" style="width:100%">
    <thead>
        <tr>
            <th><?php _e( 'Flight', 'airport-flight-status' );?></th>
        </tr>
    </thead>
    <tbody>
    <?php
   
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
    
    ?>
<tr>
    <td>
<div class="container">
    <div class="card text-center">
        <div class="card-header">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="card-title"><?php echo $departure_iata;?></h5>
                </div>
                <div class="col-md-4">
                    <span class="dashicons dashicons-airplane"></span>
                </div>
                <div class="col-md-4">
                    <h5 class="card-title"><?php echo $arrival_iata;?></h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <h6><?php echo $departure_airport;?></h6>
                    <p><?php _e( 'Departure date', 'airport-flight-status' );?><h6><?php echo $departure_actual_flight_date;?></h6></p>
                    <p><?php _e( 'Time', 'airport-flight-status' );?><h6><?php echo $departure_actual_time;?></h6></p>
                </div>
                <div class="col-md-2">
                    <h6><?php echo $airline_name;?></h6>
                </div>
                <div class="col-md-5">
                <h6><?php echo $arrival_airport;?></h6>
                    <p><?php _e( 'Arrived date', 'airport-flight-status' );?><h6><?php echo $arrival_actual_flight_date;?></h6></p>
                    <p><?php _e( 'Time', 'airport-flight-status' );?><h6><?php echo $arrival_actual_time;?></h6></p>
                </div>
        </div>
    </div>
  </div>
  </div>
  </td>
</tr>
<?php } ?>
</tbody>
</table>
</div>