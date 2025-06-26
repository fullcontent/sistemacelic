@extends('adminlte::page')
@section('content_header')
    <h1>Ordens de Compra</h1>
@stop


@section('content')
	
	<div class="box">

	    <table id="prestadores" class="table table-bordered table-hover text-center">
	        <thead>
	            <tr>
	                <th>O.C.</th>
	                <th>Prestador</th>
	                <th>O.S.</th>
	                <th>Escopo</th>
	                <th>Valor</th>
	                <th>Forma de Pagamento</th>
	                <th>Situação</th>
	                <th>Avaliação</th>

	            </tr>
	        </thead>

	        <tbody>
	            @foreach($ordensCompra as $oc)
	            <tr>
	                <td><a href="{{route('ordemCompra.edit',$oc->id)}}" class="link">#{{$oc->id}}</a> </td>
	                <td>{{$oc->prestador->nome}}</td>
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
	                    <span class="btn btn-warning">Em aberto
	                        {{$oc->pagamentos->where('situacao','pago')->count()}}/{{$oc->pagamentos->count()}}</span>

	                    @else
	                    <span class="btn btn-success">Pago</span>

	                    @endif</td>
	                <td class="text-center">

	                    @if(count($oc->rating))


	                    <span class="pull-right">({{$oc->rating->median('rating')}})</span>
	                    <div class="Stars" style="--rating: {{$oc->rating->median('rating')}};"></div>

	                    <p><a href="#" class="btn btn-xs btn-default" data-target="#modal-avaliacoes" data-toggle="modal"
	                            id="rates-show" data-ordemcompra="{{$oc->id}}"
	                            data-prestador_id="{{$oc->prestador_id}}">ver avaliações ({{$oc->rating->count()}})</a></p>
	                    @else
	                    <button class="btn btn-xs btn-info" id="rate" data-ordemcompra="{{$oc->id}}"
	                        data-prestador_id="{{$oc->prestador_id}}" data-target="#modal-rate"
	                        data-toggle="modal">Avaliar</button>
	                    @endif
	                </td>
	            </tr>
	            @endforeach
	        </tbody>


	    </table>

	</div>
	 		
            
            
            
            
            <div class="modal fade in" id="modal-avaliacoes">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Avaliações</h4>
                        </div>
                        <div class="modal-body">

                            <ul class="products-list product-list-in-box">



                            </ul>

                            <button class="btn btn-xs btn-info" id="rate" data-ordemcompra="{{$oc->id}}"
                                data-prestador_id="{{$oc->prestador_id}}" data-target="#modal-rate"
                                data-toggle="modal">Nova Avaliação</button>

                        </div>

                    </div>

                </div>
            </div>

                <div class="modal fade in" id="modal-rate">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span></button>
                                <h4 class="modal-title">Avaliar prestador</h4>
                            </div>
                            <div class="modal-body">


                                {!! Form::open(['route'=>'prestador.rate', 'id'=>'prestadorRate']) !!}

                                    @include('admin.prestadores.form-prestadorComentario')

                                <button type="submit" class="btn btn-primary" id="sendRate">Avaliar</button>
                                {!! Form::close() !!}

                            </div>

                        </div>

                    </div>

                </div>

            

@endsection


@section('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
<script>
 
 $(document).on("click", "#rate", function () {

var ordemCompra_id = $(this).data('ordemcompra');
var prestador_id = $(this).data('prestador_id');
var user_id = {{Auth::id()}};

$("#ordemCompra_id").val(ordemCompra_id);
$("#prestador_id").val(prestador_id);
$("#user_id").val(user_id);


});


$(document).on("click", "#rates-show", function () {

    var ordemCompra_id = $(this).data('ordemcompra');
    var prestador_id = $(this).data('prestador_id');
    var user_id = {{Auth::id()}};

    $.ajax({
        url: "{{ route('prestador.ratings','" + prestador_id + "')}}",
        type: "GET",
        data: {
            ordemCompra_id: ordemCompra_id,
            prestador_id: prestador_id,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            $.each(response, function (key, value) {
                $(".modal-body .products-list").append("<li class=item><div class=product-info><a href=javascript:void(0) class=product-title>"+this.user.name+"<span class='btn btn-xs btn-success'>"+this.ordem_compra.servico_principal.id+"</span><span class=label label-warning pull-right><div class=Stars style=--rating:"+this.rating+";></div></span></a><span class=product-description>"+this.comentario+"</span></div></li>");
                
            });
        },

        error: function (xhr, status, error) {
            // Handle error response here
        }
    });

});

$(document).click(function(e) {
    if ($(e.target).closest('#modal-avaliacoes').length) {
        
        $('#modal-avaliacoes .modal-body .products-list').empty();

    }
});


</script>

@endsection
