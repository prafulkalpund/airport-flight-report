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
           url : ajs_extra_param.ajaxurl,
           data : {action: "afs_fetch_flight_details", post_id : post_id, nonce: nonce},
           success: function(response) {
              if(response.type == "success") {
                var parent = jQuery('#collapse_'+current_count)
                var link = jQuery('#afs_airport_page_link').val();
                var default_airport = jQuery('#afs_default_airport_iata').val();
                

                if(response.flight_type == 'departure'){
                    link = link+'?default='+default_airport+'&destination='+response.departure_iata;
                    var html = '<a href="'+link+'" target="_blank">'+response.departure_airport+'</a>';
                    parent.find(".afs-from-airport-name").html(html)
                    parent.find(".afs-to-airport-name").text(response.arrival_airport)
                }else{
                    link = link+'?default='+default_airport+'&destination='+response.arrival_iata;
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
           url : ajs_extra_param.ajaxurl,
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

/**
 * Google map code starts here.
 */

var map;
var latlng;
var infowindow;

jQuery(document).ready(function() {

    // All airports name and iata is getting from afs_public_functions file
    var data = ajs_extra_param.all_flight_airport_name_iata;

    //As our google api KEY IS NOT WORKING i have created data as follow assuming that this data we will get using the commented code service.findPlaceFromQuery() by google api. Note: It is done just to give you an rough idea of how code will work.

    //get data set from the backend in a json structure
    var data = [{
            "airport_name": "Dubrovnik Airport",
            "iata": "DBV",
            "latitude": "42.5603",
            "longitude": "18.2622"
        },
        {
            "airport_name": "Podgorica Airport",
            "iata": "JFK",
            "latitude": "42.3678",
            "longitude": "19.2467"
        },
        {
            "airport_name": "John F. Kennedy International Airport",
            "iata": "JFK",
            "latitude": "40.6413",
            "longitude": "73.7781"
        },
        {
            "airport_name": "Jersey Airport",
            "iata": "JER",
            "latitude": "49.2075",
            "longitude": "2.1952"
        },
        {
            "airport_name": "Glasgow International",
            "iata": "GLA",
            "latitude": "55.869100",
            "longitude": "-4.435100"
        }
    ];
    AddMarkerInGoogleMap(data);
});
function AddMarkerInGoogleMap(data) {

    //Creating instance of google map
    var gm = google.maps; 

    //Add initial map option
    var mapOptions = {
        center: new google.maps.LatLng(42.5603, 18.2622),
        zoom: 4,
    };

    //Using afs_map div container to show the map.
    map = new google.maps.Map(document.getElementById("afs_map"), mapOptions);
    //create instance of google information infowindow with max width 850.
    infowindow = new google.maps.InfoWindow({
        maxWidth: 850,
    });

    var marker, i;
    var service = new google.maps.places.PlacesService(map);

    for (var i = 0; i < data.length; i++) {

        /*
        * This code is related to service find place google api
        * Note: This code is not tested due to API issue

        var request = {
            query: data[i]['airport'],
            fields: ['name', 'geometry'],
         };
         console.log(request, 'request');

        service.findPlaceFromQuery(request, function(results, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                console.log('results');
                console.log(results);
            //    for (var i = 0; i < results.length; i++) {
            //         //Will add marker - another approach
            //    }
            //    map.setCenter(results[0].geometry.location);
            }
         });


        marker = new gm.Marker({
            position: new gm.LatLng(results[1]),
            map: map,
            title: data[i]['airport_name'],
            icon: {
                url: ajs_extra_param.plane_img,
                scaledSize: new google.maps.Size(20, 20)
            }
        });
        */

        marker = new gm.Marker({
            position: new gm.LatLng(data[i]['latitude'], data[i]['longitude']),
            map: map,
            title: data[i]['airport_name'],
            icon: {
                url: ajs_extra_param.plane_img,
                scaledSize: new google.maps.Size(20, 20)
            }
        });

        // This event expects a click on a marker
        // When this event is fired the Info Window is opened.
        google.maps.event.addListener(
            marker,
            'click',
            (
                function(marker, i) {
                    return function() {
                        airport_iata = data[i]['iata'];
                        infowindow.open(map, marker);
                        var map_nonce = jQuery("#afs_fetch_all_flights_from_airport_nonce").val();
                        jQuery.ajax({
                           type : "post",
                           dataType : "json",
                           url : ajs_extra_param.ajaxurl,
                           data : {action: "afs_fetch_all_flights_from_airport", airport_iata : airport_iata, nonce: map_nonce},
                           success: function(response) {
                              if(response.type == "success") {
                                var sub_content = '';
                                response.flights.forEach((item, index)=>{
                                   sub_content += '<tr>'+
                                   '<td>'+item.flight_type+'</td>'+
                                   '<td>'+item.airline_name+'</td>'+
                                   '<td>'+item.flight_number+'</td>'+
                                   '<td>'+item.flight_time+'</td>'+
                                ' </tr>'
                                });

                                var content = '<div id="iw-container">' +
                                '<div class="iw-title">Flight Details for ' +response.airport_iata+'</div>' +
                                '<div class="iw-content">'+
                                    '<table class="table">'+
                                    '<tr>'+
                                      '<th>Type</th>'+
                                      '<th>Airline</th>'+
                                      '<th>Flight</th>'+
                                      '<th>Scheduled At</th>'+
                                   ' </tr>'+
                                   sub_content+
                                   '</table>'+
                                '</div>' +
                                '<div class="iw-bottom-gradient"></div>' +
                              '</div>';
                                 infowindow.setContent(content);
                              }else{
                                 infowindow.setContent("No response found");
                              }
                           }.bind(infowindow)
                       });
                    };
                }
            )(marker, i)
        );

        // Event that closes the Info Window with a click on the map
        google.maps.event.addListener(map, 'click', function() {
            infowindow.close();
        });

        //Customization
        google.maps.event.addListener(infowindow, 'domready', function() {

            // Reference to the DIV that wraps the bottom of infowindow
            var iwOuter = jQuery('.gm-style-iw');
        
            var iwBackground = iwOuter.prev();
        
            // Reference to the div that groups the close button elements.
            var iwCloseBtn = iwOuter.next();
        
            // Apply the desired effect to the close button
            iwCloseBtn.css({opacity: '1', right: '38px', top: '3px', border: '7px solid #48b5e9', 'border-radius': '13px', 'box-shadow': '0 0 5px #3990B9'});
        
            // If the content of infowindow not exceed the set maximum height, then the gradient is removed.
            if(jQuery('.iw-content').height() < 140){
                jQuery('.iw-bottom-gradient').css({display: 'none'});
            }
        
            // The API automatically applies 0.7 opacity to the button after the mouseout event. This function reverses this event to the desired value.
            iwCloseBtn.mouseout(function(){
                jQuery(this).css({opacity: '1'});
            });
          });
    }
}
