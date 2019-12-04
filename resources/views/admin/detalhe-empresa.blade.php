@extends('adminlte::page')

@section('content_header')
    <h1>Nome da Loja</h1>
@stop

@section('content')
		
	<div class="col-md-12">
		<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Detalhes da empresa</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
             
             	<div class="col-md-4">
             		<p><b>Status:</b> </p>
             		<p><b>CNPJ:</b> {{$empresa->cnpj}}</p>
             		<p><b>Ins. Estadual:</b> {{$empresa->inscricaoEst}}</p>
             		<p><b>Insc. Municipal:</b> {{$empresa->inscricaoMun}}</p>
             	</div>
             	<div class="col-md-4">
             		<p><b>Inscrição Imobiliária:</b> {{$empresa->inscricaoImobiliaria}}</p>
             		<p><b>Matricula RI:</b> </p>
             		<p><b>Área da Loja:</b> </p>
             		<p><b>Imóvel:</b> </p>
             	</div>
             	<div class="col-md-4">
             		<p><b>Cód:</b> {{$empresa->codigo}}</p>
             		<p><b>Endereço:</b> {{$empresa->endereco}}</p>
             		<p><b>Cidade/UF:</b> {{$empresa->cidade}}/{{$empresa->uf}}</p>
             		<p><b>CEP:</b> {{$empresa->cep}}</p>
             	</div>
             	
                
            </div>
            </div>
           
          </div>
	<div class="col-md-8">
		
		<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Demandas</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>OS</th>
                    <th>Serviço</th>
                    <th>Status</th>
                    <th>Obs</th>
                  </tr>
                  </thead>
                  <tbody>
                  	@foreach($empresa->servicos as $servico)
                  <tr>
                    <td>{{$servico->os}}</td>
                    <td>{{$servico->nome}}</td>
                    <td><span class="label label-success">{{$servico->situacao}}</span></td>
                    <td>{{$servico->observacoes}}</td>
                  </tr>
                  @endforeach
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="javascript:void(0)" class="btn btn-sm btn-info btn-flat pull-left">Nova Demanda</a>
              <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">Todas as Demandas</a>
            </div>
            <!-- /.box-footer -->
          </div>
	</div>
	
	
@endsection