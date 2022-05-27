<?php

if(empty($flights)){
    echo '<h2>No data found</h2>';
    return;
}

$afs_default_airport_iata = get_option('afs_default_airport_iata');
if(empty($afs_default_airport_iata)){
    $afs_default_airport_iata = 'LHR';
}
?>
<div id="afs_accordion" class="afs_accordion">
    <input type="hidden" id="afs_airport_page_link" value="<?php echo get_permalink(get_option('afs_airport_page_id'));?>">
    <input type="hidden" id="afs_default_airport_iata" value="<?php echo $afs_default_airport_iata;?>">
<table id="afs_flights_table" class="display" style="width:100%">
    <thead>
        <tr>
            <th><?php _e( 'Flight', 'airport-flight-status' );?></th>
        </tr>
    </thead>
    <tbody>

    <?php
    $afs_nonce = wp_create_nonce("afs_fetch_flight_details_nonce");
    
    foreach ($flights as $key => $value) {
        
        // $link = admin_url('admin-ajax.php?action=afs_fetch_flight_details&post_id='.$value.'&nonce='.$afs_nonce);
        $departure_iata = get_post_meta($value, 'afs_departure_iata', true);
        $departure_timezone = get_post_meta($value, 'afs_departure_timezone', true);
        
        $arrival_iata = get_post_meta($value, 'afs_arrival_iata', true);
        $arrival_timezone = get_post_meta($value, 'afs_arrival_timezone', true);

        $airline_name = get_post_meta($value, 'afs_airline_name', true);    

        $flight_number = get_post_meta($value, 'afs_flight_number', true);
        $flight_iata = get_post_meta($value, 'afs_flight_iata', true);
    
    ?>
<tr>
    <td>
  <div>
    <div id="heading_<?php echo $key;?>">
      <div class="container">
          <div class="row">
              <div class="col-sm">
                <h5 class="mb-0"><?php echo $flight_iata;?></h5>
                <div class="text"><?php echo $airline_name;?></div>  
              </div>
              <div class="col-sm">
                <h5 class="mb-0"><?php echo $departure_iata;?></h5>
                <div class="text"><?php _e( 'Departure', 'airport-flight-status' );?></div>
              </div>
              <div class="col-sm">
                <h5 class="mb-0"><?php echo $arrival_iata;?></h5>
                <div class="text"><?php _e( 'Arrival', 'airport-flight-status' );?></div>
              </div>
              <div class="col-sm">
                <a href="" class="btn btn-primary afs-check-details collapsed" data-toggle="collapse" data-target="#collapse_<?php echo $key;?>" aria-expanded="true" aria-controls="collapse_<?php echo $key;?>" data-post_id="<?php echo $value;?>" data-current_count="<?php echo $key;?>" data-nonce="<?php echo $afs_nonce;?>">Other details</a>
              </div>
          </div>      
      </div>
    </div>
    
    <div id="collapse_<?php echo $key;?>" class="collapse" aria-labelledby="heading_<?php echo $key;?>" data-parent="#afs_accordion">
     <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="card afs-from-airport">
                <div class="card-body">
                    <h5 class="card-title afs-from-airport-name"></h5>
                    <p class="card-text afs-from-airport-date"></p>
                    <div class="row">
                        <div class="col-sm">
                            <h6><?php _e( 'Scheduled', 'airport-flight-status' );?></h6>
                            <div class="text afs-from-airport-scheduled"></div>  
                        </div>
                        <div class="col-sm">
                            <h6><?php _e( 'Estimated', 'airport-flight-status' );?></h6> 
                            <div class="text afs-from-airport-estimated"></div>  
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm">
                            <h6><?php _e( 'Terminal', 'airport-flight-status' );?></h6>  
                            <div class="text afs-from-airport-terminal"></div>  
                        </div>
                        <div class="col-sm">
                            <h6><?php _e( 'Gate', 'airport-flight-status' );?></h6>  
                            <div class="text afs-from-airport-gate"></div>  
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card afs-to-airport">
                <div class="card-body">
                    <h5 class="card-title afs-to-airport-name"></h5>
                    <p class="card-text afs-to-airport-date"></p>
                    <div class="row">
                        <div class="col-sm">
                            <h6><?php _e( 'Scheduled', 'airport-flight-status' );?></h6>
                            <div class="text afs-to-airport-scheduled"></div>  
                        </div>
                        <div class="col-sm">
                            <h6><?php _e( 'Estimated', 'airport-flight-status' );?></h6> 
                            <div class="text afs-to-airport-estimated"></div>  
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm">
                            <h6><?php _e( 'Terminal', 'airport-flight-status' );?></h6>  
                            <div class="text afs-to-airport-terminal"></div>  
                        </div>
                        <div class="col-sm">
                            <h6><?php _e( 'Gate', 'airport-flight-status' );?></h6>  
                            <div class="text afs-to-airport-gate"></div>  
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        </div>
    </div>
  </div>
    </tr>
    
  <?php } ?>
  
</div>


    </td>
        </tr>
    </tbody>
</table>