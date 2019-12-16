<div class="row">
    <div class="col-sm-12">
      <div class="box box-black">
            <div class="box-header with-border">
              <h3 class="box-title">Últimas Interações</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            
             <ul class="timeline timeline-inverse">
                                 
                  
                @foreach($servico->historico as $historico)
                  <!-- timeline item -->
                  <li>
                    
                    <i class="fa fa-user bg-aqua"></i>
                    
                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock"></i> {{\Carbon\Carbon::parse($historico->created_at)->diffForHumans()}}</span>
                      <h3 class="timeline-header"><a href="#">{{$historico->user->name}}</a> {{$historico->observacoes}}</h3>

                      
                    </div>
                  </li>
                  <!-- END timeline item -->
                @endforeach
                  
                  
                  <li>
                    <i class="fa fa-clock bg-gray"></i>
                  </li>
                </ul>

                <div class="box-footer">
                
                <div class="box-header">
                  
                  {!! Form::open(['route'=>'interacao.salvar']) !!}
                  <div class="input-group">
                  {!! Form::text('observacoes', null, ['class'=>'form-control','id'=>'observacoes','placeholder'=>'Digite a mensagem']) !!}
                  {!! Form::hidden('servico_id',$servico->id) !!}
                  
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-info btn-flat">Enviar</button>
                      </span>
                </div>
                {!! Form::close() !!}   

                </div>

            </div>


                 
  </div>


  </div> 

</div>