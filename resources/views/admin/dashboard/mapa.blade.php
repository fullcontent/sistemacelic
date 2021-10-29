<!DOCTYPE html>
<html>
  <head>
    <title>Earthquake Markers</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <link rel="stylesheet" type="text/css" href="./style.css" />
    
  </head>
  <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
#map {
  height: 100%;
}

/* Optional: Makes the sample page fill the window. */
html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
}
  </style>
  <body>
    <div id="map"></div>

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDvbAJ_BSNB6E5_P7DpIOmGrZI8GURRmI0&callback=initMap&libraries=&v=weekly"
      async
    ></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>



    // Initialize and add the map
  function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 6,
      center: new google.maps.LatLng(-14.235004, -51.92528),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
  
    
  
  
  function codeAddress(address) {
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });
      } else {
        alert('Geocode was not successful for the following reason: ' + status);
      }
    });
  }

        function addInfoWindow(marker, message) {

      var infoWindow = new google.maps.InfoWindow({
          content: message
      });

      google.maps.event.addListener(marker, 'click', function () {
          infoWindow.open(map, marker);
      });
      }

  
    
    var locations = {!! json_encode($cidades) !!};

   

    var apikey = "AIzaSyDvbAJ_BSNB6E5_P7DpIOmGrZI8GURRmI0";
    const image =
    "https://yata.ostr.locaweb.com.br/72153c158e827ca039d7fa915121f15ea9b4628c8c92ee745221f3041221cba2";

    var marker, i;

    for (i = 0; i < locations.length; i++) {  
      
      var busca = locations[i]['endereco'] + ' - ' +locations[i]['cidade']+ '/' + locations[i]['uf'] + ' Brasil';
      var query = 'https://maps.googleapis.com/maps/api/geocode/json?address=' + busca + '&key=' + apikey;
      var empresa = locations[i]['empresa'];
      
      
          $.getJSON(query, function (data) {
            if (data.status === 'OK') { 
              var geo_data = data.results[0];
                marker = new google.maps.Marker({
                map: map,
                position: geo_data.geometry.location,
                title: empresa,
                icon: image,
            });
                                    
            }
            // addInfoWindow(marker, marker.title);
 
        })
    }

   

}
  
    </script>
  </body>
</html>