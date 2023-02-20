<div class="box box-info collapsed-box">
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
            <div class="box-footer clearfix">
              <button type="button" class="btn btn-info" data-toggle="modal" data-target="#cadastro-arquivo">
                <span class="glyphicon glyphicon-plus-sign"></span> Adicionar
              </button>
              
              
              
            </div>
            <!-- /.box-footer -->

            <div class="modal fade" id="cadastro-arquivo">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Adicionar novo arquivo</h4>
              </div>
              <div class="modal-body">
                
					{!! Form::open(['route'=>'cliente.arquivo.anexar','enctype'=>'multipart/form-data']) !!}
						
						<div class="form-group">
							{!! Form::label('nome', 'Nome', array('class'=>'control-label')) !!}
							{!! Form::text('nome', null, ['class'=>'form-control','id'=>'email']) !!}

						</div>

						<div class="form-group">
							 {!! Form::label('arquivo', 'Arquivo', array('class'=>'control-label')) !!}
        				{!! Form::file('arquivo', null, ['class'=>'form-control','id'=>'arquivo']) !!}

						</div>
              
              
              {!! Form::hidden('unidade_id', $dados->id) !!}

              

              {!! Form::hidden('route',\Request::route()->getName()) !!}
             
						
					

              </div>
              <div class="modal-footer">
                <button type="button" class="btn pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-info">Cadastrar</button>

              </div>
              {!! Form::close() !!}
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
          </div>