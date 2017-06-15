<?php
    if($settings->address != '') {
        $mapaddress = $settings->address;
    } else {
        $mapaddress = 'Perth, Western Australia';
    }
    // Get JSON results from this request
    $centergeo = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($mapaddress).'&sensor=false');

    // Convert the JSON to an array
    $centergeo = json_decode($centergeo, true);

    if ($centergeo['status'] == 'OK') {
      // Get Lat & Long
      $centerlat = $centergeo['results'][0]['geometry']['location']['lat'];
      $centerlong = $centergeo['results'][0]['geometry']['location']['lng'];
    }
?>
     <script>

      function initMap() {
        var address = {lat: <?php echo $centerlat; ?>, lng: <?php echo $centerlong; ?>};
        var map = new google.maps.Map(document.getElementById('gmap-<?php echo $id; ?>'), {
          zoom: <?php echo $settings->zoom; ?>,
          center: address
        });

        setMarkers(map);
      }
         
      <?php 
        $markers = $settings->address_fields;  
         $c = 1; ?> 
        var addresses = [
        <?php foreach ($markers as $marker) {
            $geo = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($marker->extra_address).'&sensor=false');

            // Convert the JSON to an array
            $geo = json_decode($geo, true);

            if ($geo['status'] == 'OK') {
              // Get Lat & Long
              $latlong = $geo['results'][0]['geometry']['location']['lat'] . ',' . $long = $geo['results'][0]['geometry']['location']['lng'];
            }    
            $address_label = $marker->label;
            $address_info = $marker->info_address;
            $address_geo = $latlong;
                echo "['" . $address_label . "', " . $latlong . ", " . $c++ . ", '" . $address_info . "'],";

        }
        ?> ]; <?php

        ?>

      function setMarkers(map) {
        // Adds markers to the map.

        // Marker sizes are expressed as a Size of X,Y where the origin of the image
        // (0,0) is located in the top left of the image.

        // Origins, anchor positions and coordinates of the marker increase in the X
        // direction to the right and in the Y direction down.
        var image = {
            <?php if ($settings->marker == '') { ?>
                url: '<?php echo  plugin_dir_url( __FILE__ ) . '/marker.png'; ?>',
            <?php } else { ?>
                url: '<?php echo $settings->marker_src; ?>',
            <?php } ?>
          size: new google.maps.Size(32, 32),

          origin: new google.maps.Point(0, 0),

          anchor: new google.maps.Point(0, 32)
        };
          
          
          
          var infowindow = new google.maps.InfoWindow({
              content: "loading..."
            });

        // Shapes define the clickable region of the icon. The type defines an HTML
        // <area> element 'poly' which traces out a polygon as a series of X,Y points.
        // The final coordinate closes the poly by connecting to the first coordinate.
        var shape = {
          coords: [1, 1, 1, 20, 18, 20, 18, 1],
          type: 'poly'
        };
        
          
        for (var i = 0; i < addresses.length; i++) {
          var newaddress = addresses[i];
          var marker = new google.maps.Marker({
            position: {lat: newaddress[1], lng: newaddress[2]},
            map: map,
            icon: image,
            shape: shape,
            title: newaddress[0],
            zIndex: newaddress[3],
            html: newaddress[4],
          });
            
            var contentString = 'Some content';
            
            google.maps.event.addListener(marker, "click", function () {
                infowindow.setContent(this.html);
                infowindow.open(map, this);
            });
        }
      }
    </script>
<?php if($settings->apikey !='') { ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $settings->apikey; ?>&callback=initMap"
  async defer>
</script>
<?php } ?>

<div id="gmap-<?php echo $id; ?>"></div>