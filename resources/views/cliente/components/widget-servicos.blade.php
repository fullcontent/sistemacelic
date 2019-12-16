<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Serviços</h3>

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
                    <th>OS</th>
                    <th>Serviço</th>
                    <th>Status</th>
                    <th>Obs</th>
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($servicos as $servico)
                  <tr>
                    <td>{{$servico->os}}</td>
                    <td>{{$servico->nome}}</td>
                    <td><span class="label label-success">{{$servico->situacao}}</span></td>
                    <td>{{$servico->observacoes}}</td>
                    <td><a href="{{route('servico.show',$servico->id)}}">Detalhes</a></td>
                  </tr>
                  @endforeach
                  
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            
            <!-- /.box-footer -->
          </div>