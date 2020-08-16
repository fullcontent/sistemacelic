<div class="box-body">

<div class="col-md-3">
	
	<div class="form-group">
		
		{!! Form::label('tipo', 'Tipo', array('class'=>'control-label')) !!}
		
		@if(Route::is('servicos.create'))
		
		{!! Form::select('tipo', array(
			'licencaOperacao' => 'Licenças de Operação',
			'nRenovaveis' => 'Licenças/Projetos não renováveis',
			'controleCertidoes' => 'Controle de Certidões',
			'controleTaxas' => 'Controle de Taxas',
			'facilitiesRealEstate' => 'Facilities/Real Estate'
			), 
			$tipoServico, ['class'=>'form-control'])!!}

		@else
		{!! Form::select('tipo', array(
			'licencaOperacao' => 'Licenças de Operação',
			'nRenovaveis' => 'Licenças/Projetos não renováveis',
			'controleCertidoes' => 'Controle de Certidões',
			'controleTaxas' => 'Controle de Taxas',
			'facilitiesRealEstate' => 'Facilities/Real Estate'
			), 
			null, ['class'=>'form-control'])!!}
		@endif
		
		
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
			{!! Form::select('situacao', array('andamento' => 'Andamento', 'finalizado' => 'Finalizado','arquivado'=>'Arquivado'), null, ['class'=>'form-control'])!!}

	</div>
</div>

<div class="col-md-3">
	
	<div class="form-group">
		
		 <div class="form-group">
          
          {!! Form::label('responsavel_id', 'Responsável', array('class'=>'control-label')) !!}
          
          {!! Form::select('responsavel_id', $users, Auth::id(), ['class'=>'form-control']) !!}

        </div>
		
	</div>
</div>

@switch($t ?? '')

			@case('empresa')
					{!! Form::hidden('empresa_id', $id ?? '') !!}
			@break

			@case('unidade')
					{!! Form::hidden('unidade_id', $id ?? '') !!}
					
			@break

		@endswitch


			{!! Form::hidden('t',$t ?? '' ?? '') !!}


<div class="col-md-6">
	
	<div class="form-group">
		
		{!! Form::label('servico_lpu', 'LPU', array('class'=>'control-label')) !!}
		{!! Form::select('servico_lpu',$servico_lpu, null, ['class'=>'form-control','id'=>'servico_lpu']) !!}
		
	</div>
</div>

<div class="col-md-6">
	
	<div class="form-group">
		
		{!! Form::label('nome', 'Serviço', array('class'=>'control-label')) !!}
		{!! Form::text('nome', null, ['class'=>'form-control','id'=>'nome']) !!}
		
	</div>
</div>



<div class="col-md-12">
	
	<div class="form-group">
		
		{!! Form::label('solicitante', 'Solicitante', array('class'=>'control-label')) !!}
		{!! Form::text('solicitante', null, ['class'=>'form-control','id'=>'solicitante']) !!}
		
	</div>
</div>

<div class="col-md-12">
	
	<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('protocolo_numero', 'N. Protocolo', array('class'=>'control-label')) !!}
		{!! Form::text('protocolo_numero', null, ['class'=>'form-control','id'=>'protocolo_numero']) !!}
		
	</div>
</div>

<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('protocolo_emissao', 'Emissão Protocolo', array('class'=>'control-label')) !!}
		{!! Form::text('protocolo_emissao', null, ['class'=>'form-control','id'=>'protocolo_emissao','data-date-format'=>'dd/mm/yyyy']) !!}
		
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

</div>




<div class="col-md-12">
	
	<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('laudo_numero', 'N. laudo de exigência', array('class'=>'control-label')) !!}
		{!! Form::text('laudo_numero', null, ['class'=>'form-control','id'=>'laudo_numero']) !!}
		
	</div>
</div>

<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('laudo_emissao', 'Emissão laudo', array('class'=>'control-label')) !!}
		{!! Form::text('laudo_emissao', null, ['class'=>'form-control','id'=>'laudo_emissao','data-date-format'=>'dd/mm/yyyy']) !!}
		
	</div>
</div>


	

<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('laudo_anexo', 'Anexo laudo.', array('class'=>'control-label')) !!}
		{!! Form::file('laudo_anexo', null, ['class'=>'form-control','id'=>'laudo_anexo']) !!}

		@unless ( empty($servico->laudo_anexo) )
    		
    		<a href="{{ url("storage/$servico->laudo_anexo") }}" class="btn btn-xs btn-warning" target="_blank">Ver laudo</a>
		@endunless
		
	</div>
</div>
	
</div>




<div class="row">
	<div class="col-md-12">
		<div class="col-md-2">
			<div class="form-group">
					
				{!! Form::label('valorTotal', 'Valor Total', array('class'=>'control-label')) !!}
				{!! Form::text('valorTotal', 0, ['class'=>'form-control','id'=>'valorTotal']) !!}
				
			</div>
		</div>
		
	</div>
	
</div>


<div class="col-md-12">
	
	<div class="form-group">
		
		{!! Form::label('observacoes', 'Observações', array('class'=>'control-label')) !!}
		{!! Form::textarea('observacoes', null, ['class'=>'form-control','id'=>'observacoes']) !!}
		
	</div>
</div>

<div class="col-md-12">
	
			<div class="col-md-2">
			
			<div class="form-group">
				
				{!! Form::label('tipoLicenca', 'Tipo de Licença', array('class'=>'control-label')) !!}
				{!! Form::select('tipoLicenca', array('renovavel' => 'Renovável', 'definitiva' => 'Definitiva','n/a'=>'Não Aplicada'), null, ['class'=>'form-control'])!!}
			</div>
		</div>


		<div class="col-md-3">
			
			<div class="form-group">
				
				{!! Form::label('licenca_emissao', 'Emissão Licença', array('class'=>'control-label')) !!}
				{!! Form::text('licenca_emissao', null, ['class'=>'form-control','id'=>'licenca_emissao','data-date-format'=>'dd/mm/yyyy']) !!}
				
			</div>
		</div>

		<div class="col-md-3">
			
			<div class="form-group">
				
				{!! Form::label('licenca_validade', 'Vencimento Licença', array('class'=>'control-label')) !!}
				{!! Form::text('licenca_validade', null, ['class'=>'form-control','id'=>'licenca_validade','data-date-format'=>'dd/mm/yyyy']) !!}
				
			</div>
</div>



<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('licenca_anexo', 'Documento Licença', array('class'=>'control-label')) !!}
		{!! Form::file('licenca_anexo', null, ['class'=>'form-control','id'=>'licenca_anexo']) !!}
		@unless ( empty($servico->licenca_anexo) )
    		
    		<a href="{{ url("storage/$servico->licenca_anexo") }}" class="btn btn-xs btn-warning" target="_blank">Ver Licença</a>
		@endunless
	</div>
</div>

</div>









		


</div>