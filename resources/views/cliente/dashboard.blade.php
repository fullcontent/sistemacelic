@extends('adminlte::page')




@section('content')
	

	
	
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
            <a href="{{route('cliente.servico.andamento')}}" class="small-box-footer">Mais informações <i class="fa fa-arrow-circle-right"></i></a>
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
            <a href="{{route('cliente.servico.finalizado')}}" class="small-box-footer">Mais informações <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
         
      </div>

      @if(count($pendencias))

      <div class="row">
        <div class="col-lg-12 col-xs-12">
            
            <div class="box box-warning">
                <div class="box-header with-border">
                  <h3 class="box-title">Pendências</h3>
    
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="table-responsive">
                    <table id="lista-pendencias" class="table table-bordered table-hover">
                      <thead>
                      <tr>
                        <th>Cod.</th>
                        <th>Unidade</th>
                        <th>Serviço</th>
                        <th>Pendência</th>
                        <th>Data</th>
                      </tr>
                      </thead>
                      <tbody>
                        @foreach($pendencias->where('status','pendente') as $p)
                      <tr>
                        <td><a href="{{route('cliente.servico.show',$p->servico_id)}}">{{$p->servico['unidade']['codigo']}}</a></td>
                        <td><a href="{{route('cliente.servico.show',$p->servico_id)}}">{{$p->servico['unidade']['nomeFantasia']}}</a></td>
                        <td><a href="{{route('cliente.servico.show',$p->servico_id)}}">{{$p->servico['nome']}}</a></td>
                        <td><a href="{{route('cliente.servico.show',$p->servico_id)}}">{{$p->pendencia}}</a></td>
                        <td><a href="{{route('cliente.servico.show',$p->servico_id)}}">{{\Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</a></td>
                      </tr>
                        @endforeach
                      
                      </tbody>
                    </table>
                  </div>
                  <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                  
                </div>
                <!-- /.box-footer -->
              </div>
    
        </div>
    
    </div>
    @endif
    
    

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
                	<td><a href="{{route('cliente.servico.show',$servico->id)}}">{{$servico->nome}}</a></td>
                  <td>{{\Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y')}}</td>
                  
                </tr>
                @endforeach
                
              </tbody></table>
            </div>
            <!-- /.box-body -->
            
          </div>
     		</div>

        @if(count($servicos->where('tipo','secundario')->where('situacao','andamento')))
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
                  <td><a href="{{route('cliente.servico.show',$servico->id)}}">{{$servico->nome}}</a></td>
                  
                  
                </tr>
                @endforeach
                
              </tbody></table>
            </div>
            <!-- /.box-body -->
            
          </div>
        </div>
     	</div>	
    @endif 

    

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