@extends('adminlte::page')




@section('content')
	

	<h3>Todas as ordens</h3>
	
	<div class="row">
		
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>{{count($servicos->where('situacao','andamento'))}}</h3>

              <p>Serviços em andamento</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{url('cliente/servicos')}}" class="small-box-footer">Mais informações <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>{{count($servicos->where('situacao','finalizado'))}}</h3>

              <p>Serviços finalizados</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{url('cliente/servicos')}}" class="small-box-footer">Mais informações <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
         
      </div>


     <div class="row">
     		<div class="col-md-6">
     			<div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Serviços primários - andamento</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="servicos-primario" class="table table-bordered" >
                
              <thead>
                
                <tr>
                  <th>Código</th>
                  <th>Serviço</th>

                  <th>Vencimento</th>
                  
                </tr>
              </thead>
                

                <tbody>
				@foreach($servicos->where('tipo','=','primario')->where('situacao','=','andamento') as $servico)

                <tr>
                	<td>{{$servico->unidade->codigo}}</td>
                	<td><a href="{{route('servico.show',$servico->id)}}">{{$servico->nome}}</a></td>
                  <td>{{\Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y')}}</td>
                  
                </tr>
                @endforeach
                
              </tbody></table>
            </div>
            <!-- /.box-body -->
            
          </div>
     		</div>
     		<div class="col-md-6">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Serviços secundários - andamento</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table class="table table-bordered" id="servicos-secundario">
               
               <thead>
                  <tr>
                  <th>O.S.</th>
                  <th>Nome</th>
                  
                  
                </tr>
               </thead> 

               
        <tbody>
        @foreach($servicos->where('tipo','secundario')->where('situacao','andamento') as $servico)

                <tr>
                  <td>{{$servico->os}}</td>
                  <td><a href="{{route('servico.show',$servico->id)}}">{{$servico->nome}}</a></td>
                  
                  
                </tr>
                @endforeach
                
              </tbody></table>
            </div>
            <!-- /.box-body -->
            
          </div>
        </div>
     	</div>	
     

@endsection



@section('js')

<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
<script>

  

    $(function () {
        
        $('#servicos-primario').DataTable({
          "paging": true,
          "lengthChange": false,
          "searching": true,
          "ordering": true,
          "info": false,
          "autoWidth": false,
           "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }


        });

        $('#servicos-secundario').DataTable({
          "paging": true,
          "lengthChange": false,
          "searching": true,
          "ordering": true,
          "info": false,
          "autoWidth": false,
           "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }


        });
  });


    </script>
  @stop