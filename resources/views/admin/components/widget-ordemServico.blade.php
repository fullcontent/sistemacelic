<div class="box box-info ">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-credit-card"></i> Ordens de Serviço</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body no-padding">
    <div class="table-responsive">
      <table class="table table-hover table-striped no-margin">
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Prestador</th>
            <th>Resumo</th>
            <th>Valor OS</th>
            <th>Custo Relac.</th>
            <th class="text-center">Situação</th>
            <th class="text-center">Comprovantes</th>
            <th class="text-right">Ações</th>
          </tr>
        </thead>
        <tbody>

          @foreach($ordensServico as $oc)
            @php
              $isMatriz = ($oc->servico_id == $servico->id);
              $vinculoAtual = $oc->vinculos->where('servico_id', $servico->id)->first();
              $valorRelacionado = $vinculoAtual ? $vinculoAtual->valor : 0;
              
              $clean_escopo = strip_tags($oc->escopo);
              if (strlen($clean_escopo) > 50) {
                  $clean_escopo = substr($clean_escopo, 0, 50) . '...';
              }
            @endphp
            <tr>
              <td style="vertical-align: middle;">
                @if($isMatriz)
                  <span class="label label-primary" title="Serviço Principal desta OS">Matriz</span>
                @else
                  <span class="label label-default" style="background-color: #d2d6de; color: #444;" title="Vinculado via Rateio">Rateio</span>
                @endif
              </td>
              <td style="vertical-align: middle;">
                <strong>{{$oc->prestador->nome}}</strong>
              </td>
              <td style="vertical-align: middle;">
                <small class="text-muted">{{ $clean_escopo ?: '---' }}</small>
              </td>
              <td style="vertical-align: middle;">R$ {{ number_format($oc->valorServico, 2, ',', '.') }}</td>
              <td style="vertical-align: middle;">
                <b class="text-blue">R$ {{ number_format($valorRelacionado, 2, ',', '.') }}</b>
              </td>
              <td class="text-center" style="vertical-align: middle;">
                @if($oc->situacaoPagamento->count())
                  <span class="label label-warning" style="font-size: 11px;">
                    Em aberto ({{$oc->pagamentos->where('situacao', 'pago')->count()}}/{{$oc->pagamentos->count()}})
                  </span>
                @else
                  <span class="label label-success" style="font-size: 11px;">Pago</span>
                @endif
              </td>
              <td class="text-center" style="vertical-align: middle;">
                @php $hasComprovante = false; @endphp
                @foreach($oc->pagamentos as $p)
                  @if($p->comprovante)
                    @php $hasComprovante = true; @endphp
                    <a href="{{ url("public/uploads/$p->comprovante") }}" class="btn btn-xs btn-default" target="_blank"
                      title="Parcela {{$p->parcela}}">
                      <i class="glyphicon glyphicon-file text-red"></i>
                    </a>
                  @endif
                @endforeach
                @if(!$hasComprovante)
                  <span class="text-muted">---</span>
                @endif
              </td>
              <td class="text-right" style="vertical-align: middle; white-space: nowrap;">
                <a href="{{route('ordemServico.edit', $oc->id)}}" class="btn btn-sm btn-info" title="Editar">
                  <i class="glyphicon glyphicon-pencil"></i>
                </a>

                {!! Form::open(['route' => ['ordemServico.destroy', $oc->id], 'method' => 'delete', 'style' => 'display:inline']) !!}
                <button type="submit" class="btn btn-sm btn-danger"
                  onclick="return confirm('Tem certeza que deseja excluir esta ordem de serviço?')" title="Excluir">
                  <i class="glyphicon glyphicon-trash"></i>
                </button>
                {!! Form::close() !!}
              </td>
            </tr>

          @endforeach

        </tbody>
      </table>
    </div>
  </div>
  <!-- /.box-body -->
  <div class="box-footer clearfix">
    @if(Request::is('admin/servicos/*'))
      <a href="{{route('ordemServico.criar', $servico->id)}}" class="btn btn-sm btn-info btn-flat pull-left">
        <i class="glyphicon glyphicon-plus-sign"></i> Nova ordem de Serviço
      </a>
    @endif
  </div>
  <!-- /.box-footer -->
</div>