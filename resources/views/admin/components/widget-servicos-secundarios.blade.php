<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Serviços secundários</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin" id="servicos">
                  <thead>
                  <tr>
                    
                    <th>Serviço</th>
                    <th>Status</th>
                  
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($servicos->where('tipo','secundario') as $servico)
                  <tr>
                    
                    <td><a href="{{route('servicos.show',$servico->id)}}">{{$servico->os}} | {{$servico->nome}}</a></td>
                    <td>
                      @switch($servico->situacao)

                        @case('andamento')
                            <span class="label label-warning">Andamento</span>
                        @break

                        @case('finalizado')
                            <span class="label label-success">Finalizado</span>
                        @break

                      @endswitch
                    </td>
                    
                    
                  </tr>
                  @endforeach
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="{{route('servicos.create', ['id'=>$dados->id,'t'=>substr($route, 0,7)])}}" class="btn btn-sm btn-info btn-flat pull-left"><span class="glyphicon glyphicon-plus-sign"></span> Novo Serviço</a>
              <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">Todas os Serviços</a>
            </div>
            <!-- /.box-footer -->
          </div>


