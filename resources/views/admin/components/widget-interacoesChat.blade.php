
    <!-- DIRECT CHAT -->
    <div class="box box-warning direct-chat direct-chat-warning">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-comments"></i> Interações</h3>

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
          @php $interactor = $n->user; @endphp
          
          @if($n->user_id == \Auth::id())
          
          <!-- Message. Default to the left -->
          <div class="direct-chat-msg" data-id="{{$n->id}}" data-userID="{{$n->user_id}}" data-msg="{{$n->observacoes}}">
              
                <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left"><b>Você: </b></span>
                   
                    <span class="direct-chat-timestamp pull-right">{{\Carbon\Carbon::parse($n->created_at)->format('d/m/Y h:m')}}</span>
                  </div>

                  <div class="direct-chat-img">
                    @if($interactor && $interactor->avatar_url)
                      <img src="{{ $interactor->avatar_url }}" class="img-circle" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $interactor->name }}">
                    @else
                      <i class="fa fa-user" style="font-size:35px;"></i>
                    @endif
                  </div>
                 
            
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
                  <span class="direct-chat-name pull-right"><b>{{ $interactor ? $interactor->name : 'Usuário' }}</b></span>
                  
                  <span class="direct-chat-timestamp pull-left">{{\Carbon\Carbon::parse($n->created_at)->format('d/m/Y h:m')}}</span>
                </div>
              <div class="direct-chat-img">
                @if($interactor && $interactor->avatar_url)
                  <img src="{{ $interactor->avatar_url }}" class="img-circle" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $interactor->name }}">
                @else
                  <i class="fa fa-user text-aqua" style="font-size:35px;"></i>
                @endif
              </div>
          
          <!-- /.direct-chat-info -->
          
          <!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                <span class="msg">{{$n->observacoes}}</span>
                
                <p><b><a href="#" class="responder link" data-id="{{$n->id}}" data-user="{{$n->user_id}}" data-msg="{{$n->observacoes}}">Responder</a></b></p>
                
                </div>

              </a>
          <!-- /.direct-chat-text -->



        </div>
        <!-- /.direct-chat-msg -->
        @endif


        
        
        @endforeach

        

        

        </div>
        <!--/.direct-chat-messages-->
        @if(auth()->user()->permitir_interacoes)
        <div class="box-footer">
                  {!! Form::open(['route'=>'interacao.store', 'style'=>'width: 100%;']) !!}
                  <div style="display: flex; width: 100%; gap: 10px; align-items: flex-end;">
                      <textarea rows="1" name="observacoes" id="full" class="form-control-simple mention" placeholder="Digite a mensagem..." spellcheck="false" autocorrect="off" autocapitalize="off" data-gramm="false" data-enable-grammarly="false" required autocomplete="off" style="resize: vertical; min-height: 34px; line-height: 20px; padding: 6px 12px; flex: 1;"></textarea>
                      {!! Form::hidden('servico_id',$servico->id) !!}
                      <button type="submit" class="btn btn-info btn-flat" style="height: 34px; min-width: 80px;">Enviar</button>
                  </div>
                {!! Form::close() !!}   
            </div>
        @else
            <div class="box-footer text-center text-muted" style="padding: 15px;">
                <i class="fa fa-lock"></i> Permissão para realizar interações desabilitada.
            </div>
        @endif
       
        
      </div>
      <!-- /.box-body -->
     
    </div>
    <!--/.direct-chat -->



  