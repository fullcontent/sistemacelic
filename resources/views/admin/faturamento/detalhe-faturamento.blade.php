@extends('adminlte::page')

@section('css')

  <style>
    @media print {
      a[href]:after {
        content: none !important;
      }
    }
  </style>



@section('js')
<script>
$(function() {
    $('.btn-sync').on('click', function() {
        const id = $(this).data('id');
        const btn = $(this);
        btn.html('<i class="fa fa-refresh fa-spin"></i> Sincronizando...').prop('disabled', true);
        
        $.ajax({
            url: "{{ url('admin/nfse/sync') }}/" + id,
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function(err) {
                alert('Erro ao sincronizar status.');
                btn.html('<i class="fa fa-refresh"></i> Sincronizar Status').prop('disabled', false);
            }
        });
    });
});
</script>
@stop
@endsection

@section('content')

  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h2 class="page-header">
          <img src="{{asset('img/logoCastro.png')}}" alt="" width="300">
          <small class="pull-right">Data: {{$data}}</small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
      <div class="col-sm-6">
        <p><b>RELATÓRIO DE FATURAMENTO: </b>{{$descricao}} 
          <span class="no-print">
            <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#edit-faturamento"><i class="fa fa-edit"></i> Editar</button>
            @if(!$faturamento->ultimaEmisao)
              <a href="{{ route('nfse.emissao', $faturamento->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-magic"></i> Gerar NFS-e</a>
            @elseif(in_array(strtoupper($faturamento->ultimaEmisao->status), ['ERRO', 'CANCELADA', 'REJEITADO', 'REJEITADA']))
              <a href="{{ route('nfse.emissao', $faturamento->id) }}" class="btn btn-xs btn-warning"><i class="fa fa-refresh"></i> Re-emitir NFS-e</a>
            @endif
          </span>
        </p>
        <p><b>Referência: </b>{{$obs}}</p>


      </div>

    </div>
    <!-- /.row -->

    @if($faturamento->ultimaEmisao)
    <div class="row no-print" style="margin-bottom: 20px;">
      <div class="col-xs-12">
        <div class="box box-solid" style="border: 1px solid #d2d6de; background-color: #f9f9f9;">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-file-text-o"></i> Status NFS-e (PlugNotas)</h3>
          </div>
          <div class="box-body">
            @php 
               $emissao = $faturamento->ultimaEmisao;
               $status = strtoupper($emissao->status);
               $success = ($status == 'CONCLUIDA' || $status == 'CONCLUIDO' || $status == 'EMITIDA' || $status == 'CONCLUIDO');
            @endphp
            <div class="row">
              <div class="col-sm-4">
                <p><b>Status: </b>
                  @if($success)
                    <span class="label label-success">CONCLUÍDA</span>
                  @elseif($status == 'PROCESSANDO')
                    <span class="label label-primary"><i class="fa fa-spinner fa-spin"></i> PROCESSANDO</span>
                  @elseif($status == 'ERRO')
                    <span class="label label-danger">ERRO NA EMISSÃO</span>
                  @else
                    <span class="label label-default">{{ $status }}</span>
                  @endif
                </p>
                @if($status == 'ERRO' && $emissao->mensagem_erro)
                   <p class="text-danger"><small><b>Motivo:</b> {{ $emissao->mensagem_erro }}</small></p>
                @endif
              </div>
              <div class="col-sm-4">
                 @if($emissao->numero_nf)
                   <p><b>Número NF:</b> <code>{{ $emissao->numero_nf }}</code></p>
                 @endif
              </div>
              <div class="col-sm-4 text-right">
                <div class="btn-group">
                  @if($status == 'PROCESSANDO')
                    <button type="button" class="btn btn-sm btn-info btn-sync" data-id="{{ $emissao->id }}">
                      <i class="fa fa-refresh"></i> Sincronizar Status
                    </button>
                  @endif

                  @if($emissao->pdf_url)
                    <a href="{{ route('nfse.download.pdf', $emissao->id) }}" class="btn btn-sm btn-danger" target="_blank">
                      <i class="fa fa-file-pdf-o"></i> Baixar PDF
                    </a>
                  @endif

                  @if($emissao->xml_url)
                    <a href="{{ route('nfse.download.xml', $emissao->id) }}" class="btn btn-sm btn-warning" target="_blank">
                      <i class="fa fa-file-code-o"></i> Baixar XML
                    </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Código</th>
              <th>Loja</th>
              <th>Cidade</th>
              <th>CNPJ</th>
              <th>Serviço</th>
              <th>Valor</th>
              <th>NF</th>
              @if($link == 'on')
                <th>Download</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @foreach($faturamentoItens as $i)
              <tr>
                <td>{{$i->detalhes->unidade->codigo}}</td>
                <td><a href="{{route('unidades.show', $i->detalhes->unidade->id)}}"
                    class="no-print">{{$i->detalhes->unidade->nomeFantasia}}</a><span
                    class="visible-print">{{$i->detalhes->unidade->nomeFantasia}}</span></td>
                <td>{{$i->detalhes->unidade->cidade}}/{{$i->detalhes->unidade->uf}}</td>
                <td>
                  @php echo App\Http\Controllers\FaturamentoController::formatCnpjCpf($i->detalhes->unidade->cnpj); @endphp
                </td>
                <td>{{$i->detalhes->nome}}
                  <p><a href="{{route('servicos.show', $i->servico_id)}}"
                      class="btn btn-xs btn-success no-print">{{$i->detalhes->os}}</a></p>
                </td>
                <td>R$ {{number_format($i->valorFaturado, 2, '.', ',')}}</td>
                <td>{{$i->detalhes->nf}}</td>
                @if($link == 'on')
                  <td><a href="{{ route('servico.downloadFile', ['servico_id' => $i->servico_id, 'tipo' => 'licenca']) }}"
                      class="btn btn-xs btn-warning no-print" target="_blank" rel="external">Ver Licença</a>
                    <a href="{{ route('servico.downloadFile', ['servico_id' => $i->servico_id, 'tipo' => 'licenca']) }}"
                      class="btn btn-xs btn-warning visible-print" target="_blank">Ver Licença</a>
                  </td>
                @endif
              </tr>
            @endforeach
          </tbody>

        </table>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
      <!-- accepted payments column -->
      <div class="col-xs-6">

      </div>
      <!-- /.col -->
      <div class="col-xs-6">
        <p class="pull-right lead"><b>Total: </b>R$ {{number_format($totalFaturamento, 2, '.', ',')}}</p>

      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    <div class="col-xs-8" style="margin-top: 100px;">
      <p>__________________________________________________________________</p>
      @if($dadosCastro)
        <p><b>{{$dadosCastro->razaoSocial}}</b></p>
        <p>CNPJ: {{$dadosCastro->cnpj}}</p>
      @else
        <p><b>Empresa/Tomador não vinculado</b></p>
      @endif

    </div>
    <!-- this row will not appear when printing -->
    <div class="row no-print">
      <div class="col-xs-12">

      </div>
    </div>
  </section>

  <div class="modal fade" id="edit-faturamento" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
          <h4 class="modal-title">Editar faturamento</h4>
        </div>
        <div class="modal-body">

          {!! Form::open(['route' => 'faturamento.editarFaturamento']) !!}


          {!! Form::hidden('faturamentoID', $id) !!}

          {!! Form::label('nome', 'Descrição', array('class' => 'control-label')) !!}
          {!! Form::text('nome', $descricao, ['class' => 'form-control', 'id' => 'nome']) !!}


          {!! Form::label('obs', 'Referência', array('class' => 'control-label')) !!}
          {!! Form::text('obs', $obs, ['class' => 'form-control', 'id' => 'obs']) !!}

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>

          {!! Form::close() !!}
        </div>
      </div>

    </div>

  </div>


@endsection