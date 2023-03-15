<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reembolso</title>
  
  <style>
      
    body {
    font-family: Arial,sans-serif;
    font-weight: 400;
    font-size: 12px;
    margin: 0;

    }
    
.row {
    margin-right: -15px;
    margin-left: -15px;
}

div {
    display: block;
}
img {
    vertical-align: middle;
}
img {
    border: 0;
}

.page-header {
    margin: 10px 0 20px 0;
    font-size: 22px;
}
.content {
    min-height: 250px;
    
    margin-right: auto;
    margin-left: auto;
   
}
.invoice {
    position: relative;
    background: #fff;
    /* border: 1px solid #f4f4f4; */
    padding: 0px;
    margin: 10px 10px;
}
article, aside, details, figcaption, figure, footer, header, hgroup, main, menu, nav, section, summary {
    display: block;
}



.page-header>small {
    color: #666;
    display: block;
    margin-top: 5px;
}
.pull-right {
    float: right!important;
}
.h1 .small, .h1 small, .h2 .small, .h2 small, .h3 .small, .h3 small, h1 .small, h1 small, h2 .small, h2 small, h3 .small, h3 small {
    font-size: 65%;
}
.table-responsive {
    min-height: .01%;
    
}
.col-xs-12 {
    width: 100%;
}
.col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
    float: left;
}

.table-bordered {
    border: 1px solid #f4f4f4;
}
thead {
    display: table-header-group;
    vertical-align: middle;
    border-color: inherit;
}
tr {
    display: table-row;
    vertical-align: inherit;
    border-color: inherit;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 8px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}

.col-xs-12 {
    width: 100%;
}
.col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
    float: left;
}

.col-xs-8 {
    width: 66.66666667%;
}

.col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
    position: relative;
    min-height: 1px;
    padding-right: 1px;
    padding-left: 1px;
}
.text-right {
    text-align: right;
}

.col-xs-6 {
    width: 50%;
}
.col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
    float: left;
}
.col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
    position: relative;
    min-height: 1px;
    padding-right: 15px;
    padding-left: 15px;
}
.text-left {
    text-align: left;
}

.col-xs-6 {
    width: 50%;
}
.col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
    float: left;
}

.col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
    position: relative;
    min-height: 1px;
    padding-right: 15px;
    padding-left: 15px;
}

.pull-right {
    float: right!important;
}

.lead {
    margin-bottom: 20px;
    font-size: 16px;
    font-weight: 300;
    line-height: 1.4;
}
p {
    margin: 0 0 10px;
}
.h3, h3 {
    font-size: 24px;
}
.h1, .h2, .h3, h1, h2, h3 {
    margin-top: 20px;
    margin-bottom: 10px;
}
.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    font-family: inherit;
    font-weight: 500;
    line-height: 1.1;
    color: inherit;
}
h3 {
    display: block;
    font-size: 1.17em;
   
    font-weight: bold;
}

.text-center {
    text-align: center;
    clear: both;
}
.assinatura{
    display:block;
    clear: both;
}

.total{
    clear: both;
    display: inline;
    text-align: right;
}
.empresa{
    display:block;
    clear:both;
    
}


  </style>
</head>
  <body>
  

  <div class="content">
  <section class="invoice">

    <div class="row">
      <div class="col-xs-12">
        
          <img src="http://sistemacelic.net/img/logoCastro.png" alt="" width="300">
         
        
      </div>
      <div class="col-xs-12 text-right" style="line-height: 10px;">
        <p>Rua novecentos e Um, 400 | Sala 405</p>
        <p>Balneário Camboriú | Santa Catarina | CEP: 88.330-725</p>
        <p>Telefone: 47 3334-2927</p>
        <p>contato@castroli.com.br | www.castroli.com.br</p>
      </div>
      <!-- /.col -->
    </div>

    

   

    <div class="row">
      <div class="col-xs-12 text-right">

        <h2 class="page-header">
        <p>Recibo</p>
        <p>R$ {{number_format($totalReembolso,2,'.',',')}}</p>
        </h2>
    </div>

    <div class="row">
    
        <div class="empresa">
        
      <div class="col-xs-6 text-left" style="line-height: 10px;">
        <h3>N° {{$id}}</h3>

        <p style="margin-bottom: 20px; padding-top:20px;">Bal. Camboriú, {{ \Carbon\Carbon::parse($data)->isoFormat("D, MMMM, YYYY") }}</p>
      
        <p>{{$empresa->razaoSocial}}</p>
        <p>{{$empresa->endereco}},{{$empresa->numero}}</p>
        <p>{{$empresa->bairro}} {{$empresa->cidade}}/{{$empresa->uf}}</p>
        <p>{{$empresa->cep}}</p>
        <p>{{$empresa->telefone}}</p>

      </div>
      </div>
    </div>

    <div class="row">
        <div class="text-center">
          <h3>RECIBO</h3>
          <p style="padding: 20px; text-align:justify-all;">Recebemos da {{$empresa->razaoSocial}} a importância de R$ {{number_format($totalReembolso,2,'.',',')}} referente ao pagamento de taxas para o processo de legalização, conforme demonstrativo comprovante em anexo.</p>
        </div>

    </div>

    <div class="row" style="padding-top:20px;">
      <div class="col-xs-12 text-left">
        <p>CHAVE PIX: 27352308000152</p>
        <p>Caixa Econômica Federal</p>
        <p>Agência: 0921</p>
        <p>Conta corrente: 6992-4</p>
        <p>Castro Empresarial Serviços Administrativos LTDA ME</p>
        <p>CNPJ: 27.352.308/0001-52</p>
      </div>
    </div>

    <div class="row">
    <div class="assinatura">
    <div class="col-xs-8" style="margin-top: 100px;">
      <p>__________________________________________________________________</p>
      <p>Castro Empresarial - Consultoria e Legalização Imobiliária
      </p>
      <p>CNPJ: 27.352.308/0001-52
      </p>

    </div>
    </div>
    </div>
    
    
    
  </section>
  </div>



<div class="content">
<section class="invoice" style="page-break-before: always; padding:0px;">
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
    <h3 class="text-center">Demonstrativo de taxas pagas</h3>
      <div class="col-sm-6">
        <p>Reembolso {{$descricao}}</p>
        <p>Referência: {{$obs}}</p>
      </div>
      
    </div>
    <!-- /.row -->

    <!-- Table row -->
    <div class="row" style="padding:0px;">
      <div class="col-xs-12 table-responsive">
        <table class="table table-bordered">
          <thead>
          <tr>
            <th>#</th>
            <th>Cod.</th>
            <th>Unidade</th>
            <th>Serviço</th>
            <th>Taxa</th>
            <th>Solicitante</th>
            <th width="13%">Valor</th>
            <th>Vcto.</th>
            <th>Pgto.</th>
            
          </tr>
          </thead>
          <tbody style="font-size: 11px;">
              @foreach($reembolsoItens as $key => $s)
              <tr>
                <td>{{$key+1}}</td>
                <td>{{$s->taxa->unidade->codigo}}</td>
								<td>{{$s->taxa->unidade->nomeFantasia}}</td>
								<td>{{$s->taxa->servico->nome}}</td>
								<td>{{$s->taxa->nome}}</td>
								<td>@if(!is_numeric($s->taxa->servico->solicitante))
                  {{$s->taxa->servico->solicitante}}
                  @else
                  {{\App\Models\Solicitante::where('id',$s->taxa->servico->solicitante)->value('nome')}}
                  @endif</td>
								<td>R$ {{number_format($s->taxa->valor,2,'.',',')}}</td>
								<td>{{ \Carbon\Carbon::parse($s->taxa->vencimento)->format('d/m/Y')}}</td>
								<td>{{ \Carbon\Carbon::parse($s->taxa->pagamento)->format('d/m/Y')}}</td>
              </tr>
              @endforeach
          </tbody>
          
        </table>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
     
      <div class="total">
      
      
      <div class="col-xs-12">
      <p class="pull-right lead">Total: R$ {{number_format($totalReembolso,2,'.',',')}}</p>

      </div>
      </div>
      <!-- /.col -->
    </div>

    
  </section>
  </div>



  </body>
</html>
