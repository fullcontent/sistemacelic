<div class="box-body">
  
      <div class="col-md-3">
        <div class="form-group">
          
          {!! Form::label('servico_id', 'Ordem de serviço', array('class'=>'control-label')) !!}

          
          {!! Form::select('servico_id', $servicos, null, ['class'=>'form-control','id'=>'servico_id']) !!}
          
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
        {!! Form::select('situacao', array('aberto' => 'Em aberto', 'vencimento' => 'Vencimento','vencida'=>'Vencida'), 'aberto', ['class'=>'form-control','id'=>'situacao']) !!}
        </div>
        
      </div>

      <div class="col-md-4">
        <div class="form-group">
        {!! Form::label('emissao', 'Emissão', array('class'=>'control-label')) !!}
        {!! Form::text('emissao', null, ['class'=>'form-control','id'=>'emissao']) !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
        {!! Form::label('vencimento', 'Vencimento', array('class'=>'control-label')) !!}
        {!! Form::text('vencimento', null, ['class'=>'form-control','id'=>'vencimento']) !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
        {!! Form::label('valor', 'Valor', array('class'=>'control-label')) !!}
        {!! Form::text('valor', null, ['class'=>'form-control','id'=>'valor']) !!}
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
        {!! Form::label('boleto', 'Boleto', array('class'=>'control-label')) !!}
        {!! Form::file('boleto', null, ['class'=>'form-control','id'=>'boleto']) !!}

        @unless ( empty($taxa->boleto) )
        
        <a href="{{ url("storage/$taxa->boleto") }}" class="btn btn-xs btn-warning" target="_blank">Ver Boleto</a>
        @endunless
    
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
        {!! Form::label('comprovante', 'Comprovante', array('class'=>'control-label')) !!}
        {!! Form::file('comprovante', null, ['class'=>'form-control','id'=>'comprovante']) !!}
        @unless ( empty($taxa->comprovante) )
        
        <a href="{{ url("storage/$taxa->comprovante") }}" class="btn btn-xs btn-warning" target="_blank">Ver Comprovante</a>
        @endunless
        </div>
      </div>

      <div class="col-md-12">
        <div class="form-group">
        {!! Form::label('observacoes', 'Observações', array('class'=>'control-label')) !!}
        {!! Form::textarea('observacoes', null, ['class'=>'form-control','id'=>'observacoes']) !!}
        </div>
      </div>


            

</div>

