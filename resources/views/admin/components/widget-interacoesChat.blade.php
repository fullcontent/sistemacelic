
    <!-- DIRECT CHAT -->
    <div class="box box-warning direct-chat direct-chat-warning">
      <div class="box-header with-border">
        <h3 class="box-title">Novas interações</h3>

        <div class="box-tools pull-right">
          
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
         <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
          </button>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <!-- Conversations are loaded here -->
        <div class="direct-chat-messages">
            
          <!-- Message. Default to the left -->
          <div class="direct-chat-msg">
              <a href="#" class="none">
                <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left">Bruno Carvalho ref.: <b>DP0001</b></span>
                    
                    <span class="direct-chat-timestamp pull-right">18 Ago 2:00 pm</span>
                  </div>
              
            
            <!-- /.direct-chat-info -->
            
            <!-- /.direct-chat-img -->
            <div class="direct-chat-text">
              Essa é uma interação de teste, clique nela para abrir.
            </div>
        </a>
            <!-- /.direct-chat-text -->
          </div>
          <!-- /.direct-chat-msg -->

        

        </div>
        <!--/.direct-chat-messages-->
        <div class="box-footer">
                
                <div class="box-header">
                  
                  {!! Form::open(['route'=>'interacao.store']) !!}
                  <div class="input-group">
                  
                  {!! Form::text('observacoes', null, ['class'=>'form-control','id'=>'full','placeholder'=>'Digite a mensagem']) !!}
                  {!! Form::hidden('servico_id',$servico->id) !!}
                  
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-info btn-flat">Enviar</button>
                      </span>
                </div>
                {!! Form::close() !!}   

                </div>

            </div>
       
        
      </div>
      <!-- /.box-body -->
     
    </div>
    <!--/.direct-chat -->


