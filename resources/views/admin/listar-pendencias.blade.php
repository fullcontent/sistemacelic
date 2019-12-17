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
                  
                  <!-- checkbox -->
                  <input type="checkbox" data-id="{{$pendencia->id}}" @if($pendencia->status == 'concluido') checked="" @endif>
                  <!-- todo text -->
                  <span class="text">{{$pendencia->pendencia}}</span>
                  <!-- Emphasis label -->
                  
                  <!-- General tools such as edit or delete-->
                  <div class="tools">
                    <a href="{{route('pendencia.edit',$pendencia->id)}}"><i class="fa fa-edit"></i></a>
                    
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
    </script>
@stop