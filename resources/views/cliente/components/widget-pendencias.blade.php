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
          
        
        </li>
        @endforeach
        
      </ul>
    </div>
   
   
  </div>

