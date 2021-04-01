@extends('adminlte::page')

@section('content')

<div class="col-md-4">
<div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Situacao atual</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
              <canvas id="situacaoAtual" width="400" height="400"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
</div>



<div class="col-md-4">
<div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Serviços Finalizados</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
              <canvas id="servicosFinalizados" width="400" height="400"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
</div>

<div class="col-md-4">
<div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Evolucao Mensal</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
              <canvas id="evolucaoMensal" width="400" height="400"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
</div>



@endsection




@section('js')
<script src="https://cdnjs.com/libraries/Chart.js"></script>
<script>



var ctx = document.getElementById('evolucaoMensal').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho'],
        datasets: [{
            label: 'Licenças emitidas',
            data: [
                
               23,
               45,
               65,
               12,
               45,
               24
            ],
            backgroundColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 99, 132, 1)',
            ]
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


var ctx = document.getElementById('servicosFinalizados').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Janeiro/2020', 'Fevereiro/2020', 'Março/2020', 'Abril/2020', 'Maio/2020', 'Junho/2020'],
        datasets: [{
            label: 'Licenças',
            data: [
                
               10,
               12,
               18,
               26,
               23,
               18
            ],
            backgroundColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 99, 132, 1)',
            ]
            },
            {
            label: 'Outros',
            data: [
                
               5,
               3,
               14,
               10,
               10,
               9
            ],
            backgroundColor: [
                'rgba(54, 162, 235, 0.9)',
                'rgba(54, 162, 235, 0.9)',
                'rgba(54, 162, 235, 0.9)',
                'rgba(54, 162, 235, 0.9)',
                'rgba(54, 162, 235, 0.9)',
                'rgba(54, 162, 235, 0.9)',
            ]
            }],
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


// For a pie chart
new Chart(document.getElementById("situacaoAtual"),{"type":"doughnut","data":{"labels":["Vigentes","Vencidos"],"datasets":[{"label":"My First Dataset","data":[65,12],"backgroundColor":["rgb(54, 162, 235)","rgb(255, 99, 132)"]}]}});
	
</script>
@stop