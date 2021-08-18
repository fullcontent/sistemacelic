<div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-clone"></i> Sub Serviços</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Nome</th>
                    <th>O.S.</th>
                    <th>Vencimento</th>
                    <th>Situação</th>
                    <th>Responsável</th>
                  </tr>
                  </thead>
                  <tbody>
                   @foreach($servico->subServicos as $servico)
                    <tr>
                        <td><a href="{{route('servicos.show', $servico->id)}}">{{$servico->nome}}</a></td>
                        <td><a href="{{route('servicos.show', $servico->id)}}">{{$servico->os}}</a></td>
                        <td>{{\Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y')}}</td>
                        <td>@switch($servico->situacao)

@case('andamento')

  @if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'licencaOperacao'))
      
      <button type="button" class="btn btn-xs btn-success">Andamento</button>
        @elseif(($servico->licenca_validade < date('Y-m-d'))&& ($servico->tipo == 'licencaOperacao'))
        <button type="button" class="btn btn-xs btn-danger">Andamento</button>
  @elseif($servico->tipo == 'nRenovaveis')
      <button type="button" class="btn btn-xs btn-warning">Andamento</button>

    @endif

  


    @break

@case('finalizado')

    @if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'licencaOperacao'))
      
      <button type="button" class="btn btn-xs btn-success">Finalizado</button>
        @elseif(($servico->licenca_validade < date('Y-m-d'))&& ($servico->tipo == 'licencaOperacao'))
        <button type="button" class="btn btn-xs btn-danger">Finalizado</button>

    @elseif($servico->tipo == 'nRenovaveis')
      <button type="button" class="btn btn-xs btn-warning">Finalizado</button>

    @endif
  
    @break

@case('arquivado')
  <button type="button" class="btn btn-xs btn-default">Arquivado</button>
    @break
@case('standBy')
  <button type="button" class="btn btn-xs btn-gray">Stand By</button>
    @break

@endswitch</td>
                        <td>{{$servico->responsavel->name}}</td>
                    </tr>
                   @endforeach
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
           
          </div>