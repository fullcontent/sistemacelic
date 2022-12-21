<div class="box box-primary  collapsed-box">
            <div class="box-header with-border">
              <a href="#" data-widget="collapse"><h3 class="box-title">Projetos e Laudos</h3></a>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin" id="projetosLaudos">
                  <thead>
                  <tr>
                    
                    <th>Serviço</th>
                    <th>Status</th>
                    <th></th>
                    <th></th>
                  
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($servicos->where('tipo','projetosLaudos') as $servico)
                  <tr>
                    
                    <td><a href="{{route('servicos.show',$servico->id)}}">{{$servico->os}} | {{$servico->nome}}</a> @if($servico->servicoPrincipal) <small class="label bg-red">S</small>@endif</td>
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
                    <td>@if($servico->licenca_anexo) <a href="{{ route('servico.downloadFile', ['servico_id'=> $servico->id,'tipo'=>'licenca']) }}" class="btn btn-xs btn-warning" target="_blank">Ver Licença</a> @endif</td>
                    <td><a href="{{route('servico.delete', $servico->id)}}" class="confirmation danger" alt="Excluir serviço"> <i class="glyphicon glyphicon-trash
"></i></a></td>
                    
                    
                  </tr>
                  @endforeach
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="{{route('servicos.create', ['id'=>$dados->id,'t'=>substr($route, 0,7),'tipoServico'=>'projetosLaudos'])}}" class="btn btn-sm btn-info btn-flat pull-left"><span class="glyphicon glyphicon-plus-sign"></span> Novo Serviço</a>
              <button class="btn btn-sm btn-default btn-flat pull-right" id="projetosLaudos_btn">Todos os Serviços</button>
            </div>
            <!-- /.box-footer -->
          </div>


