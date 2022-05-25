jQuery(document).ready(function () {
    jQuery('#afs_flights_table').DataTable({
        "bFilter": false,
        "bInfo": false,
        "bSort": false,
        "bLengthChange": false,
        "stripeClasses": []
    });

    jQuery('#afs_flight_between_airports').DataTable({
        "bFilter": false,
        "bInfo": false,
        "bSort": false,
        "bLengthChange": false,
        "stripeClasses": []
    });

    jQuery("#afs_accordion").on("click",".afs-check-details",function(e){
        e.preventDefault();
        if(jQuery(this).hasClass( "collapsed" ) == false){
            return;
        }

        var post_id = jQuery(this).attr("data-post_id")
        var current_count = jQuery(this).attr("data-current_count")
        var nonce = jQuery(this).attr("data-nonce")
  
        jQuery.ajax({
           type : "post",
           dataType : "json",
           url : ajs_ajax_url.ajaxurl,
           data : {action: "afs_fetch_flight_details", post_id : post_id, nonce: nonce},
           success: function(response) {
              if(response.type == "success") {
                var parent = jQuery('#collapse_'+current_count)
                var link = jQuery('#afs_airport_page_link').val();

                if(response.flight_type == 'departure'){
                    link = link+'?default=LHR&destination='+response.departure_iata;
                    var html = '<a href="'+link+'" target="_blank">'+response.departure_airport+'</a>';
                    parent.find(".afs-from-airport-name").html(html)
                    parent.find(".afs-to-airport-name").text(response.arrival_airport)
                }else{
                    link = link+'?default=LHR&destination='+response.arrival_iata;
                    var html = '<a href="'+link+'" target="_blank">'+response.arrival_airport+'</a>';
                    parent.find(".afs-to-airport-name").html(html)
                    parent.find(".afs-from-airport-name").text(response.departure_airport)
                }
                
                parent.find(".afs-from-airport-date").text(response.departure_scheduled_flight_date)
                parent.find(".afs-from-airport-scheduled").text(response.departure_scheduled_time)
                parent.find(".afs-from-airport-estimated").text(response.departure_estimated_time)
                parent.find(".afs-from-airport-terminal").text(response.departure_terminal)
                parent.find(".afs-from-airport-gate").text(response.departure_gate)

                parent.find(".afs-to-airport-date").text(response.arrival_scheduled_flight_date)
                parent.find(".afs-to-airport-scheduled").text(response.arrival_scheduled_time)
                parent.find(".afs-to-airport-estimated").text(response.arrival_estimated_time)
                parent.find(".afs-to-airport-terminal").text(response.arrival_terminal)
                parent.find(".afs-to-airport-gate").text(response.arrival_gate)
              }
              else {
                 alert("No response found")
              }
           }
        })   
  
     });

     jQuery("#afs_airport_flights_list").on("change",".afs-airports-dropdown",function(e){
        e.preventDefault();
        console.log(jQuery(this).attr("data-nonce"));
        var destination = jQuery(this).val()
        var nonce = jQuery(this).attr("data-nonce")

        jQuery('#afs_flight_between_airports_main_container').empty();
  
        jQuery.ajax({
           type : "post",
           dataType : "json",
           url : ajs_ajax_url.ajaxurl,
           data : {action: "afs_flight_between_airports", destination : destination, nonce: nonce},
           success: function(response) {
              if(response.type == "success") {
                console.log(response.afs_html);
                jQuery('#afs_flight_between_airports_main_container').html(response.afs_html);
                jQuery('#afs_flight_between_airports').DataTable({
                  "bFilter": false,
                  "bInfo": false,
                  "bSort": false,
                  "bLengthChange": false,
                  "stripeClasses": []
              });
                // jQuery('#afs_airport_flights_list').replacewith(response.afs_html);
              } else {
                 alert("No response found")
              }
           }
        })   
  
     });
});