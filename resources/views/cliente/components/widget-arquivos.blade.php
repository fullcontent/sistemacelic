<div class="box box-info">
            <div class="box-header with-border">
              <a href="#" data-widget="collapse"><h3 class="box-title">Arquivo digital</h3></a>

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
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                   @foreach($dados->arquivos as $a)
                   <tr>
                   	<td>{{$a->nome}}</td>
                     <td><a href="{{ route('arquivo.download',$a->id) }}" class="btn btn-xs btn-default" target="_self">Download</a></td>
                   </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
          </div>