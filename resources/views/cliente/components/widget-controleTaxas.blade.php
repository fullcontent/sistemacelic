<div class="box box-primary  collapsed-box">
  <div class="box-header with-border">
    <a href="#" data-widget="collapse"><h3 class="box-title">Controle de Taxas</h3></a>

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
                    @foreach($servicos->where('tipo','controleTaxas') as $servico)
                  <tr>
                    
                    <td><a href="{{route('cliente.servico.show',$servico->id)}}">{{$servico->os}} | {{$servico->nome}}</a></td>
                    <td>
                      @switch($servico->situacao)

                      @case('andamento')
                          @if($servico->licenca_validade >= date('Y-m-d'))
                  
                           <a href="{{route('cliente.servico.show',$servico->id)}}" class="btn btn-xs btn-success">Andamento</a>
                          @elseif($servico->licenca_validade < date('Y-m-d'))
                          <a href="{{route('cliente.servico.show',$servico->id)}}" class="btn btn-xs btn-danger">Andamento</a>

                        @endif
                        @break

                      @case('finalizado')

                        @if($servico->licenca_validade >= date('Y-m-d'))
                  
                           <a href="{{route('cliente.servico.show',$servico->id)}}" class="btn btn-xs btn-success">Finalizado</a>
                          @elseif($servico->licenca_validade < date('Y-m-d'))
                          <a href="{{route('cliente.servico.show',$servico->id)}}" class="btn btn-xs btn-danger">Finalizado</a>

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
             
            </div>
            <!-- /.box-footer -->
          </div>


