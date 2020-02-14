<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Taxa</h3>

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
                    @foreach($taxas ?? '' as $taxa)
                  <tr>
                    <td><a href="{{route('taxas.show',$taxa->id)}}">{{$taxa->nome}}</a></td>
                    <td>R$ {{$taxa->valor}}</td>
                    <td><span class="label label-default">{{ \Carbon\Carbon::parse($taxa->vencimento)->format('d/m/Y')}}
</span></td>
                    <td>
                      @switch($taxa->vencimento)

                        @case($taxa->vencimento >= date('Y-m-d'))
                            @if($taxa->comprovante)
                              <span class="label label-success">Pago</span>
                            @else
                            
                            <span class="label label-warning">Aberto</span>
                          @endif
                          
                        @break
                      
                        @case($taxa->vencimento < date('Y-m-d'))
                            @if($taxa->comprovante)
                                <span class="label label-success">Pago</span>
                            @else
                            <span class="label label-danger">Vencida</span>
                            @endif
                        @break
                      @endswitch
                    </td>
                    <td>{{$taxa->servico->os}}</td>
                    <td>
        @if(empty($taxa->comprovante))
        @unless ( empty($taxa->boleto))
        
        <a href="{{ url("uploads/$taxa->boleto") }}" class="btn btn-xs btn-warning" target="_blank">Ver Boleto</a>
        @endunless
        @endif
        
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
           
              
            </div>
            <!-- /.box-footer -->
          </div>