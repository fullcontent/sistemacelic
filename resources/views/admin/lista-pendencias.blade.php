@extends('adminlte::page')

@section('content')


<div class="row">
    <div class="col-lg-12 col-xs-12">
        
        <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">{{$title}}</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table id="lista-pendencias" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Empresa</th>
                    <th>Cod.</th>
                    <th>Unidade</th>
                    <th>Serviço</th>
                    <th>Pendência</th>
                    <th>Data</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($pendencias->where('status','pendente') as $p)
                  <tr>
                  <td><a href="{{route('empresas.show',$p->servico['unidade']['empresa']['id'])}}">{{$p->servico['unidade']['empresa']['nomeFantasia']}}</a></td>
                    <td><a href="{{route('servicos.show',$p->servico_id)}}">{{$p->servico['unidade']['codigo']}}</a></td>
                    <td><a href="{{route('servicos.show',$p->servico_id)}}">{{$p->servico['unidade']['nomeFantasia']}}</a></td>
                    <td><a href="{{route('servicos.show',$p->servico_id)}}">{{$p->servico['nome']}}</a></td>
                    <td><a href="{{route('servicos.show',$p->servico_id)}}">{{$p->pendencia}}</a></td>
                    <td><a href="{{route('servicos.show',$p->servico_id)}}">
                    @switch($p->vencimento)
                        
                        @case($p->vencimento > date('Y-m-d'))
                            <span class="label label-success">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</span>
                        @break

                        @case($p->vencimento < date('Y-m-d'))
                            <span class="label label-danger">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</span>
                        @break

                        @case($p->vencimento == date('Y-m-d'))
                            <span class="label label-warning">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</span>
                        @break





                  @endswitch</a></td>
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

    </div>

</div>





@endsection

