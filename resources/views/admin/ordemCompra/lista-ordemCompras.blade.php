@extends('adminlte::page')
@section('content_header')
    <h1>Ordens de Compra</h1>
@stop


@section('content')
	
	<div class="box">
				<div class="box-header">
					
				</div>
				<table id="prestadores" class="table table-bordered table-hover">
					<thead>
						<tr>
                            <th>O.C.</th>
                            <th>O.S.</th>
                            <th>Escopo</th>
                            <th>Valor</th>
                            <th>Forma de Pagamento</th>
                            <th>Situação</th>
                                                    
						</tr>
					</thead>

                    <tbody>
                        @foreach($ordensCompra as $oc)
                        <tr>
                            <td><a href="{{route('ordemCompra.edit',$oc->id)}}" class="link">#{{$oc->id}}</a> </td>
                            <td>{{$oc->servicoPrincipal->os}} | {{$oc->servicoPrincipal->nome}}</td>
                            <td>{{$oc->escopo}}</td>
                            <td>R${{$oc->valorServico}}</td>
                            <td>
                                @if($oc->formaPagamento == 1)
                                à vista
                                @else
                                {{$oc->formaPagamento}}x
                                @endif
                            </td>
                            <td>
                                @if($oc->situacaoPagamento->count())
                                <span class="btn btn-warning">Em aberto {{$oc->pagamentos->where('situacao','pago')->count()}}/{{$oc->pagamentos->count()}}</span>
                                
                                @else
                                <span class="btn btn-success">Pago</span>
                                
                                @endif</td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>

                    
				  </table>
				  
			</div>
	 		

@endsection


