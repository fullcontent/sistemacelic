<div class="box box-info ">
  <div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-credit-card"></i> Ordens de Compra</h3>

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
            <th>Prestador</th>
            <th>Valor</th>
            <th class="text-center">Situação</th>
            <th class="text-center">Comprovantes</th>
            <th class="text-right">Ações</th>
          </tr>
        </thead>
        <tbody>

          @foreach($ordensCompra as $oc)

            <tr>
              <td style="vertical-align: middle;">
                <strong>{{$oc->prestador->nome}}<small>({{$oc->prestador->qualificacao}})</small> </strong>
              </td>
              <td style="vertical-align: middle;">R$ {{ number_format($oc->valorServico, 2, ',', '.') }}</td>
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
                <a href="{{route('ordemCompra.edit', $oc->id)}}" class="btn btn-sm btn-info" title="Editar">
                  <i class="glyphicon glyphicon-pencil"></i> Editar
                </a>

                {!! Form::open(['route' => ['ordemCompra.destroy', $oc->id], 'method' => 'delete', 'style' => 'display:inline']) !!}
                <button type="submit" class="btn btn-sm btn-danger"
                  onclick="return confirm('Tem certeza que deseja excluir esta ordem de compra?')" title="Excluir">
                  <i class="glyphicon glyphicon-trash"></i> Excluir
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
      <a href="{{route('ordemCompra.criar', $servico->id)}}" class="btn btn-sm btn-info btn-flat pull-left">
        <i class="glyphicon glyphicon-plus-sign"></i> Nova ordem de Compra
      </a>
    @endif
  </div>
  <!-- /.box-footer -->
</div>