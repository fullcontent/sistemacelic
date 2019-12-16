@extends('adminlte::page')




@section('content')
	

	<h3>Todas as ordens</h3>
	
	<div class="row">
		
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>150</h3>

              <p>Total de O.S.s</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">Mais informações <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>53<sup style="font-size: 20px">%</sup></h3>

              <p>Finalizadas</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">Mais informações <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
               <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>65</h3>

              <p>Em andamento</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">Mais informações <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>65</h3>

              <p>Em andamento</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">Mais informações <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>


     <div class="row">
     		<div class="col-md-6">
     			<div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Ordens de serviço</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table class="table table-bordered">
                <tbody>

                <tr>
                  <th>O.S.</th>
                  <th>Nome</th>
                  <th>Situação</th>
                  <th></th>
                </tr>
				@foreach($servicos as $servico)
                <tr>
                	<td>{{$servico->os}}</td>
                	<td>{{$servico->nome}}</td>
                	<td>{{$servico->situacao}}</td>
                	<td></td>
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
              <h3 class="box-title">Suas empresas</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table class="table table-bordered">
                <tbody>

                <tr>
                  <th>Nome</th>
                  <th>CNPJ</th>
                  
                </tr>
                
              </tbody></table>
            </div>
            <!-- /.box-body -->
            
          </div>
     		</div>
     	</div>	
     

@endsection




