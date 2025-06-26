@extends('adminlte::page')


@section('content')

<h3>Onde estão nossos clientes?</h3>

<div class="row">
  <div class="col-md-6">
    <div id="map"></div>
  </div>
  <div class="col-md-6">
    <canvas id="graficoUF"></canvas>
  </div>
</div>


<button onclick="initMap()" class="btn btn-default btn-xs">Centralizar</button>

@endsection


@section('js')
<script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDOIxyXtEWnbcGYXUEQmVHHagZizBplcfI&callback=initMap&v=weekly"
      defer
    ></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  let map;
  var unidades = {!! json_encode($unidades) !!}

      function initMap() {
          map = new google.maps.Map(document.getElementById("map"), {
              zoom: 5,
              center: new google.maps.LatLng(-15.7801, -47.9292),
              mapTypeId: "terrain",
              disableDefaultUI: true,

          });

          const infoWindow = new google.maps.InfoWindow({
              content: "",
              disableAutoPan: true,
          });

          $.each(unidades, function (index, value) {

              
              const marker = new google.maps.Marker({
                  position: {
                      lat: parseFloat(value.latitude),
                      lng: parseFloat(value.longitude)
                  },
                  map: map,
                  icon: 'http://sistemacelic.com/public/img/favicon/favicon-32x32.png',
              });

              const contentString = "<div class='infoWindow' id="+value.id+"><h2>" + value.empresa.nomeFantasia + "</h2><h3>" + value.nomeFantasia + "</h3><p>" + value.endereco + "," + value.numero + " " + value.complemento + "</p><p>" + value.bairro + " " + value.cep + "</p><p>" + value.cidade + "/" + value.uf + "</p><p><a href=unidades/"+value.id+" class='btn btn-primary btn-xs'>Ver Unidade</a></p></div>";


              marker.addListener("click", () => {
                  infoWindow.setContent(contentString);
                  infoWindow.open(map, marker);
                  map.panTo({lat: parseFloat(this.latitude), lng: parseFloat(this.longitude)});
                  

                                    
              });

          });


      }

  window.initMap = initMap;


  </script>

<script>
   const endpoint = "{{ route('api.getUnidadesByState') }}"; // Route URL referenced in the controller function that returns the JSON data
$.ajax({
  method: "GET",
  url: endpoint
}).done(function(response){
  
  const pieData = [];
  const pieLabels = [];
  const randomColor = Math.floor(Math.random()*16777215).toString(16);

  var resultArray = $.map(response, function(value, index) { return [value]; });
    
  console.log(resultArray.sort(sortDps()).reverse());

  $.each(resultArray, function(key, value){
    pieData.push(value.total);
    pieLabels.push(value.uf);
    pieColors = randomColor;
  });





  var pieChart = new Chart(document.getElementById("graficoUF"), {
    type: 'pie',
    data: {
      labels: pieLabels,
      datasets: [{
        
        data: pieData
      }]
    },
    options: {
      responsive: true,
      legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'Chart.js Doughnut Chart'
        },
        
  scales: {}
},
    
  });
});

function sortDps(){
  return function(a, b){
    if(a.total < b.total){
      return -1;
    }else if(a.total > b.total){
      return 1;
    }else{
      return 0;   
    }
  }
}


</script>

@endsection

@section('css')
<style>
  /* 
* Always set the map height explicitly to define the size of the div element
* that contains the map. 
*/
#map {
height: 600px;
}
.infoWindow{
  padding: 20px;
}

</style>
@endsection