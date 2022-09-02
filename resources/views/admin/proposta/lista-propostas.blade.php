@extends('adminlte::page')
@section('content_header')
    <h1>Listagem de propostas</h1>
@stop


@section('content')
	
	<div class="box" style="padding: 5px;">
				<div class="box-header">
					<a class="btn btn-app" href="{{route('proposta.create')}}">
						<i class="fa fa-plus"></i> Cadastrar
					 </a>
				</div>
				<table id="lista-propostas" class="table table-bordered table-hover" data-page-length="25"> 
                <thead>
                <tr>
                  <th>#</th>
				  <th>Empresa</th>
				  <th>Código</th>
				  <th>Unidade</th>
				  
				  <th>Total</th>
				  <th>Status</th>
				  <th>Faturamento</th>
				  <th></th>			
				</tr>
                </thead>
                <tbody>
				@foreach($propostas as $p)

				<tr>
					<td><a href="{{route('proposta.edit',$p->id)}}">#{{$p->id}}</a> </td>
					<td>{{$p->empresa->nomeFantasia}}</td>
					<td>{{$p->unidade->codigo}}</td>
					<td>{{$p->unidade->nomeFantasia}}</td>
					
					<td>R$ {{number_format($p->servicos->sum('valor'),2)}}</td>
					<td>
						@if($p->status == 'Revisando')
							<a href="#" class="btn btn-default btn-xs status" data-id="{{$p->id}}">{{$p->status}}</a> 
							<a href="#" data-id="{{$p->id}}" class="btn btn-info btn-xs analisar"><i class="glyphicon glyphicon-send"></i></a>

						@elseif($p->status == 'Em análise')
							<a href="#" class="btn btn-info btn-xs">Em análise</a>

						@elseif($p->status == 'Aprovada')
							<a href="#" class="btn btn-success btn-xs">Aprovada</a>
						
						@elseif($p->status == 'Recusada')
							<a href="#" class="btn btn-danger btn-xs">Recusada</a>
						@elseif($p->status == 'Arquivada')
							<a href="#" class="btn btn-default btn-xs">Arquivada</a>
						
						@endif
					</td>
					<td>
					@switch(count($p->servicosFaturados))
						@case(0)
							<span class="btn btn-default btn-xs">Em aberto</span>
						@break

						@case(count($p->servicosFaturados) < count($p->servicosCriados))
						<span class="btn btn-success btn-xs">Parcial</span>
						@break

						@case(count($p->servicosFaturados) == count($p->servicosCriados))
						<span class="btn btn-success btn-xs">Faturado</span>
						@break

					@endswitch
					</td>
					<td>
					<a href="{{route('propostaPDF', $p->id)}}" class="btn btn-info btn-xs" target="_blank"> <i class="glyphicon glyphicon-file"></i>PDF</a>
						@if($p->status == 'Em análise')
						<a href="" class="btn btn-success btn-xs aprovar" data-id="{{$p->id ?? ''}}"><i class="fa fa-check"></i> Aprovar</a>
						@endif

						@if($p->status == 'Em análise')
						<a href="" class="btn btn-warning btn-xs recusar" data-id="{{$p->id}}"><i class="glyphicon glyphicon-thumbs-down"></i></a>
						@endif

						@if($p->status != "Arquivada")
						<a href="{{route('removerProposta', $p->id)}}" class="btn btn-danger btn-xs confirmation" data-id="{{$p->id}}"> <i class="glyphicon glyphicon-trash"></i></a>
						
						@endif
				</td>
				</tr>
				
				@endforeach
                </tbody>
              </table>   
			</div>
			
			
			
			
			 

			  

@endsection



@section('js')


<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
<script>
		$(function () {
		    $('#lista-propostas').DataTable({
		      "paging": true,
		      "lengthChange": true,
			  
		      "searching": true,
		      "ordering": true,
		      "info": false,
		      "autoWidth": true,
		       "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }           
  });
  $('.confirmation').on('click', function (e) {
						
						var currentRow=$(this).closest("tr");
						var proposta_id = $(this).data('id');
						var status=currentRow.find("td:eq(3)");
						e.preventDefault();  
        				if (confirm('Você deseja excluir a proposta?')) {
        				   
        				    $.ajax({
        				        type: "GET",
        				        url: "/admin/proposta/remover/" + proposta_id + "",
        				        data: {
        				            id: proposta_id,
        				            _token: CSRF_TOKEN
        				        },
        				        success: function (data) {
        				            console.log("Removido"+proposta_id+"")
									$('a[data-id='+proposta_id+']').remove();
									status.empty();
									status.append('<a href="#" class="btn btn-default btn-xs">Arquivada</a>');

        				            

        				        },
        				        error: function (result) {
        				            console.log(result)
        				        }
        				    });
        				};


    			});
		     
		    });

var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');			
			
$(function() {
    $('.analisar').click(function(e) {   
		e.preventDefault();     
		
        var proposta_id = $(this).data('id');
		var currentRow=$(this).closest("tr");
		var status=currentRow.find("td:eq(3)");

		var actions=currentRow.find("td:eq(5)");
         
		console.log(status);

        $.ajax({
            type: "GET",
            url: "/admin/proposta/analisar/"+proposta_id+"",
            data: {
				id: proposta_id,
				_token: CSRF_TOKEN
				},
            success: function(data){
              console.log(data.success)
			  
			  $('a[data-id='+proposta_id+']').remove();

			  status.append('<a href="#" class="btn btn-info btn-xs">Em análise</a>');
			  actions.prepend('<a href="" class="btn btn-success btn-xs aprovar" data-id="'+proposta_id+'"><i class="fa fa-check"></i> Aprovar</a> <a href="" class="btn btn-warning btn-xs recusar" data-id="'+proposta_id+'"><i class="glyphicon glyphicon-thumbs-down"></i></a>');
			
            },
			error: function (result) {
                console.log(result)
            }
        });
    })
  })		

$(function() {
    $('.aprovar').click(function(e) {        
		
		e.preventDefault();
		
		if(confirm("Gostaria de criar os serviços automaticamente?")){
        	var s = 1;
		}
		else{
			var s = 0;
		}

        var proposta_id = $(this).data('id');
		var currentRow=$(this).closest("tr");
		var status=currentRow.find("td:eq(3)"); 
		var actions=currentRow.find("td:eq(5)");
         
		// console.log(status);

        $.ajax({
            type: "GET",
            url: "/admin/proposta/aprovar/"+proposta_id+"/"+s+"",
			datatType : 'JSON',
            data: {
				id: proposta_id,
				_token: CSRF_TOKEN,
				s: s,
				},
            success: function(data){
              
				console.log(data)		  
			  
			  status.empty();
			  status.append('<a href="#" class="btn btn-success btn-xs">Aprovada</a>');
			  actions.empty();
			  actions.append('<a href="{{route("removerProposta", $p->id)}}" class="btn btn-danger btn-xs confirmation"> <i class="glyphicon glyphicon-trash"></i></a>');

			 


            },
			error: function (result) {
                console.log(result)
            }
        });
    })
  })	

  $(function() {
    $('.recusar').click(function(e) {        
		
		e.preventDefault(); 


        var proposta_id = $(this).data('id');
		var currentRow=$(this).closest("tr");
		var status=currentRow.find("td:eq(3)");
		var actions=currentRow.find("td:eq(5)"); 
         
		console.log(status);

        $.ajax({
            type: "GET",
            url: "/admin/proposta/recusar/"+proposta_id+"",
            data: {
				id: proposta_id,
				_token: CSRF_TOKEN
				},
            success: function(data){

              console.log(data.success)
			  	  
			  status.empty();
			  status.append('<a href="#" class="btn btn-danger btn-xs">Recusada</a>'); 
			  
			  actions.empty();
			  actions.append('<a href="{{route("removerProposta", $p->id)}}" class="btn btn-danger btn-xs confirmation"> <i class="glyphicon glyphicon-trash"></i></a>')

            },
			error: function (result) {
                console.log(result)
            }
        });
    })
  })	
			
</script>
  @stop