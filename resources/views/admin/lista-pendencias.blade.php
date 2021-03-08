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

              <h3 class="box-title">{{$title}}</h3>
  
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
                  <span class="text"><a href="{{route('pendencia.edit',$pendencia->id)}}">{{$pendencia->pendencia}}</a></span>
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
                    <a href="{{route('pendencia.edit',$pendencia->id)}}"><i class="fa fa-edit"></i></a>
                     <a href="{{route('pendencia.delete',$pendencia->id)}}" onclick="return confirm('Tem certeza que deseja excluir a pendência?');"><i class="fa fa-trash"></i></a>
                    
                  </div>
                </li>
                @endforeach
                
              </ul>
            </div>
            <!-- /.box-body -->
           
           
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