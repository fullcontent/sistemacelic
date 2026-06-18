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
                 <button type="button" class="btn btn-xs btn-default ver-pendencia-btn" data-id="{{$pendencia->id}}">
                   <span class="fa fa-paperclip"></span>Ver
                   </button>
                   </span>
         
        
        </li>
        @endforeach
        
      </ul>
    </div>
   
   
  </div>

<!-- Modal de Detalhes da Pendência -->
<div class="modal fade" id="modal-ver-pendencia" tabindex="-1" role="dialog" aria-labelledby="modalVerPendenciaLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalVerPendenciaLabel"><i class="fa fa-tasks"></i> Detalhes da Pendência</h4>
      </div>
      <div class="modal-body" style="font-size: 14px; line-height: 1.6;">
        <div class="row" style="margin-bottom: 15px;">
          <div class="col-md-2">
            <label class="text-muted" style="display:block; margin-bottom: 2px;">Etapa</label>
            <div id="modal-pendencia-etapa" class="text-bold"></div>
          </div>
          <div class="col-md-3">
            <label class="text-muted" style="display:block; margin-bottom: 2px;">Ordem de Serviço</label>
            <div id="modal-pendencia-os" class="text-bold"></div>
          </div>
          <div class="col-md-4">
            <label class="text-muted" style="display:block; margin-bottom: 2px;">Descrição</label>
            <div id="modal-pendencia-descr" class="text-bold"></div>
          </div>
          <div class="col-md-3">
            <label class="text-muted" style="display:block; margin-bottom: 2px;">Status</label>
            <div id="modal-pendencia-status" class="text-bold"></div>
          </div>
        </div>
        <div class="row" style="margin-bottom: 15px;">
          <div class="col-md-4">
            <label class="text-muted" style="display:block; margin-bottom: 2px;">Responsabilidade</label>
            <div id="modal-pendencia-resp-tipo" class="text-bold"></div>
          </div>
          <div class="col-md-4">
            <label class="text-muted" style="display:block; margin-bottom: 2px;">Responsável</label>
            <div id="modal-pendencia-resp" class="text-bold"></div>
          </div>
          <div class="col-md-4">
            <label class="text-muted" style="display:block; margin-bottom: 2px;">Data Limite</label>
            <div id="modal-pendencia-venc" class="text-bold"></div>
          </div>
        </div>
        <div class="row" style="margin-bottom: 15px;">
          <div class="col-md-12">
            <label class="text-muted" style="display:block; margin-bottom: 2px;">Observações</label>
            <div id="modal-pendencia-obs" class="well well-sm" style="background: #fdfdfd; min-height: 40px; margin-bottom: 0; white-space: pre-line;"></div>
          </div>
        </div>
        <div class="row" id="modal-pendencia-arquivos-sec" style="display:none;">
          <div class="col-md-12">
            <label class="text-muted" style="display:block; margin-bottom: 5px;">Arquivos</label>
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Arquivo</th>
                  <th>Cadastrado por:</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody id="modal-pendencia-arquivos-body">
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

@section('js')

<script>
$('.ver-pendencia-btn').on('click', function(e){
  e.preventDefault();
  var id = $(this).data('id');
  
  var $btn = $(this);
  var originalHtml = $btn.html();
  $btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
  
  var url = "{{ route('cliente.pendencia.show', ':id') }}";
  url = url.replace(':id', id);
  
  $.getJSON(url, function(data) {
    $('#modal-pendencia-etapa').text(data.etapa);
    $('#modal-pendencia-os').text(data.os);
    $('#modal-pendencia-descr').text(data.pendencia);
    $('#modal-pendencia-status').text(data.status);
    $('#modal-pendencia-resp-tipo').text(data.responsabilidade);
    $('#modal-pendencia-resp').text(data.responsavel);
    $('#modal-pendencia-venc').text(data.vencimento);
    
    // Set observations, rendering HTML formatted output if tags are detected
    var obs = data.observacoes || 'Nenhuma observação registrada.';
    var hasHtml = /<[a-z/][\s\S]*>/i.test(obs);
    if (hasHtml) {
        $('#modal-pendencia-obs').html(obs).css('white-space', 'normal');
    } else {
        $('#modal-pendencia-obs').text(obs).css('white-space', 'pre-line');
    }
    
    var $filesBody = $('#modal-pendencia-arquivos-body');
    $filesBody.empty();
    if(data.arquivos && data.arquivos.length > 0) {
      $.each(data.arquivos, function(idx, arq) {
        $filesBody.append(
          '<tr>' +
          '  <td>' + arq.nome + '</td>' +
          '  <td>' + arq.user_name + '</td>' +
          '  <td><a href="' + arq.download_url + '" class="btn btn-xs btn-success"><i class="fa fa-download"></i> Download</a></td>' +
          '</tr>'
        );
      });
      $('#modal-pendencia-arquivos-sec').show();
    } else {
      $('#modal-pendencia-arquivos-sec').hide();
    }
    
    $('#modal-ver-pendencia').modal('show');
  }).fail(function() {
    alert('Erro ao carregar detalhes da pendência.');
  }).always(function() {
    $btn.html(originalHtml).prop('disabled', false);
  });
});
</script>
@stop