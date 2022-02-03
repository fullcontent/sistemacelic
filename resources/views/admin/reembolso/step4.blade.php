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
        <p><b>Reembolso </b>{{$descricao}}</p>
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
            <th>Cod.</th>
			  	<th>Unidade</th>
				<th>Serviço</th>
				<th>Taxa</th>
				<th>Solicitante</th>
				<th>Valor</th>
				<th>Vcto.</th>
				<th>Pgto.</th>	
          </tr>
          </thead>
          <tbody>
              @foreach($reembolsoItens as $s)
              <tr>
                <td>{{$s->unidade->codigo}}</td>
								<td>{{$s->unidade->nomeFantasia}}</td>
								<td>{{$s->servico->nome}}</td>
								<td>{{$s->nome}}</td>
								<td>{{$s->servico->solicitante}}</td>
								<td>R$ {{number_format($s->valor,2,'.',',')}}</td>
								<td>{{ \Carbon\Carbon::parse($s->vencimento)->format('d/m/Y')}}</td>
								<td>{{ \Carbon\Carbon::parse($s->pagamento)->format('d/m/Y')}}</td>
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
      <p class="pull-right lead"><b>Total: </b>R$ {{number_format($totalReembolso,2,'.',',')}}</p>

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

  <section class="invoice" style="page-break-before: always; padding:20px;">

    <div class="row">
      <div class="col-xs-12">
        
          <img src="{{asset('img/logoCastro.png')}}" alt="" width="300">
          <small class="pull-right">Data: {{date('d/m/Y')}}</small>
        
      </div>
      <!-- /.col -->
    </div>

    <div class="row">
      <div class="col-xs-12 text-right" style="line-height: 10px;">
        <p>Rua novecentos e Um, 400 | Sala 203</p>
        <p>Balneário Camboriú | Santa Catarina | CEP: 88.330-725</p>
        <p>Telefone: 47 3334-2927</p>
        <p>contato@castroli.com.br | www.castroli.com.br</p>
      </div>
    </div>

   

    <div class="row">
      <div class="col-xs-12 text-right">

        <h2 class="page-header">
        <p>Recibo</p>
        <p>R$ {{number_format($totalReembolso,2,'.',',')}}</p>
        </h2>
    </div>

    <div class="row">

      <div class="col-xs-6 text-left" style="line-height: 10px;">

        <p style="margin-bottom: 20px">Bal. Camboriú, {{$data}}</p>
      
        <p>{{$empresa->nomeFantasia}}</p>
        <p>{{$empresa->endereco}}</p>
        <p>{{$empresa->bairro}}</p>
        <p>{{$empresa->cep}}</p>
        <p>{{$empresa->telefone}}</p>

      </div>

    </div>

    <div class="row">
        <div class="text-center">
          <h3>RECIBO</h3>
          <p style="padding: 20px">Recebemos da {{$empresa->razaoSocial}} a importância de R$ {{number_format($totalReembolso,2,'.',',')}} referente ao pagamento de taxas para o processo de legalização, conforme demonstrativo comprovante em anexo.</p>
        </div>

    </div>

    <div class="row">
      <div class="col-xs-12 text-left">
        <p>CAIXA ECONÔMICA FEDERAL</p>
        <p><b>Agência: </b>0921</p>
        <p><b>Conta corrente: </b>6992-4</p>
        <p><b>Castro Empresarial Serviços Administrativos LTDA ME</p>
        <p><b>CNPJ: </b>27.352.308/0001-52</p>
      </div>
    </div>
    <div class="col-xs-8" style="margin-top: 100px;">
      <p>__________________________________________________________________</p>
      <p><b>CASTRO EMPRESARIAL - CONSULTORIA E LEGALIZAÇÃO IMOBILIÁRIA
      </b></p>
      <p>CNPJ: 27.352.308/0001-52
      </p>

    </div>
    </div>
  </section>

@endsection


