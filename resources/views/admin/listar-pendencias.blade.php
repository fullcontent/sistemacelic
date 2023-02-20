@extends('adminlte::page')

@section('content')

<div class="row">
    <div class="col-md-12">
        
       
        
    </div>
</div>

<div class="row">
	<div class="col-md-12">
    
    	<div class="box box-primary">
            <div class="box-header ui-sortable-handle" style="cursor: move;">
              <i class="ion ion-clipboard"></i>

              <h3 class="box-title">Pendências da O.S. {{$servico->os}}</h3>
  
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <!-- See dist/js/pages/dashboard.js to activate the todoList plugin -->
              <ul class="todo-list ui-sortable" data-widget="todo-list" id="todo-list">
                
                @foreach($pendencias as $pendencia)
                <li @if($pendencia->status == 'concluido') class='done' @endif>
                  <!-- drag handle -->
                  @if($pendencia->prioridade == 1)
                      <span style="display:none;">{{$pendencia->prioridade}}</span>
                      <a href="#" onClick="unPriorize({{$pendencia->id}})">
                      <i class="fa fa-exclamation priorize" style="color:red" data-prioridadeID="{{$pendencia->id}}"></i></a>
                      
                      @else
                      <span style="display:none;">{{$pendencia->prioridade}}</span>
                      <a href="#" onClick="unPriorize({{$pendencia->id}})">
                      <i class="priorize" style="color:red" data-prioridadeID="{{$pendencia->id}}"></i></a>
                              
                      
                      @endif
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
			
			<a href="{{route('servicos.show', $servico->id)}}" class="btn btn-default pull-left"><i class="fa fa-arrow"></i> Voltar</a>		

              <a href="{{route('pendencia.create', ['servico_id'=>$servico->id])}}" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Adicionar Pendência</a>
             
            </div>
           
          </div>


    
	</div>
</div>

@endsection

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