@extends('adminlte::page')

@section('css')
<style>
	.table-container {
		background: #fff;
		border-radius: 8px;
		padding: 20px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
		border: 1px solid #ebf0f5;
		margin-bottom: 25px;
	}

	.content-header .breadcrumb { display: none !important; }
</style>
@stop

@section('content_header')
<div class="row" style="margin-bottom: 15px;">
	<div class="col-sm-12">
		<h1 style="margin: 0; font-weight: 700; color: #333;">{{$title}}</h1>
	</div>
</div>
@stop

@section('content')
<div class="table-container">
	<table id="lista-pendencias" class="table table-hover" style="width: 100%;">
		<thead>
			<tr style="background: #fcfcfc;">
				<th>Prioridade</th>
				<th>Empresa</th>
				<th>Cod.</th>
				<th>Unidade</th>
				<th>Serviço</th>
				<th>Pendência</th>
				<th>Data</th>
			</tr>
		</thead>
		<tbody>
			@foreach($pendencias->where('status','pendente') as $p)
				<tr>
					<td width="8%" class="prioridade text-center">
						@if($p->prioridade == 1)
							<span style="display:none;">1</span>
							<span class="label label-danger priorize" style="cursor:pointer; border-radius: 4px; padding: 3px 8px;" data-id="{{$p->id}}" data-toggle="tooltip" data-placement="top" title="{{ $p->observacoes ?: 'Pendência com Prioridade/Urgência Alta' }}">
								<i class="fa fa-exclamation-triangle"></i> Alta
							</span>
						@else
							<span style="display:none;">0</span>
							<span class="label label-default" style="cursor:pointer; border-radius: 4px; padding: 3px 8px;" data-toggle="tooltip" data-placement="top" title="{{ $p->observacoes ?: 'Prioridade Normal' }}">
								<input type="checkbox" data-id="{{$p->id}}" id="{{$p->id}}" style="margin-right: 5px; vertical-align: middle;"> Normal
							</span>
						@endif
					</td>
					<td><a href="{{route('empresas.show',$p->servico['unidade']['empresa']['id'])}}" style="font-weight: 600; color: #3c8dbc;">{{$p->servico['unidade']['empresa']['nomeFantasia']}}</a></td>
					<td><a href="{{route('servicos.show',$p->servico_id)}}" style="color: #555;"><code>{{$p->servico['unidade']['codigo']}}</code></a></td>
					<td><a href="{{route('servicos.show',$p->servico_id)}}" style="color: #555;">{{$p->servico['unidade']['nomeFantasia']}}</a></td>
					<td><a href="{{route('servicos.show',$p->servico_id)}}" style="color: #555;">{{$p->servico['nome']}}</a></td>
					<td><a href="{{route('servicos.show',$p->servico_id)}}" style="color: #555;">{{$p->pendencia}}</a></td>
					<td>
						<span style="display:none;">{{$p->vencimento}}</span>
						<a href="{{route('servicos.show',$p->servico_id)}}">
							@switch($p->vencimento)
								@case($p->vencimento > date('Y-m-d'))
									<span class="label label-success" style="border-radius: 4px; padding: 3px 8px;">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</span>
									@break

								@case($p->vencimento < date('Y-m-d'))
									<span class="label label-danger" style="border-radius: 4px; padding: 3px 8px;">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</span>
									@break

								@case($p->vencimento == date('Y-m-d'))
									<span class="label label-warning" style="border-radius: 4px; padding: 3px 8px;">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</span>
									@break
							@endswitch
						</a>
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
			$('[data-toggle="tooltip"]').tooltip();

		    $('#lista-pendencias').DataTable({
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

  $(document).on('click', 'input:checkbox', function() {
    var pendenciaID = $(this).data('id');
    var row = $(this).closest("tr");
    
    $.ajax({
      url: '{{url('admin/pendencia/priority')}}/'+pendenciaID+'',
      method: 'GET',
      success: function(data) {
        row.find(".prioridade").html('<span style="display:none;">1</span><span class="label label-danger priorize" style="cursor:pointer;" data-id="'+pendenciaID+'" data-toggle="tooltip" data-placement="top" title="Pendência com Prioridade/Urgência Alta"><i class="fa fa-exclamation-triangle"></i> Alta</span>');
        $('[data-toggle="tooltip"]').tooltip();
      },
    });
  });

  $(document).on('click', '.priorize', function(){
    var pendenciaID = $(this).data('id');
    var row = $(this).closest("tr");
    $.ajax({
      url: '{{url('admin/pendencia/unPriority')}}/'+pendenciaID+'',
      method: 'GET',
      success: function(data) {
        row.find(".prioridade").html('<span style="display:none;">0</span><span class="label label-default" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Prioridade Normal"><input type="checkbox" data-id="'+pendenciaID+'"> Normal</span>');
        $('[data-toggle="tooltip"]').tooltip();
      },
    });
  });

});			
</script>
@stop