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
                <li @if($pendencia->status == 'concluido') class='done' @endif @if($pendencia->responsavel_id == Auth::id()) style="color:red;" @endif>
                  <!-- drag handle -->
                  
                  <!-- checkbox -->
                  <input type="checkbox" data-id="{{$pendencia->id}}" @if($pendencia->status == 'concluido') checked="" @endif>
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

                
                <button type="button" class="btn btn-xs btn-default" data-toggle="modal" data-target="#cadastro-arquivo">
                <span class="glyphicon glyphicon-plus-sign"></span>Anexar
                </button>
                  
                  <!-- Emphasis label -->
                  
                  <!-- General tools such as edit or delete-->
                  <div class="tools">
                    <a href="{{route('pendencia.edit',$pendencia->id)}}"><i class="fa fa-edit"></i></a>
                    <a href="{{route('pendencia.delete',$pendencia->id)}}" onclick="return confirm('Tem certeza que deseja excluir a pendência?');"><i class="fa fa-trash"></i></a>
                    
                  </div>
                </li>
                @endforeach
                
              </ul>
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix no-border">
              <a href="{{route('pendencia.create', ['servico_id'=>$servico->id])}}" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Adicionar Pendência</a>
              <a href="{{route('pendencia.index', ['servico_id'=>$servico->id])}}" class="btn btn-default pull-left"><i class="fa fa-list"></i> Todas as pendências</a>
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
                
					{!! Form::open(['route'=>'arquivo.store','enctype'=>'multipart/form-data']) !!}
						
						<div class="form-group">
							{!! Form::label('nome', 'Nome', array('class'=>'control-label')) !!}
							{!! Form::text('nome', null, ['class'=>'form-control','id'=>'email']) !!}

						</div>

						<div class="form-group">
							 {!! Form::label('arquivo', 'Arquivo', array('class'=>'control-label')) !!}
        				{!! Form::file('arquivo', null, ['class'=>'form-control','id'=>'arquivo']) !!}

                

               
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
    </script>
@stop