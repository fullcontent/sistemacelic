@extends('adminlte::page')

@section('content_header')
    <h1>{{$unidade->nomeFantasia}}</h1>
@stop

@section('content')
		
	<div class="col-md-12">
		<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Detalhes da unidade</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
             
             	<div class="col-md-4">
             		<p><b>Status:</b> Ativa</p>
             		<p><b>CNPJ:</b> {{$unidade->cnpj}}</p>
             		<p><b>Ins. Estadual:</b> {{$unidade->inscricaoEst}}</p>
             		<p><b>Insc. Municipal:</b> {{$unidade->inscricaoMun}}</p>
             	</div>
             	<div class="col-md-4">
             		<p><b>Inscrição Imobiliária:</b> {{$unidade->inscricaoImo}}</p>
             		<p><b>Matricula RI:</b> {{$unidade->matriculaRI}}</p>
             		<p><b>Área da Loja:</b> {{$unidade->area}} m2</p>
             		<p><b>Imóvel:</b> {{$unidade->tipoImovel}}</p>
             	</div>
             	<div class="col-md-4">
             		<p><b>Cód:</b> {{$unidade->codigo}}</p>
             		<p><b>Endereço:</b> {{$unidade->endereco}}</p>
             		<p><b>Cidade/UF:</b> {{$unidade->cidade}}/{{$unidade->uf}}</p>
             		<p><b>CEP:</b> {{$unidade->cep}}</p>
             	</div>
             	
                
            </div>
            </div>
           
           <div class="row">
    
  <div class="col-md-6">
    
    <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Serviços</h3>

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
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($unidade->servicos as $servico)
                  <tr>
                    <td>{{$servico->os}}</td>
                    <td>{{$servico->nome}}</td>
                    <td><span class="label label-success">{{$servico->situacao}}</span></td>
                    <td>{{$servico->observacoes}}</td>
                    <td><a href="{{route('servicos.show',$servico->id)}}">Detalhes</a></td>
                  </tr>
                  @endforeach
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="javascript:void(0)" class="btn btn-sm btn-info btn-flat pull-left">Novo Serviço</a>
              <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">Todas os Serviços</a>
            </div>
            <!-- /.box-footer -->
          </div>
  </div>

  <div class="col-md-6">
    
    <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Taxas</h3>

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
                    <th>Nome</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Situacao</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($unidade->taxas->take(2) as $taxa)
                  <tr>
                    <td>{{$taxa->nome}}</td>
                    <td>R$ {{$taxa->valor}}</td>
                    <td><span class="label label-success">{{ \Carbon\Carbon::parse($taxa->vencimento)->format('d/m/Y')}}
</span></td>
                    <td>{{$taxa->situacao}}</td>
                  </tr>
                  @endforeach
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">Todas as Taxas</a>
            </div>
            <!-- /.box-footer -->
          </div>
    
  </div>
  
   </div>


          </div>

 
@endsection