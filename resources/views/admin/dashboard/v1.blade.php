@extends('adminlte::page')

@section('content')





    <div class="row">
        <div class="col-md-6">
            <canvas id="licencasEmitidasMes"></canvas>
        </div>

        <div class="col-md-6">
            
                <canvas id="chart-container"></canvas>
           
        </div>
    </div>



@endsection


@section('js')

<script>
var ctx = document.getElementById('licencasEmitidasMes').getContext('2d');
var barChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [], // array of months in Brazilian Portuguese
        datasets: [{
            label: 'Licenças Emitidas por mês',
            data: [], // array of total_count values
            backgroundColor: [], // array of background colors for each year
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});

$.ajax({
    url: '{{ route("getLicencasEmitidasMes") }}',
    type: 'GET',
    dataType: 'json',
    success: function (data) {
        var monthsInPortuguese = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        var backgroundColors = ['#00a65a'];

        data.forEach(function (item) {
            var date = item.month.split('-');
            var monthIndex = parseInt(date[0]) - 1;
            var year = date[1];

            barChart.data.labels.push(monthsInPortuguese[monthIndex] + ' ' + year);
            barChart.data.datasets[0].data.push(item.total_count);
            barChart.data.datasets[0].backgroundColor.push(backgroundColors[year % 1]);
        });

        barChart.update();
    }
});
</script>




<script>
$(document).ready(function() {
  // This function is executed when the document is ready
  $.ajax({
    // Perform an AJAX request to the specified URL
    url: '{{route("usersMoreActive")}}',
    type: 'GET',
    success: function (data) {
      // This function is executed if the AJAX request succeeds
      // Process the data to extract user_name and percentage
      var labels = [];
      var values = [];
      for (var i = 0; i < data.length; i++) {
        labels.push(data[i].user_name);
        values.push(data[i].percentage);
      }

      // Create the chart
      var ctx = document.getElementById('chart-container').getContext('2d');

      // Initialize a new chart using the Chart.js library
      var chart = new Chart(ctx, {
        type: 'horizontalBar',
        data: {
          labels: labels,
          datasets: [{
            axis: 'y',
            label: 'Usuários mais ativos do sistema',
            data: values,
            fill: false,
            backgroundColor: '#00a65a',
            borderColor: 'rgb(54, 162, 235)',
            borderWidth: 0
          }]
        },
        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true
              }
            }],
            xAxes: [{
              display: false,
              ticks: {
                callback: function(value, index, values) {
                  return value + '%';
                }
              }
            }]
          }
        }
      });
    }
  });
});
</script>



@endsection