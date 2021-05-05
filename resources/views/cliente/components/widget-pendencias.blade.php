<div class="box box-primary">
    <div class="box-header ui-sortable-handle" style="cursor: move;">
      <i class="ion ion-clipboard"></i>

      <h3 class="box-title">Pendências em aberto</h3>

    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <!-- See dist/js/pages/dashboard.js to activate the todoList plugin -->
      <ul class="todo-list ui-sortable" data-widget="todo-list" id="todo-list">
        
      
        
        @foreach($pendencias->where('status','pendente') as $pendencia)
        <li @if($pendencia->status == 'concluido') class='done' @endif>
          <!-- drag handle -->
          
          <!-- checkbox -->
         
          <!-- todo text -->
          <span class="text">{{$pendencia->pendencia}}</span>

          @switch($pendencia->vencimento)
                
                @case($pendencia->vencimento > date('Y-m-d'))
                    <span class="label label-success">{{ \Carbon\Carbon::parse($pendencia->vencimento)->format('d/m/Y')}}</span>
                @break

                @case($pendencia->vencimento < date('Y-m-d'))
                    <span class="label label-danger">{{ \Carbon\Carbon::parse($pendencia->vencimento)->format('d/m/Y')}}</span>
                @break

                @case($pendencia->vencimento == date('Y-m-d'))
                    <span class="label label-warning">{{ \Carbon\Carbon::parse($pendencia->vencimento)->format('d/m/Y')}}</span>
                @break





          @endswitch

          
          <span class="pull-right">
          <button type="button" class="btn btn-xs btn-default" data-toggle="modal" data-target="#cadastro-arquivo" data-nome="{{$pendencia->pendencia}}">
                <span class="fa fa-paperclip"></span>Anexar
                </button>
                  </span>
         
        
        </li>
        @endforeach
        
      </ul>
    </div>
   
   
  </div>

  <div class="modal fade" id="cadastro-arquivo">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Anexar arquivo a pendencia</h4>
              </div>
              <div class="modal-body">
                
					{!! Form::open(['route'=>'cliente.arquivo.anexar','enctype'=>'multipart/form-data']) !!}
						
						<div class="form-group">
							{!! Form::label('nome', 'Nome', array('class'=>'control-label')) !!}
							{!! Form::text('nome', null, ['class'=>'form-control','id'=>'nomeArquivo']) !!}

						</div>

						<div class="form-group">
							 {!! Form::label('arquivo', 'Arquivo', array('class'=>'control-label')) !!}
        				{!! Form::file('arquivo', null, ['class'=>'form-control','id'=>'arquivo']) !!}

                {!! Form::hidden('pendencia_id', $pendencia->id) !!}

                {!! Form::hidden('servico_id', $pendencia->servico_id) !!}
                {!! Form::hidden('unidade_id', $servico->unidade_id) !!}

                {!! Form::hidden('user_id', Auth::id()) !!}

                

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
@section('js')

<script>

$('#cadastro-arquivo').on('show.bs.modal', function(e) {  
            var getIdFromRow = $(e.relatedTarget).data('nome');

           
            $("#nomeArquivo").val(getIdFromRow);
            });



</script>
@stop