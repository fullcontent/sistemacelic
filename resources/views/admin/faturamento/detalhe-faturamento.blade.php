@extends('adminlte::page')


@section('content')

<section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h2 class="page-header">
          <img src="http://sistemacelic.net/img/logoCastro.png" alt="" width="300">
          <small class="pull-right">Data: {{$data}}</small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
      <div class="col-sm-6">
        <p><b>RELATÓRIO DE FATURAMENTO: </b>{{$descricao}}</p>
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
          </tr>
          </thead>
          <tbody>
              @foreach($faturamentoItens as $i)
              <tr>
                <td>{{$i->detalhes->unidade->codigo}}</td>
                  <td>{{$i->detalhes->unidade->nomeFantasia}}</td>
                  <td>{{$i->detalhes->unidade->cidade}}/{{$i->detalhes->unidade->uf}}</td>
                  <td>@php echo App\Http\Controllers\FaturamentoController::formatCnpjCpf($i->detalhes->unidade->cnpj); @endphp</td>
                  <td>{{$i->detalhes->nome}}</td>
                  <td>R$ {{number_format($i->valorFaturado,2,'.',',')}}</td>
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
        <p><b>CASTRO EMPRESARIAL - CONSULTORIA E LEGALIZAÇÃO IMOBILIÁRIA
        </b></p>
        <p>CNPJ: 27.352.308/0001-52
        </p>

      </div>
    <!-- this row will not appear when printing -->
    <div class="row no-print">
      <div class="col-xs-12">
        
      </div>
    </div>
  </section>

@endsection