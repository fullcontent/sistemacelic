<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Taxas em aberto</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
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
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Situacao</th>
                    <th>O.S.</th>
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($taxas->take(5) as $taxa)
                  <tr>
                    <td>{{$taxa->nome}}</td>
                    <td>R$ {{$taxa->valor}}</td>
                    <td><span class="label label-success">{{ \Carbon\Carbon::parse($taxa->vencimento)->format('d/m/Y')}}
</span></td>
                    <td>{{$taxa->situacao}}</td>
                    <td>{{$taxa->servico->os}}</td>
                    <td>
        @unless ( empty($taxa->boleto))
        
        <a href="{{ url("uploads/$taxa->boleto") }}" class="btn btn-xs btn-warning" target="_blank">Ver Boleto</a>
        @endunless
        @unless ( empty($taxa->comprovante) )
        
        <a href="{{ url("uploads/$taxa->comprovante") }}" class="btn btn-xs btn-success" target="_blank">Ver Comprovante</a>
        @endunless</td>

                  </tr>
                  @endforeach
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="{{route('taxas.create')}}" class="btn btn-sm btn-info btn-flat pull-left"><span class="glyphicon glyphicon-plus-sign"></span> Nova Taxa</a>
              
            </div>
            <!-- /.box-footer -->
          </div>