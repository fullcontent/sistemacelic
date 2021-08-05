<div class="row">
    <div class="col-lg-12 col-xs-12">
        
        <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Pendências do dia</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table id="lista-pendencias-dia" class="table table-bordered table-hover">
                  <thead>
                  <tr>
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
                    @foreach($pendencias->where('status','pendente')->where('vencimento',date('Y-m-d')) as $p)
                  <tr>
                  <td width="2%" class="prioridade">
                      @if($p->prioridade == 1)
                      <span style="display:none;">{{$p->prioridade}}</span>
                      <i class="fa fa-exclamation priorize" style="color:red" data-id="{{$p->id}}"></i>
                      
                      @else
                      <span style="display:none;">{{$p->prioridade}}</span>
                      <input type="checkbox" data-id="{{$p->id}}" id="{{$p->id}}">                
                      
                      @endif
                  
                  
                  
                  
                  </td>
                    <td><a href="{{route('empresas.show',$p->servico['unidade']['empresa']['id'])}}">{{$p->servico['unidade']['empresa']['nomeFantasia']}}</a></td>
                    <td><a href="{{route('servicos.show',$p->servico_id)}}">{{$p->servico['unidade']['codigo']}}</a></td>
                    <td><a href="{{route('servicos.show',$p->servico_id)}}">{{$p->servico['unidade']['nomeFantasia']}}</a></td>
                    <td><a href="{{route('servicos.show',$p->servico_id)}}">{{$p->servico['nome']}}</a></td>
                    <td><a href="{{route('servicos.show',$p->servico_id)}}">{{$p->pendencia}}</a></td>
                    <td><a href="{{route('servicos.show',$p->servico_id)}}"><span style="display:none;">{{$p->vencimento}}</span>{{\Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</a></td>
                  </tr>
                    @endforeach
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              
            </div>
            <!-- /.box-footer -->
          </div>

    </div>

</div>

@section('js')

<script>
$(document).ready(function($){

$('input:checkbox').click(function() {


  var pendenciaID = $(this).data('id');
  var row = $(this).closest("tr");

      
  $.ajax({
            url: '{{url('admin/pendencia/priority')}}/'+pendenciaID+'',
            method: 'GET',
            success: function(data) {

              $(this).data('status', data.completed);
                      
              
              row.find(".prioridade").html('<i class="fa fa-exclamation priorize" style="color:red" data-id='+pendenciaID+'></i>');
              },
            })

});

$('.priorize').click(function(){

  var pendenciaID = $(this).data('id');
  var row = $(this).closest("tr");
  $.ajax({
            url: '{{url('admin/pendencia/unPriority')}}/'+pendenciaID+'',
            method: 'GET',
            success: function(data) {

              $(this).data('status', data.completed);
                      
              
              row.find(".prioridade").html('<input type="checkbox" data-id='+pendenciaID+'> ');
              },
            })
})

 

});
</script>


@stop