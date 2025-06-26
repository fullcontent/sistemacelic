<html>
  <head>
    <title>Earthquake Markers</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>



  <style>
    /* 
 * Always set the map height explicitly to define the size of the div element
 * that contains the map. 
 */
#map {
  height: 100%;
}

/* 
 * Optional: Makes the sample page fill the window. 
 */
html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
}
  </style>
   
   
   
   <script>
   let map;
   var unidades = <?php print_r(json_encode($unidades)) ?>

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
    zoom: 5,
    center: new google.maps.LatLng(-15.7801,-47.9292),
    mapTypeId: "terrain",
    
  });

  const infoWindow = new google.maps.InfoWindow({
    content: "",
    disableAutoPan: true,
  });
  const uluru = { lat: -25.344, lng: 131.031 };

  $.each( unidades, function( index, value ){

    // The marker, positioned at Uluru
    const marker = new google.maps.Marker({
    position: {lat: parseFloat(value.latitude), lng: parseFloat(value.longitude)},
    map: map,
    icon: 'http://sistemacelic.com/public/img/favicon/favicon-32x32.png',
  });

  const contentString = "<div><h2>" + value.empresa.nomeFantasia + "</h2><h3>" + value.nomeFantasia + "</h3><p>" + value.endereco + "," + value.numero + " " + value.complemento + "</p><p>" + value.bairro + " " + value.cep + "</p><p>"+value.cidade+"/"+value.uf+"</p></div>";
  

  marker.addListener("click", () => {
      infoWindow.setContent(contentString);
      infoWindow.open(map, marker);
    });

 });

 
}

window.initMap = initMap;


   </script>
  </head>
  <body>

    
    <div id="map"></div>

    <!-- 
      The `defer` attribute causes the callback to execute after the full HTML
      document has been parsed. For non-blocking uses, avoiding race conditions,
      and consistent behavior across browsers, consider loading using Promises
      with https://www.npmjs.com/package/@googlemaps/js-api-loader.
      -->
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDOIxyXtEWnbcGYXUEQmVHHagZizBplcfI&callback=initMap&v=weekly"
      defer
    ></script>
  </body>
</html>