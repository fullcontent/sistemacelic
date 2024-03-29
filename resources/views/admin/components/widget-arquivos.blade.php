<div class="box box-navy  collapsed-box">
            <div class="box-header with-border">
              <a href="#" data-widget="collapse"><h3 class="box-title">Arquivo digital</h3></a>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin" id="lista-arquivos">
                  <thead>
                  <tr>
                    <th>Arquivo</th>
                    <th>Enviado por</th>
                    <th></th>
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                   @foreach($dados->arquivos as $a)
                   <tr>
                   	<td>{{$a->nome}}</td>
                     <td>{{$a->user['privileges'] ?? ''}}</td>
                   	<td><a href="{{ route('arquivo.download',$a->id) }}" class="btn btn-xs btn-default" target="_blank">Download</a></td>
                    <td><a href="{{route('arquivo.delete',$a->id)}}" onclick="return confirm('Tem certeza que deseja excluir o arquivo?');"><i class="fa fa-trash"></i></a></td>
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
                
					{!! Form::open(['route'=>'arquivo.store','enctype'=>'multipart/form-data']) !!}
						
						<div class="form-group">
							{!! Form::label('nome', 'Nome', array('class'=>'control-label')) !!}
							{!! Form::text('nome', null, ['class'=>'form-control','id'=>'email']) !!}

						</div>

						<div class="form-group">
							 {!! Form::label('arquivo', 'Arquivo', array('class'=>'control-label')) !!}
        				{!! Form::file('arquivo', null, ['class'=>'form-control','id'=>'arquivo']) !!}

						</div>
              
              
              @switch($arquivo)

              @case('unidade')
                {!! Form::hidden('unidade_id', $dados->id ?? '') !!}
                {!! Form::hidden('route', 'unidade.show') !!}
                @break
              
              @case('empresa')
                {!! Form::hidden('empresa_id', $dados->id ?? '') !!}
                  {!! Form::hidden('route', 'empresa.show') !!}
                @break

              @case('servico')
                {!! Form::hidden('servico_id', $dados->id ?? '') !!}
                  {!! Form::hidden('route', 'servico.show') !!}
                @break
              @default
                  <span>Something went wrong, please try again</span>
              @endswitch
             
						
					

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


