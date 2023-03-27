@extends('adminlte::page')


@section('content')

<section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h2 class="page-header">
          <img src="http://sistemacelic.net/img/logoCastro.png" alt="" width="300">
          <small class="pull-right">Data: {{date('d/m/Y')}}</small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
      <div class="col-sm-6">
        <p><b>RELATÓRIO DE FATURAMENTO </b>{{$descricao}}</p>
        <p><b>Referência: </b>{{$obs}}</p>
      </div>
      
    </div>
    <!-- /.row -->

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
            @if($link=='on')
            <th>Download</th>
            @endif
          </tr>
          </thead>
          <tbody>
              @foreach($faturamentoItens as $i)
              <tr>
                <td>{{$i->unidade->codigo}}</td>
                  <td>{{$i->unidade->nomeFantasia}}</td>
                  <td>{{$i->unidade->cidade}}/{{$i->unidade->uf}}</td>
                  <td>@php echo App\Http\Controllers\FaturamentoController::formatCnpjCpf($i->unidade->cnpj); @endphp</td>
                  <td>{{$i->nome}}</td>
                  <td>R$ {{number_format($i->financeiro->valorFaturar,2,'.',',')}}</td>
                  <td>{{$i->nf}}</td>
                  @if($link=='on')
                  <td>
                    @if($i->licenca_anexo)
                    <a href="{{ route('servico.downloadFile', ['servico_id'=> $i->id,'tipo'=>'licenca']) }}" class="btn btn-xs btn-warning" target="_blank" rel="external">Ver Licença</a>
                    @else
                    Não há documentos 
                    @endif
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
      <p class="pull-right lead"><b>Total: </b>R$ {{number_format($totalFaturamento,2,'.',',')}}</p>

      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    <div class="col-xs-8" style="margin-top: 100px;">
        <p>__________________________________________________________________</p>
        <p><b>{{$dadosCastro->razaoSocial}}
        </b></p>
        <p>CNPJ: {{$dadosCastro->cnpj}}
        </p>

      </div>
    <!-- this row will not appear when printing -->
    <div class="row no-print">
      <div class="col-xs-12">
        
      </div>
    </div>
  </section>

@endsection