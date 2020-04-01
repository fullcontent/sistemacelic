<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Serviços primários</h3>

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
                    @foreach($servicos->where('tipo','primario')->where('situacao','!=','arquivado') as $servico)
                  <tr>
                    
                    <td><a href="{{route('servicos.show',$servico->id)}}">{{$servico->os}} | {{$servico->nome}}</a></td>
                    <td>
                      @switch($servico->situacao)

                      @case('andamento')
                          @if($servico->licenca_validade >= date('Y-m-d'))
                  
                           <a href="{{route('servicos.show',$servico->id)}}" class="btn btn-xs btn-success">Andamento</a>
                          @elseif($servico->licenca_validade < date('Y-m-d'))
                          <a href="{{route('servicos.show',$servico->id)}}" class="btn btn-xs btn-danger">Andamento</a>

                        @endif
                        @break

                      @case('finalizado')

                          @if($servico->licenca_validade >= date('Y-m-d'))

                          <a href="{{route('servicos.show',$servico->id)}}" class="btn btn-xs btn-success">Finalizado</a>
                          @elseif($servico->licenca_validade < date('Y-m-d'))
                          <a href="{{route('servicos.show',$servico->id)}}" class="btn btn-xs btn-danger">Finalizado</a>
                          @endif

                          @break

                      @case('arquivado')
                <button type="button" class="btn btn-xs btn-default">Arquivado</button>
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


