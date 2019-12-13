<div class="box-body">

<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('tipo', 'Tipo', array('class'=>'control-label')) !!}
		{!! Form::select('tipo', array('primario' => 'Primário', 'secundario' => 'Secundário'), null, ['class'=>'form-control'])!!}
		
	</div>
</div>

<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('os', 'Ordem de serviço', array('class'=>'control-label')) !!}
		{!! Form::text('os', null, ['class'=>'form-control','id'=>'os']) !!}
		
	</div>
</div>

<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('situacao', 'Situação', array('class'=>'control-label')) !!}
		{!! Form::select('situacao', array('andamento' => 'Andamento', 'finalizado' => 'Finalizado','vencimento'=>'Vencimento'), null, ['class'=>'form-control'])!!}
		
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
		
		{!! Form::label('protocolo_emissao', 'Emissão Prot.', array('class'=>'control-label')) !!}
		{!! Form::text('protocolo_emissao', null, ['class'=>'form-control','id'=>'protocolo_emissao']) !!}
		
	</div>
</div>

<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('protocolo_validade', 'Validade Prot.', array('class'=>'control-label')) !!}
		{!! Form::text('protocolo_validade', null, ['class'=>'form-control','id'=>'protocolo_validade']) !!}
		
	</div>
</div>

<div class="col-md-12">
	
	<div class="form-group">
		
		{!! Form::label('protocolo_anexo', 'Anexo Prot.', array('class'=>'control-label')) !!}
		{!! Form::file('protocolo_anexo', null, ['class'=>'form-control','id'=>'protocolo_anexo']) !!}
		
	</div>
</div>

<div class="col-md-6">
	
	<div class="form-group">
		
		{!! Form::label('pendencia', 'Pendência', array('class'=>'control-label')) !!}
		{!! Form::text('pendencia', null, ['class'=>'form-control','id'=>'pendencia']) !!}
		
	</div>
</div>

<div class="col-md-6">
	
	<div class="form-group">
		
		{!! Form::label('acao', 'Ação', array('class'=>'control-label')) !!}
		{!! Form::text('acao', null, ['class'=>'form-control','id'=>'acao']) !!}
		
	</div>
</div>


<div class="col-md-12">
	
	<div class="form-group">
		
		{!! Form::label('observacoes', 'Observações', array('class'=>'control-label')) !!}
		{!! Form::textarea('observacoes', null, ['class'=>'form-control','id'=>'observacoes']) !!}
		
	</div>
</div>

		
		@if($tipo ?? '' == 'empresa')
			
			{!! Form::hidden('empresa_id', $id ?? '') !!}
		@else
			{!! Form::hidden('unidade_id', $id ?? '') !!}
		@endif




</div>