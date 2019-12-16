<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Taxas</h3>

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
                  </tr>
                  @endforeach
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">Todas as Taxas</a>
            </div>
            <!-- /.box-footer -->
          </div>