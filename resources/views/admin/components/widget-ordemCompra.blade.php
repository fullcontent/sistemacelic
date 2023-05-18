<div class="box box-info ">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-credit-card"></i> Ordens de Compra</h3>

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
                    <th>Prestador</th>
                    <th>Valor</th>
                    <th>Situação</th>
                    <th>Data Pgto</th>
                    <th></th>
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                   
                    @foreach($ordensCompra as $oc)

                    <tr>
                      <td>{{$oc->prestador->nome}}</td>
                      <td>{{$oc->valorServico}}</td>
                      <td> @if($oc->situacaoPagamento->count())
                        <span class="btn btn-warning">Em aberto {{$oc->pagamentos->where('situacao','pago')->count()}}/{{$oc->pagamentos->count()}}</span>
                        
                        @else
                        <span class="btn btn-success">Pago</span>
                        
                        @endif</td>
                      <td>{{$oc->dataPagamento}}</td>
                      <td>
                      @foreach($oc->pagamentos as $p)
                      <a href="{{ url("public/uploads/$p->comprovante") }}" class="btn btn-xs btn-success" target="_blank">Comprovante</a>
                      @endforeach
                      </td>
                      <td></td>
                    </tr>

                    @endforeach
                  
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              @if(Request::is('admin/servicos/*'))
              <a href="{{route('ordemCompra.criar', $servico->id)}}" class="btn btn-sm btn-info btn-flat pull-left"><span class="glyphicon glyphicon-plus-sign"></span> Nova ordem de Compra</a>
              @endif

              
             
              
            </div>
            <!-- /.box-footer -->
          </div>