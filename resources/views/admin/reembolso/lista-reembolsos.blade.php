@extends('adminlte::page')
@section('content_header')
    <h1>Listagem de reembolsos</h1>
@stop


@section('content')
	
	<div class="box" style="padding: 5px;">
				<div class="box-header">
					<a class="btn btn-app" href="{{route('reembolso.create')}}">
						<i class="fa fa-plus"></i> Cadastrar
					 </a>
				</div>
				<table id="lista-reembolsos" class="table table-bordered table-hover">
                <thead>
                <tr>
				  <th>ID</th>
                  <th>Obs</th>
				  <th>Cliente</th>
                  <th>Data</th>
				  <th>Total</th>
				  <th>Actions</th>
				</tr>
                </thead>
                <tbody>
				@foreach($reembolsos as $r)
				@php
				$controller = new \App\Http\Controllers\ReembolsoController;
				
				@endphp
				<tr>
					<td><a href="{{route('reembolso.show',$r->id)}}">{{$controller->fillWithZeros($r->id)}}</a></td>
					<td><a href="{{route('reembolso.show',$r->id)}}">{{$r->nome}}</a></td>
					<td>{{$r->empresa->nomeFantasia}}</td>
					<td><span style="display:none;">{{$r->created_at}}</span>{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y')}}</td>
					<td>R$ {{number_format($r->valorTotal,2,'.',',')}}</td>
					<td>
						<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal" data-reembolso_id="{{ $r->id }}" data-dados_id="{{ $r->dadosCastro_id}}">
							Alterar CNPJ
						  </button>			
					
							<a href="{{route('reembolso.download',$r->id)}}" type="button" class="btn btn-success btn-xs" target="_blank">PDF</a>

					<a href="{{route('reembolso.downloadZip',$r->id)}}" type="button" class="btn btn-warning btn-xs" target="_self">ZIP</a>
					<a href="{{route('reembolso.destroy',$r->id)}}" type="button" class="confirmation btn btn-danger btn-xs"> <i class="glyphicon glyphicon-trash"></i></a>
					</td>				
				
				</tr>

				@endforeach
                </tbody>
              </table>   
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
		    $('#lista-reembolsos').DataTable({
		      "paging": true,
		      "lengthChange": true,
		      "searching": true,
		      "ordering": true,
		      "info": true,
		      "autoWidth": false,
		       "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json",
				
            },
			"order": [[2, 'desc']],   
  });
$('.confirmation').on('click', function () {
        		return confirm('Você deseja excluir o reembolso?');
    			});
		     
		    });
			
		
			
</script>

<script>
	$(document).ready(function () {
	    $('#myModal').on('show.bs.modal', function (event) {
	        var button = $(event.relatedTarget); // Button that triggered the modal
	        var reembolso_id = button.data('reembolso_id'); // Extract info from data-* attributes
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
	            name: "reembolso_id",
	            value: reembolso_id
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