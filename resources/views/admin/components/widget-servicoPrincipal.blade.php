<div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-clone"></i> Serviço Principal</h3>

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
                   @foreach(\App\Models\Servico::where('id',$servico->servicoPrincipal)->get() as $s2)
                    <tr>
                        <td><a href="{{route('servicos.show', $s2->id)}}">{{$s2->nome}}</a></td>
                        <td><a href="{{route('servicos.show', $s2->id)}}">{{$s2->os}}</a></td>
                        <td>{{\Carbon\Carbon::parse($s2->licenca_validade)->format('d/m/Y')}}</td>
                        <td>@switch($s2->situacao)

@case('andamento')

  @if(($s2->licenca_validade >= date('Y-m-d')) && ($s2->tipo == 'licencaOperacao'))
      
      <button type="button" class="btn btn-xs btn-success">Andamento</button>
        @elseif(($s2->licenca_validade < date('Y-m-d'))&& ($s2->tipo == 'licencaOperacao'))
        <button type="button" class="btn btn-xs btn-danger">Andamento</button>
  @elseif($s2->tipo == 'nRenovaveis')
      <button type="button" class="btn btn-xs btn-warning">Andamento</button>

    @endif

  


    @break

@case('finalizado')

    @if(($s2->licenca_validade >= date('Y-m-d')) && ($s2->tipo == 'licencaOperacao'))
      
      <button type="button" class="btn btn-xs btn-success">Finalizado</button>
        @elseif(($s2->licenca_validade < date('Y-m-d'))&& ($s2->tipo == 'licencaOperacao'))
        <button type="button" class="btn btn-xs btn-danger">Finalizado</button>

    @elseif($s2->tipo == 'nRenovaveis')
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
                        <td>{{$s2->responsavel->name}}</td>
                    </tr>
                   @endforeach
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
           
          </div>