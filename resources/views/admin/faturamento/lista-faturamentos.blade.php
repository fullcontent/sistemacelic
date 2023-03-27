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
				  
				  <th>Actions</th>
				  <th></th>
				</tr>
                </thead>
                <tbody>
				@foreach($faturamentos->sortByDesc('id') as $f)

				<tr>
				    <td><a href="{{route('faturamento.show',$f->id)}}">{{$f->nome}}</a></td>
				    <td>{{$f->empresa->nomeFantasia}}</td>
				    <td><span
				            style="display:none;">{{$f->created_at}}</span>{{ \Carbon\Carbon::parse($f->created_at)->format('d/m/Y')}}
				    </td>
				    <td>R$ {{number_format($f->valorTotal,2,'.',',')}}</td>

				    <td style="display:none">{{$f->servicos}}{{$f->obs}}</td>
				    <td><a href="{{route('faturamento.destroy',$f->id)}}" class="confirmation"> <i
				                class="glyphicon glyphicon-trash"></i></a>
				        <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal"
				            data-faturamento_id="{{ $f->id }}" data-dados_id="{{ $f->dadosCastro_id}}">
				            Alterar CNPJ
				        </button>
				    </td>
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

				<!-- The modal -->
<div class="modal fade" id="myModal">
	<div class="modal-dialog">
	  <div class="modal-content">
	  
		<!-- Modal Header -->
		<div class="modal-header">
		  <h4 class="modal-title">Select a Company</h4>
		  <button type="button" class="close" data-dismiss="modal">&times;</button>
		</div>
		
		<!-- Modal body -->
		<div class="modal-body">
		  <form id="company-select-form">
			@csrf
			<select name="dadosCastro_id" class="form-control"></select>

		  </form>
		</div>
		
		<!-- Modal footer -->
		<div class="modal-footer">
		  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		  <button type="button" class="btn btn-primary save-selected-item" data-dismiss="modal">Save</button>
		</div>
		
	  </div>
	</div>
  </div>	
			  

@endsection



@section('js')


<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
<script>
		$(function () {
		    $('#lista-faturamentos').DataTable({
		      "paging": true,
		      "lengthChange": true,
			  "pageLength": 100,
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

<script>
	$(document).ready(function () {
	    $('#myModal').on('show.bs.modal', function (event) {
	        var button = $(event.relatedTarget); // Button that triggered the modal
	        var faturamento_id = button.data('faturamento_id'); // Extract info from data-* attributes
	        var dados_id = button.data('dados_id')
	        var modal = $(this);

	        // Make AJAX call to get the list of companies
	        $.get('/api/getDadosCastro', function (data) {
	            // Populate the select element with the received data
	            var select = modal.find('#company-select-form select');
	            select.empty();
	            for (var i = 0; i < data.length; i++) {
	                var option = $('<option></option>');
	                option.attr('value', data[i].id);
	                option.text(data[i].razaoSocial);
	                if (data[i].id == dados_id) {
	                    option.attr('selected', 'selected');
	                }
	                select.append(option);
	            }
	        });

	        var hiddenInput = $("<input>").attr({
	            type: "hidden",
	            name: "faturamento_id",
	            value: faturamento_id
	        });
	        $("#company-select-form").append(hiddenInput);



	    });

	    // When the Save button is clicked, send an AJAX request to save the selected company
	    $('.modal-footer .save-selected-item').click(function () {
	        var form = $('#company-select-form');
	        var data = form.serialize();
	        var url = '/api/saveDadosCastro/';

	        $.get(url, data, function (response) {
	            console.log(data);
	        });
	    });
	});
	</script>
  @stop