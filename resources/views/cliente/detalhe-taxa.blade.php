@extends('adminlte::page')

@section('content_header')
    <h1>Detalhes da taxa</h1>
@endsection



@section('content')

<div class="box box-info">
	
	<!-- /.box-header -->
	<div class="box-body">
		
		<div class="col-md-6">
			
            <p><b>Taxa: </b>{{$taxa->nome}}</p>
            <p><b>OS: </b>{{$taxa->servico->os}}</p>
            
            <p><b>Valor: </b>R$ {{$taxa->valor}}</p>
            <p><b>Vencimento: </b>@switch($taxa->vencimento)

                @case($taxa->vencimento >= date('Y-m-d'))
                    @if($taxa->comprovante)
                      <span class="label label-success">Pago</span>
                    @else
                    
                    <span class="label label-warning">Aberto</span>
                  @endif
                  
                @break
              
                @case($taxa->vencimento < date('Y-m-d'))
                    @if($taxa->comprovante)
                        <span class="label label-success">Pago</span>
                    @else
                    <span class="label label-danger">Vencida</span>
                    @endif
                @break
              @endswitch</p>

            @if($taxa->pagamento > '0000-00-00')
              <p><b>Data de pagamento: </b>{{\Carbon\Carbon::parse($taxa->pagamento)->format('d/m/Y')}}</p>
            @endif

            @if($taxa->observacoes)
            <p><b>Observações</b>{{$taxa->observacoes}}</p>
            @endif
		</div>
		<div class="col-md-6">
            <p>
			@unless ( empty($taxa->boleto) )
               <a href="{{ url("storage/$taxa->boleto") }}" class="btn btn-warning" target="_blank">Ver Boleto</a>
            @endunless
        </p>

        <p>
            @if(empty($taxa->comprovante))
        @unless ( empty($taxa->boleto))
        
        <a href="{{ url("uploads/$taxa->boleto") }}" class="btn btn-warning" target="_blank">Ver Boleto</a>
        @endunless
        @endif
    </p>
        <p>
        @unless ( empty($taxa->comprovante) )
        
        <a href="{{ url("uploads/$taxa->comprovante") }}" class="btn btn-success" target="_blank">Ver Comprovante</a>
        @endunless
    </p>
        

		</div>
		
		
		
  </div>
  
</div>

<a href="{{route('cliente.servico.show', $taxa->servico_id)}}" class="btn btn-default">Voltar</a>

@endsection