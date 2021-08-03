@extends('adminlte::page')
@section('content_header')
    <h1>Listagem de faturamentos</h1>
@stop


@section('content')
	
	<div class="box" style="padding: 5px;">
				<div class="box-header">
					<a class="btn btn-app" href="{{route('faturamento.create')}}">
						<i class="fa fa-plus"></i> Cadastrar
					 </a>
				</div>
				<table id="lista-faturamentos" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>#</th>
				  <th>Cliente</th>
                  <th>Data</th>
				  <th>Total</th>
				  <th>NF</th>
				  <th>Actions</th>
				</tr>
                </thead>
                <tbody>
				@foreach($faturamentos->sortByDesc('id') as $f)

				<tr>
				<td><a href="{{route('faturamento.show',$f->id)}}">{{$f->nome}}</a></td>
				<td>{{$f->empresa->nomeFantasia}}</td>
				<td><span style="display:none;">{{$f->created_at}}</span>{{ \Carbon\Carbon::parse($f->created_at)->format('d/m/Y')}}</td>
				<td>R$ {{number_format($f->valorTotal,2,'.',',')}}</td>
				<td>
					@if(!$f->nf)
					<button type="button" class="btn btn-warning btn-xs cadastroNF" data-toggle="modal" data-target="#cadastroNF" data-ID='{{$f->id}}' data-cliente='{{$f->empresa->nomeFantasia}}' data-nome='{{$f->nome}}'>
						<span class="glyphicon glyphicon-plus-sign"></span> Cadastrar
					  </button>
					@else
					
					<button type="button" class="btn btn-default btn-xs editNF" data-toggle="modal" data-target="#editNF" data-ID='{{$f->id}}' data-cliente='{{$f->empresa->nomeFantasia}}' data-nome='{{$f->nome}}' data-nf="{{$f->nf}}">
						<span class="glyphicon glyphicon-plus-sign"></span> {{$f->nf}}
					  </button>

					@endif

				</td>
				<td><a href="{{route('faturamento.destroy',$f->id)}}" class="confirmation"> <i class="glyphicon glyphicon-trash"></i></a></td>
				</tr>

				@endforeach
                </tbody>
              </table>   
			</div>
			
			
			<div class="modal fade" id="cadastroNF">
				<div class="modal-dialog">
				  <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span></button>
					  <h4 class="modal-title">Cadastrar NF</h4>
					</div>
					<div class="modal-body">
					  
						{!! Form::open(['route'=>'faturamento.addNF']) !!}
						{!! Form::hidden('faturamentoID', null, ['class'=>'form-control','id'=>'faturamentoID']) !!}
						
						<div class="form-group">
						{!! Form::label('faturamentoNome', 'Faturamento', array('class'=>'control-label')) !!}
						{!! Form::text('faturamentoNome', null, ['class'=>'form-control','disabled'=>true,'id'=>'faturamentoNome']) !!}
						</div>
						<div class="form-group">
						{!! Form::label('faturamentoCliente', 'Cliente', array('class'=>'control-label')) !!}
						{!! Form::text('faturamentoCliente', null, ['class'=>'form-control','disabled'=>true,'id'=>'faturamentoCliente']) !!}
						</div>
						<div class="form-group">
						{!! Form::label('nf', 'N.F.', array('class'=>'control-label')) !!}
						{!! Form::text('nf', null, ['class'=>'form-control', 'id'=>'faturamentoNF']) !!}
						</div>

					</div>
					<div class="modal-footer">
					  <button type="button" class="btn pull-left" data-dismiss="modal">Close</button>
					  <button type="submit" class="btn btn-info">Cadastrar</button>
	  
					</div>
					{!! Form::close() !!}
				  </div>
				  <!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			  </div>
			
			  <div class="modal fade" id="editNF">
				<div class="modal-dialog">
				  <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span></button>
					  <h4 class="modal-title">Editar NF</h4>
					</div>
					<div class="modal-body">
					  
						{!! Form::open(['route'=>'faturamento.editNF']) !!}
						{!! Form::hidden('faturamentoID', null, ['class'=>'form-control','id'=>'faturamentoID']) !!}
						
						<div class="form-group">
						{!! Form::label('faturamentoNome', 'Faturamento', array('class'=>'control-label')) !!}
						{!! Form::text('faturamentoNome', null, ['class'=>'form-control','disabled'=>true,'id'=>'faturamentoNome']) !!}
						</div>
						<div class="form-group">
						{!! Form::label('faturamentoCliente', 'Cliente', array('class'=>'control-label')) !!}
						{!! Form::text('faturamentoCliente', null, ['class'=>'form-control','disabled'=>true,'id'=>'faturamentoCliente']) !!}
						</div>
						<div class="form-group">
						{!! Form::label('nf', 'N.F.', array('class'=>'control-label')) !!}
						{!! Form::text('nf', null, ['class'=>'form-control', 'id'=>'faturamentoNF']) !!}
						</div>

					</div>
					<div class="modal-footer">
					  <button type="button" class="btn pull-left" data-dismiss="modal">Close</button>
					  <button type="submit" class="btn btn-info">Editar</button>
	  
					</div>
					{!! Form::close() !!}
				  </div>
				  <!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			  </div>

			  

@endsection



@section('js')


<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
<script>
		$(function () {
		    $('#lista-faturamentos').DataTable({
		      "paging": true,
		      "lengthChange": false,
		      "searching": true,
		      "ordering": true,
		      "info": false,
		      "autoWidth": true,
		       "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }           
  });
$('.confirmation').on('click', function () {
        		return confirm('Você deseja excluir o faturamento?');
    			});
		     
		    });
			
$(document).on("click", ".cadastroNF", function () {

	
     var faturamentoID = $(this).data('id');
	 var faturamentoCliente = $(this).data('cliente');
	 var faturamentoNome = $(this).data('nome');
	


     $(".modal-body #faturamentoID").val( faturamentoID );
	 $(".modal-body #faturamentoCliente").val( faturamentoCliente );
	 $(".modal-body #faturamentoNome").val( faturamentoNome );

	 $(".modal-body #faturamentoNF").val(null);

	 $('#cadastroNF').on('shown.bs.modal', function () {
   	 $('.modal-body #faturamentoNF').focus();
	}); 
    
});			

$(document).on("click", ".editNF", function () {


	 var faturamentoID = $(this).data('id');
	 var faturamentoCliente = $(this).data('cliente');
	 var faturamentoNome = $(this).data('nome');
	 var faturamentoNF = $(this).data('nf');


     $(".modal-body #faturamentoID").val( faturamentoID );
	 $(".modal-body #faturamentoCliente").val( faturamentoCliente );
	 $(".modal-body #faturamentoNome").val( faturamentoNome );
	 $(".modal-body #faturamentoNF").val( faturamentoNF );

	 $('#editNF').on('shown.bs.modal', function () {
   	 $('.modal-body #faturamentoNF').focus();
	}); 
});

			
</script>
  @stop