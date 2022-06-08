<?php 

// Nonce for ajax
$afs_map_nonce = wp_create_nonce("afs_fetch_all_flights_from_airport_nonce");

?>

<div>
    <input type="hidden" id="afs_fetch_all_flights_from_airport_nonce" value="<?php echo $afs_map_nonce;?>">
    
    <div id="afs_map" style="height: 800px; width: 100%;"></div>
    
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCgDWBXUaAtdpQJ8wjRHzQLUFrIaE3RYG0&libraries=places"
    defer></script>
</div>

<!--
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCXPkAsD2V1J8ah8RKorba2MjoR-85cH4k&libraries=places"
    defer></script>
    
    AIzaSyDwYUR5CuOiJqmTRvyCwO6oD7ICQXecSSs
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDwYUR5CuOiJqmTRvyCwO6oD7ICQXecSSs&libraries=places"
    defer></script>
-->
