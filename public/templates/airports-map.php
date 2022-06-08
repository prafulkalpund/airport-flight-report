<?php 

// Nonce for ajax
$afs_map_nonce = wp_create_nonce("afs_fetch_all_flights_from_airport_nonce");

?>

<div>
    <input type="hidden" id="afs_fetch_all_flights_from_airport_nonce" value="<?php echo $afs_map_nonce;?>">
    
    <div id="afs_map" style="height: 800px; width: 100%;"></div>
    
    <script src="https://maps.googleapis.com/maps/api/js?key=ADD_YOUR_KEY&libraries=places"
    defer></script>
</div>