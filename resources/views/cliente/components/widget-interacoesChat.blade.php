
    <!-- DIRECT CHAT -->
    <div class="box box-warning direct-chat direct-chat-warning">
      <div class="box-header with-border">
        <h3 class="box-title">Interações</h3>

        <div class="box-tools pull-right">
          
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
         <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
          </button>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body" style="overflow: visible;">
        <!-- Conversations are loaded here -->
        <div class="direct-chat-messages">
            

        @foreach($servico->interacoes as $n)  
          
          
          @if($n->user_id == \Auth::id())
          
          <!-- Message. Default to the left -->
          <div class="direct-chat-msg" data-id="{{$n->id}}" data-userID="{{$n->user_id}}" data-msg="{{$n->observacoes}}">
              
                <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left"><b>Você: </b></span>
                   
                    <span class="direct-chat-timestamp pull-right">{{\Carbon\Carbon::parse($n->created_at)->format('d/m/Y h:m')}}</span>
                  </div>

                  <div class="direct-chat-img"> <i class="fa fa-user" style="font-size:35px;"></i></div>
                 
            
            <!-- /.direct-chat-info -->
            
            <!-- /.direct-chat-img -->
                  <div class="direct-chat-text">
                  {{$n->observacoes}}
                  </div>

                </a>
            <!-- /.direct-chat-text -->
          </div>
          <!-- /.direct-chat-msg -->


          @else
           
           
           <!-- Message. Default to the right -->
           <div class="direct-chat-msg right" >
              
              <div class="direct-chat-info clearfix">
                  <span class="direct-chat-name pull-right"><b>{{\App\User::find($n->user_id)->name}}</b></span>
                  
                  <span class="direct-chat-timestamp pull-left">{{\Carbon\Carbon::parse($n->created_at)->format('d/m/Y h:m')}}</span>
                </div>
              <div class="direct-chat-img"> <i class="fa fa-user text-aqua" style="font-size:35px;"></i></div>
          
          <!-- /.direct-chat-info -->
          
          <!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                <span class="msg">{{$n->observacoes}}</span>
                
                <p><b><a href="#" class="responder" data-id="{{$n->id}}" data-user="{{$n->user_id}}" data-msg="{{$n->observacoes}}">Responder</a></b></p>
                
                </div>

              </a>
          <!-- /.direct-chat-text -->



        </div>
        <!-- /.direct-chat-msg -->
        @endif


        
        
        @endforeach

        

        

        </div>
        <!--/.direct-chat-messages-->
        <div class="box-footer">
                
                <div class="box-header">
                  
                  {!! Form::open(['route'=>'cliente.interacao.salvar']) !!} 
                  <div class="input-group">
                  
                  {!! Form::text('observacoes', null, ['class'=>'form-control mention','id'=>'full','placeholder'=>'Digite a mensagem','autocomplete'=>'off']) !!}
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



