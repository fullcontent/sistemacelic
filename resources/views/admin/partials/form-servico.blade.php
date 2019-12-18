<div class="box-body">

<div class="col-md-3">
	
	<div class="form-group">
		
		{!! Form::label('tipo', 'Tipo', array('class'=>'control-label')) !!}
		{!! Form::select('tipo', array('primario' => 'Primário', 'secundario' => 'Secundário'), null, ['class'=>'form-control'])!!}
		
	</div>
</div>

<div class="col-md-3">
	
	<div class="form-group">
		
		{!! Form::label('os', 'Ordem de serviço', array('class'=>'control-label')) !!}
		{!! Form::text('os', null, ['class'=>'form-control','id'=>'os']) !!}
		
	</div>
</div>

<div class="col-md-3">
	
	<div class="form-group">
		
		{!! Form::label('situacao', 'Situação', array('class'=>'control-label')) !!}
		{!! Form::select('situacao', array('andamento' => 'Andamento', 'finalizado' => 'Finalizado','vencimento'=>'Vencimento'), null, ['class'=>'form-control'])!!}
		
	</div>
</div>

<div class="col-md-3">
	
	<div class="form-group">
		
		 <div class="form-group">
          
          {!! Form::label('responsavel_id', 'Responsável', array('class'=>'control-label')) !!}
          
          {!! Form::select('responsavel_id', $users, null, ['class'=>'form-control']) !!}

        </div>
		
	</div>
</div>



<div class="col-md-12">
	
	<div class="form-group">
		
		{!! Form::label('nome', 'Nome', array('class'=>'control-label')) !!}
		{!! Form::text('nome', null, ['class'=>'form-control','id'=>'nome']) !!}
		
	</div>
</div>


<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('protocolo_numero', 'N. Protocolo', array('class'=>'control-label')) !!}
		{!! Form::text('protocolo_numero', null, ['class'=>'form-control','id'=>'protocolo_numero']) !!}
		
	</div>
</div>

<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('protocolo_emissao', 'Emissão Protocolo', array('class'=>'control-label')) !!}
		{!! Form::text('protocolo_emissao', null, ['class'=>'form-control','id'=>'protocolo_emissao']) !!}
		
	</div>
</div>


	

<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('protocolo_anexo', 'Anexo Protocolo.', array('class'=>'control-label')) !!}
		{!! Form::file('protocolo_anexo', null, ['class'=>'form-control','id'=>'protocolo_anexo']) !!}

		@unless ( empty($servico->protocolo_anexo) )
    		
    		<a href="{{ url("storage/$servico->protocolo_anexo") }}" class="btn btn-xs btn-warning" target="_blank">Ver Protocolo</a>
		@endunless
		
	</div>
</div>














<div class="col-md-12">
	
	<div class="form-group">
		
		{!! Form::label('observacoes', 'Observações', array('class'=>'control-label')) !!}
		{!! Form::textarea('observacoes', null, ['class'=>'form-control','id'=>'observacoes']) !!}
		
	</div>
</div>





<div class="col-md-3">
	
	<div class="form-group">
		
		{!! Form::label('licenca_emissao', 'Emissão Licença', array('class'=>'control-label')) !!}
		{!! Form::text('licenca_emissao', null, ['class'=>'form-control','id'=>'licenca_emissao']) !!}
		
	</div>
</div>

<div class="col-md-3">
	
	<div class="form-group">
		
		{!! Form::label('licenca_vencimento', 'Vencimento Licença', array('class'=>'control-label')) !!}
		{!! Form::text('licenca_vencimento', null, ['class'=>'form-control','id'=>'licenca_vencimento']) !!}
		
	</div>
</div>

<div class="col-md-6">
	
	<div class="form-group">
		
		{!! Form::label('licenca_anexo', 'Documento Licença', array('class'=>'control-label')) !!}
		{!! Form::file('licenca_anexo', null, ['class'=>'form-control','id'=>'licenca_anexo']) !!}
		@unless ( empty($servico->licenca_anexo) )
    		
    		<a href="{{ url("storage/$servico->licenca_anexo") }}" class="btn btn-xs btn-warning" target="_blank">Ver Licença</a>
		@endunless
	</div>
</div>

		
		@if($tipo ?? '' == 'empresa')
			
			{!! Form::hidden('empresa_id', $id ?? '') !!}
		@else
			{!! Form::hidden('unidade_id', $id ?? '') !!}
		@endif




</div>