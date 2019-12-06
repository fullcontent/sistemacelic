@extends('adminlte::page')

@section('content_header')
    <h1></h1>
@stop



@section('content')


<div class="row">
    
</div>

<div class="col-md-12">
		<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">{{$dados->nomeFantasia}}</h3>

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
             		<p><b>CNPJ:</b> {{$dados->cnpj}}</p>
             		<p><b>Ins. Estadual:</b> {{$dados->inscricaoEst}}</p>
             		<p><b>Insc. Municipal:</b> {{$dados->inscricaoMun}}</p>
             	</div>
             	<div class="col-md-4">
             		<p><b>Inscrição Imobiliária:</b> {{$dados->inscricaoImo}}</p>
             		<p><b>Matricula RI:</b> {{$dados->matriculaRI}}</p>
             		<p><b>Área da Loja:</b> {{$dados->area}} m2</p>
             		<p><b>Imóvel:</b> {{$dados->tipoImovel}}</p>
             	</div>
             	<div class="col-md-4">
             		<p><b>Cód:</b> {{$dados->codigo}}</p>
             		<p><b>Endereço:</b> {{$dados->endereco}}</p>
             		<p><b>Cidade/UF:</b> {{$dados->cidade}}/{{$dados->uf}}</p>
             		<p><b>CEP:</b> {{$dados->cep}}</p>
             	</div>
             	
                
            </div>
            </div>
</div>
 
<div class="col-md-8">
    
    <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Detalhes do serviço {{$servico->os}}</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                
                <div class="col-sm-6">
                  <p><b>Nome: </b>{{$servico->nome}}</p>
                  
                  <p><b>Emissão: </b>{{\Carbon\Carbon::parse($servico->protocoloEmissao)->format('d/m/Y')}}</p>
                  <p><b>Número Protocolo: </b>{{$servico->anexo}} <button type="button" class="btn btn-primary btn-xs">Ver</button></p>
                  <p><b>Data Protocolo: </b>{{\Carbon\Carbon::parse($servico->protocoloEmissao)->format('d/m/Y')}}</p>

                </div>
                
                <div class="col-sm-6">
                    
                  <p><b>Início do processo: </b></p>
                  <p><b>Última cobrança: </b></p>
                  <p><b>Próxima cobrança: </b></p>
                </div>

              


            </div>
            <!-- /.box-body -->
           
          </div>
</div>

<div class="row">
    <div class="col-sm-12">
      <div class="box box-black">
            <div class="box-header with-border">
              <h3 class="box-title">Interações</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                        
                <ul class="timeline timeline-inverse">
                                 
                  
                @foreach($servico->historico->take(5) as $historico)
                  <!-- timeline item -->
                    <li>
                    <i class="fa fa-user bg-aqua"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> {{\Carbon\Carbon::parse($historico->created_at)->diffForHumans()}}</span>

                      <h3 class="timeline-header no-border">
                        {{$historico->observacoes}}
                      </h3>
                    </div>
                  </li>
                  <!-- END timeline item -->
                @endforeach
                  
                  
                  <li>
                    <i class="fa fa-clock-o bg-gray"></i>
                  </li>
                </ul>

            </div>
  </div>


  </div> 

</div>

@endsection

