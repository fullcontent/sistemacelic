<div class="box-body">
  
      <div class="col-md-3">
        <div class="form-group">
          
          {!! Form::label('servico_id', 'Ordem de serviço', array('class'=>'control-label')) !!}

                  

          @isset($servico_id)
          {!! Form::select('servico_id', $servicos, $servico_id, ['class'=>'form-control','id'=>'servico_id','disabled']) !!}
          @endisset


          @empty($servico_id)
              {!! Form::select('servico_id', $servicos, null, ['class'=>'form-control','id'=>'servico_id','disabled']) !!}
          @endempty

          
          @if($s ?? '')

          {!! Form::hidden('servico_id', $s) !!}

          @endif
          
        </div>
      </div>
        
      <div class="col-md-6">
        <div class="form-group">
        {!! Form::label('nome', 'Descrição', array('class'=>'control-label')) !!}
        {!! Form::text('nome', null, ['class'=>'form-control','id'=>'nome']) !!}
        </div>
        
      </div>

      <div class="col-md-3">
        <div class="form-group">
        {!! Form::label('situacao', 'Situação', array('class'=>'control-label')) !!}
        {!! Form::select('situacao',array('aberto' => 'Em aberto','vencida'=>'Vencida', 'pago'=>'Pago'), null, ['class'=>'form-control','id'=>'situacao']) !!}
        </div>
        
      </div>

      <div class="col-md-3">
        <div class="form-group">
        {!! Form::label('emissao', 'Emissão', array('class'=>'control-label')) !!}
        @if(Route::is('taxas.show'))
        {!! Form::text('emissao', \Carbon\Carbon::parse($taxa->emissao)->format('d/m/Y'), ['class'=>'form-control','id'=>'emissao','data-date-format'=>'dd/mm/yyyy']) !!}
        @else
        {!! Form::text('emissao', null, ['class'=>'form-control','id'=>'emissao','data-date-format'=>'dd/mm/yyyy']) !!}
        @endif

        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
        {!! Form::label('vencimento', 'Vencimento', array('class'=>'control-label')) !!}
        @if(Route::is('taxas.show'))
        {!! Form::text('vencimento', \Carbon\Carbon::parse($taxa->vencimento)->format('d/m/Y'), ['class'=>'form-control','id'=>'vencimento','data-date-format'=>'dd/mm/yyyy']) !!}
        @else
        {!! Form::text('vencimento', null, ['class'=>'form-control','id'=>'vencimento','data-date-format'=>'dd/mm/yyyy']) !!}
        @endif        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
        {!! Form::label('valor', 'Valor', array('class'=>'control-label')) !!}
        {!! Form::text('valor', null, ['class'=>'form-control','id'=>'valor']) !!}
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
        {!! Form::label('reembolso', 'Reembolso', array('class'=>'control-label')) !!}
        {!! Form::select('reembolso', ['sim'=>'Sim','nao'=>'Não'] ,null, ['class'=>'form-control','id'=>'reembolso']) !!}
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
        {!! Form::label('boleto', 'Boleto', array('class'=>'control-label')) !!}
        {!! Form::file('boleto', null, ['class'=>'form-control','id'=>'boleto']) !!}

        @unless ( empty($taxa->boleto) )
        <a href="{{ url("storage/$taxa->boleto") }}" class="form-control btn btn-warning" target="_blank" id="btnBoleto">Ver Boleto</a>
        <span><a href="#" class="btn btn-xs btn-danger" alt="Remover Boleto" id="removerBoleto">X</a></span>
        
       
        @endunless
    
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
        {!! Form::label('comprovante', 'Comprovante', array('class'=>'control-label')) !!}
        {!! Form::file('comprovante', null, ['class'=>'form-control','id'=>'comprovante']) !!}
        @unless ( empty($taxa->comprovante) )
        <a href="{{ url("storage/$taxa->comprovante") }}" class="form-control btn btn-warning" target="_blank" id="btnComprovante">Ver Comprovante</a>
        <span><a href="#" class="btn btn-xs btn-danger" alt="Remover Comprovante" id="removerComprovante">X</a></span>
     
        @endunless
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="form-group">
        {!! Form::label('pagamento', 'Data de Pagamento', array('class'=>'control-label')) !!}  
        {!! Form::text('pagamento', null, ['class'=>'form-control','id'=>'pagamento','data-date-format'=>'dd/mm/yyyy']) !!}    
        </div>
      </div>


      <div class="col-md-12">
        <div class="form-group">
        {!! Form::label('observacoes', 'Observações', array('class'=>'control-label')) !!}
        {!! Form::textarea('observacoes', null, ['class'=>'form-control','id'=>'observacoes']) !!}
        </div>
      </div>

     
            

</div>

