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
                  @if($pendencia->prioridade == 1)
                      <span style="display:none;">{{$pendencia->prioridade}}</span>
                      
                      <i class="fa fa-exclamation priorize" style="color:red" data-prioridadeID="{{$pendencia->id}}" onClick="unPriorize({{$pendencia->id}})"></i>
                      
                      @else
                      <span style="display:none;">{{$pendencia->prioridade}}</span>
                      
                      <i class="priorize" style="color:red" data-prioridadeID="{{$pendencia->id}}"></i>
                              
                      
                      @endif
                  <!-- checkbox -->
                  <input type="checkbox" data-id="{{$pendencia->id}}" @if($pendencia->status == 'concluido') checked="" @endif>
                  <!-- todo text -->
                  <span class="text"><a href="{{route('pendencia.edit',$pendencia->id)}}">{{$pendencia->pendencia}}</a></span>

                  @switch($pendencia->vencimento)
                        
                        @case($pendencia->vencimento > date('Y-m-d'))
                            <span id="dataPendencia" class="label label-success">{{ \Carbon\Carbon::parse($pendencia->vencimento)->format('d/m/Y')}}</span>
                            
                        @break

                        @case($pendencia->vencimento < date('Y-m-d'))
                            <span id="dataPendencia"  class="label label-danger">{{ \Carbon\Carbon::parse($pendencia->vencimento)->format('d/m/Y')}}</span>
                        @break

                        @case($pendencia->vencimento == date('Y-m-d'))
                            <span id="dataPendencia"  class="label label-warning">{{ \Carbon\Carbon::parse($pendencia->vencimento)->format('d/m/Y')}}</span>
                        @break





                  @endswitch

                
                <button type="button" class="btn btn-xs btn-default" data-toggle="modal" data-target="#cadastro-arquivo" data-nome="{{$pendencia->pendencia}}" data-pendencia="{{$pendencia->id}}" data-servico="{{$pendencia->servico_id}}">
                <span class="fa fa-paperclip"></span>Anexar
                </button>
                  
                  <!-- Emphasis label -->
                  
                  <!-- General tools such as edit or delete-->
                  <div class="tools">
                  <a href="#" onClick="priorize({{$pendencia->id}})"><i class="fa fa-exclamation"></i></a>
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
							{!! Form::text('nome', null, ['class'=>'form-control','id'=>'nomeArquivo']) !!}

						</div>

						<div class="form-group">
							 {!! Form::label('arquivo', 'Arquivo', array('class'=>'control-label')) !!}
        				{!! Form::file('arquivo', null, ['class'=>'form-control','id'=>'arquivo']) !!}

                
                

                {!! Form::hidden('unidade_id', $servico->unidade_id) !!}
                {!! Form::hidden('pendencia_id', null,['class'=>'form-control','id'=>'pendenciaID']) !!}
                {!! Form::hidden('servico_id', null,['class'=>'form-control','id'=>'servicoID']) !!}
                

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
            var pendenciaID = $(e.relatedTarget).data('pendencia');
            var servicoID = $(e.relatedTarget).data('servico');
           
            $("#nomeArquivo").val(getIdFromRow);
            $("#pendenciaID").val(pendenciaID);
            $("#servicoID").val(servicoID);

            console.log(pendenciaID)
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

$('#full').mentionsInput({
    onDataRequest:function (mode, query, callback) {
      $.getJSON('{{route('users.list')}}', function(responseData) {
        responseData = _.filter(responseData, function(item) { 
          return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 
          });
        
          callback.call(this, responseData);        
      });
    }    

});


$('.responder').click(function () {

        
  var msg = $(this).data('msg');
  var userID = $(this).data('user');

  $.getJSON('{{route('users.list')}}', function(responseData) {

   var user_filter = responseData.filter(element => element.id == userID);

   var userName = JSON.stringify(user_filter);
  var user = JSON.parse(userName);
    
     

    $('#full').val(user[0].name).focus();

  });







});

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
              
              $("[data-prioridadeID="+pendenciaID+"]").removeAttr("class","fa fa-exclamation");


              console.log("Removeu Prioridade");
              },
            })
 
}



</script>
@stop