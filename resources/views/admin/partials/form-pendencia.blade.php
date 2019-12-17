<div class="box-body">
  
      <div class="col-md-3">
        <div class="form-group">
          
          {!! Form::label('servico_id', 'Ordem de serviço', array('class'=>'control-label')) !!}

          
          {!! Form::select('servico_id', $servico, null,['class'=>'form-control','id'=>'servico_id']) !!}
          
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
        {!! Form::label('pendencia', 'Descrição', array('class'=>'control-label')) !!}
        {!! Form::text('pendencia', null, ['class'=>'form-control','id'=>'pendencia']) !!}
        </div>
        
      </div>

      <div class="col-md-3">
        <div class="form-group">
        {!! Form::label('status', 'Status', array('class'=>'control-label')) !!}
        {!! Form::select('status', array('pendente' => 'Pendente', 'vencimento' => 'Vencimento','vencida'=>'Vencida'), 'pendente', ['class'=>'form-control','id'=>'status']) !!}
        </div>
    </div>

       <div class="col-md-3">
        <div class="form-group">
        {!! Form::label('responsavel_tipo', 'Responsabilidade', array('class'=>'control-label')) !!}
        {!! Form::select('responsavel_tipo', array('usuario' => 'Castro', 'cliente' => 'Cliente','op'=>'Orgão Público'), 'usuario', ['class'=>'form-control','id'=>'responsavel_tipo']) !!}
        </div>
        
      </div>

      <div class="col-md-3">
        <div class="form-group">
        {!! Form::label('responsavel_id', 'Responsável', array('class'=>'control-label')) !!}
        {!! Form::select('responsavel_id', $responsaveis ,null, ['class'=>'form-control','id'=>'responsavel_id']) !!}
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
        {!! Form::label('vencimento', 'Data limite', array('class'=>'control-label')) !!}
        {!! Form::text('vencimento' ,null, ['class'=>'form-control','id'=>'vencimento']) !!}
        </div>
      </div>

     

     
      <div class="col-md-12">
        <div class="form-group">
        {!! Form::label('observacoes', 'Observações', array('class'=>'control-label')) !!}
        {!! Form::textarea('observacoes', null, ['class'=>'form-control','id'=>'observacoes']) !!}
        </div>
      </div>

          

</div>

@section('js')

<script>
	
	$(document).ready(function() {

  	$("#vencimento").datepicker();
  	
	  	
 
});


</script>

@stop