@extends('adminlte::page')

@section('css')

<style>
  @media print {
  a[href]:after {
    content: none !important;
  }
}
</style>


  
@endsection

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
        <p><b>RELATÓRIO DE FATURAMENTO: </b>{{$descricao}} <span><button type="button" class="btn btn-xs btn-info no-print" data-toggle="modal" data-target="#edit-faturamento"><i class="fa fa-edit"></i> Editar</button></span></p>
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
                <td>{{$i->detalhes->unidade->codigo}}</td>
                  <td><a href="{{route('unidades.show',$i->detalhes->unidade->id)}}" class="no-print">{{$i->detalhes->unidade->nomeFantasia}}</a><span class="visible-print">{{$i->detalhes->unidade->nomeFantasia}}</span></td>
                  <td>{{$i->detalhes->unidade->cidade}}/{{$i->detalhes->unidade->uf}}</td>
                  <td>@php echo App\Http\Controllers\FaturamentoController::formatCnpjCpf($i->detalhes->unidade->cnpj); @endphp</td>
                  <td>{{$i->detalhes->nome}}<p><a href="{{route('servicos.show',$i->servico_id)}}" class="btn btn-xs btn-success no-print">{{$i->detalhes->os}}</a></p></td>
                  <td>R$ {{number_format($i->valorFaturado,2,'.',',')}}</td>
                  <td>{{$i->detalhes->nf}}</td>
                  @if($link=='on')
                  <td><a href="{{ route('servico.downloadFile', ['servico_id'=> $i->servico_id,'tipo'=>'licenca']) }}" class="btn btn-xs btn-warning no-print" target="_blank" rel="external">Ver Licença</a>
                    <a href="{{ route('servico.downloadFile', ['servico_id'=> $i->servico_id,'tipo'=>'licenca']) }}" class="btn btn-xs btn-warning visible-print" target="_blank">Ver Licença</a></td>
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

  <div class="modal fade" id="edit-faturamento" style="display: none;">
    <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">×</span></button>
    <h4 class="modal-title">Editar faturamento</h4>
    </div>
    <div class="modal-body">

      {!! Form::open(['route'=>'faturamento.editarFaturamento']) !!}


      {!! Form::hidden('faturamentoID', $id) !!}

      {!! Form::label('nome', 'Descrição', array('class'=>'control-label')) !!}
      {!! Form::text('nome', $descricao, ['class'=>'form-control','id'=>'nome']) !!}
     

      {!! Form::label('obs', 'Referência', array('class'=>'control-label')) !!}
      {!! Form::text('obs', $obs, ['class'=>'form-control','id'=>'obs']) !!}

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