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

	.btn-pill {
		border-radius: 50px;
		padding: 6px 20px;
		font-weight: 600;
		transition: all 0.2s;
	}

	.btn-pill:hover {
		transform: translateY(-1px);
		box-shadow: 0 2px 5px rgba(0,0,0,0.1);
	}

	.content-header .breadcrumb { display: none !important; }
</style>
@stop

@section('content_header')
<div class="row" style="margin-bottom: 15px;">
	<div class="col-sm-12">
		<h1 style="margin: 0; font-weight: 700; color: #333;">Pendências da O.S. {{$servico->os}}</h1>
	</div>
</div>
@stop

@section('content')
<div class="table-container">
	<!-- See dist/js/pages/dashboard.js to activate the todoList plugin -->
	<ul class="todo-list ui-sortable" data-widget="todo-list" id="todo-list" style="margin-bottom: 20px;">
		@foreach($pendencias as $pendencia)
			<li @if($pendencia->status == 'concluido') class='done' @endif>
				<!-- drag handle -->
				@if($pendencia->prioridade == 1)
					<span style="display:none;">1</span>
					<a href="#" onClick="unPriorize({{$pendencia->id}})">
						<span class="label label-danger" style="border-radius: 4px; padding: 3px 8px;" data-toggle="tooltip" data-placement="top" title="{{ $pendencia->observacoes ?: 'Pendência com Prioridade/Urgência Alta' }}" data-prioridadeID="{{$pendencia->id}}">
							<i class="fa fa-exclamation-triangle"></i> Alta
						</span>
					</a>
				@else
					<span style="display:none;">0</span>
					<a href="#" onClick="priorize({{$pendencia->id}})">
						<span class="label label-default" style="border-radius: 4px; padding: 3px 8px;" data-toggle="tooltip" data-placement="top" title="{{ $pendencia->observacoes ?: 'Prioridade Normal' }}" data-prioridadeID="{{$pendencia->id}}">
							Normal
						</span>
					</a>
				@endif
				<!-- checkbox -->
				<input type="checkbox" data-id="{{$pendencia->id}}" @if($pendencia->status == 'concluido') checked="" @endif style="margin: 0 10px; vertical-align: middle;">
				<!-- todo text -->
				<span class="text" style="font-weight: 500;">{{$pendencia->pendencia}}</span>
				@switch($pendencia->vencimento)
					@case($pendencia->vencimento > date('Y-m-d'))
						<span class="label label-success" style="border-radius: 4px; padding: 2px 6px; margin-left: 10px;">{{ \Carbon\Carbon::parse($pendencia->vencimento)->format('d/m/Y')}}</span>
						@break

					@case($pendencia->vencimento < date('Y-m-d'))
						<span class="label label-danger" style="border-radius: 4px; padding: 2px 6px; margin-left: 10px;">{{ \Carbon\Carbon::parse($pendencia->vencimento)->format('d/m/Y')}}</span>
						@break

					@case($pendencia->vencimento == date('Y-m-d'))
						<span class="label label-warning" style="border-radius: 4px; padding: 2px 6px; margin-left: 10px;">{{ \Carbon\Carbon::parse($pendencia->vencimento)->format('d/m/Y')}}</span>
						@break
				@endswitch

				<!-- General tools such as edit or delete-->
				<div class="tools">
					<a href="#" onClick="priorize({{$pendencia->id}})"><i class="fa fa-exclamation text-warning"></i></a>
					<a href="{{route('pendencia.edit',$pendencia->id)}}"><i class="fa fa-edit text-info"></i></a>
					<a href="{{route('pendencia.delete',$pendencia->id)}}" onclick="return confirm('Tem certeza que deseja excluir a pendência?');"><i class="fa fa-trash text-danger"></i></a>
				</div>
			</li>
		@endforeach
	</ul>

	<div style="border-top: 1px solid #f4f4f4; padding-top: 15px; display: flex; justify-content: space-between; align-items: center;">
		<a href="{{route('servicos.show', $servico->id)}}" class="btn btn-default btn-pill"><i class="fa fa-arrow-left" style="margin-right: 5px;"></i> Voltar</a>		
		<a href="{{route('pendencia.create', ['servico_id'=>$servico->id])}}" class="btn btn-primary btn-pill"><i class="fa fa-plus" style="margin-right: 5px;"></i> Adicionar Pendência</a>
	</div>
</div>
@endsectionction

@section('js')
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
        
       $('#todo-list').todoList({ 



            onCheck: function(checkbox) {
              
            $.ajax({
            url: '{{url('admin/pendencia/done')}}/'+$(this).data('id')+'',
            method: 'GET',
            success: function(data) {

              $(this).data('status', data.completed);
            },
            })
                                     
      },
      onUnCheck: function(checkbox) {
        // Do something after the checkbox has been unchecked

        $.ajax({
            url: '{{url('admin/pendencia/undone')}}/'+$(this).data('id')+'',
            method: 'GET',
            success: function(data) {

              $(this).data('status', data.completed);
            },
            })
      }
    })

function priorize(id)
{

  var pendenciaID = id;
  
  $.ajax({
            url: '{{url('admin/pendencia/priority')}}/'+pendenciaID+'',
            method: 'GET',
            success: function(data) {

              $(this).data('status', data.completed);      
              
              $("[data-prioridadeID="+pendenciaID+"]").attr("class","fa fa-exclamation");
              },
            })
 
}

function unPriorize(id)
{

  var pendenciaID = id;
  
  $.ajax({
            url: '{{url('admin/pendencia/unPriority')}}/'+pendenciaID+'',
            method: 'GET',
            success: function(data) {

              $(this).data('status', data.completed);      
              
              $("[data-prioridadeID="+pendenciaID+"]").removeClass("fa fa-exclamation");
              },
            })
 
}


    </script>
@stop