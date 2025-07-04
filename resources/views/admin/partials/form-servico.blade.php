<div class="box-body">
	
	@if($errors->any())
    {!! implode('', $errors->all('<div class="callout callout-danger">:message</div>')) !!}
@endif

<div class="col-md-3">
	
	<div class="form-group">
		
		{!! Form::label('tipo', 'Tipo', array('class'=>'control-label')) !!}
		
		@if(Route::is('servicos.create'))
		
		{!! Form::hidden('servicoPrincipal', $servicoPrincipal ?? '') !!}
		
		{!! Form::select('tipo', array(
			'licencaOperacao' => 'Licenças de Operação',
			'nRenovaveis' => 'Licenças não renováveis',
			'controleCertidoes' => 'Controle de Certidões',
			'controleTaxas' => 'Controle de Taxas',
			'facilitiesRealEstate' => 'Facilities/Real Estate',
			'projetosLaudos' => 'Projetos e Laudos',
			), 
			$tipoServico, ['class'=>'form-control'])!!}

		@else
		{!! Form::select('tipo', array(
			'licencaOperacao' => 'Licenças de Operação',
			'nRenovaveis' => 'Licenças não renováveis',
			'controleCertidoes' => 'Controle de Certidões',
			'controleTaxas' => 'Controle de Taxas',
			'facilitiesRealEstate' => 'Facilities/Real Estate',
			'projetosLaudos' => 'Projetos e Laudos',
			), 
			null, ['class'=>'form-control'])!!}
		@endif
		
		
	</div>
</div>

<div class="col-md-2">


	
	<div class="form-group">
		
		{!! Form::label('os', 'Ordem de serviço', array('class'=>'control-label')) !!}
		{!! Form::text('os', null, ['class'=>'form-control','id'=>'os']) !!}
		
	</div>
</div>

<div class="col-md-3">
	
	<div class="form-group">
		
		{!! Form::label('situacao', 'Situação', array('class'=>'control-label')) !!}
			
		@if(Route::is('servicos.create'))

			{!! Form::select('situacao', array(
				'andamento' => 'Andamento',
				
				
				), null, ['class'=>'form-control'])!!}
				
		@else
		{!! Form::select('situacao', array(
				'andamento' => 'Andamento',
				'finalizado' => 'Finalizado',
				'arquivado'=>'Arquivado',
				'standBy'=>'Stand By',
				'nRenovado'=>'Não renovado',
				), null, ['class'=>'form-control'])!!}

		@endif
	</div>
</div>

<div class="col-md-2">
	
	<div class="form-group">
		
		 <div class="form-group">
          
          {!! Form::label('responsavel_id', 'Responsável', array('class'=>'control-label')) !!}
		  
		  
		  @if(Route::is('servicos.edit'))
		  {!! Form::select('responsavel_id', $users, null, ['class'=>'form-control','id'=>'responsavel']) !!}
		  @elseif(Route::is('servicos.create'))
		  {!! Form::select('responsavel_id', $users, Auth::id(), ['class'=>'form-control','id'=>'responsavel']) !!}
		  @endif


        </div>
		
	</div>
</div>

<div class="col-md-2">

	<div class="form-group">
		{!! Form::label('corresponsavel_id', 'Co-Responsável', array('class'=>'control-label')) !!}
				
		{!! Form::select('coresponsavel_id', $users, null, ['class'=>'form-control','id'=>'corresponsavel']) !!}


	</div>
</div>

<div class="col-md-2">

	<div class="form-group">
		{!! Form::label('analista1_id', 'Analista 1', array('class'=>'control-label')) !!}
				
		{!! Form::select('analista1_id', $users, null, ['class'=>'form-control','id'=>'analista1_id']) !!}

	</div>
</div>

<div class="col-md-2">

	<div class="form-group">
		{!! Form::label('analista2_id', 'Analista2', array('class'=>'control-label')) !!}
				
		{!! Form::select('analista2_id', $users, null, ['class'=>'form-control','id'=>'analista2_id']) !!}


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



<div class="col-md-4">
	
	<div class="form-group">
		
		{!! Form::label('nome', 'Serviço', array('class'=>'control-label')) !!}
		
		
		{!! Form::text('nome', null, ['class'=>'form-control','id'=>'nome']) !!}
		
	</div>
</div>

<div class="col-md-2">
	<div class="form-group">
		{!! Form::label('licenciamento', 'Licenciamento', array('class'=>'control-label')) !!}


		@if(Route::is('servicos.edit'))
		{!! Form::select('licenciamento', array(
			null,
			'empresa' => 'Empresa',
			'imovel' => 'Imóvel',
			
			), $licenciamento, ['class'=>'form-control'])!!}
		@else
		{!! Form::select('licenciamento', array(
				null,
				'empresa' => 'Empresa',
				'imovel' => 'Imóvel',
				
				), null, ['class'=>'form-control'])!!}

		@endif
	</div>
</div>



<div class="col-md-4">
	
	<div class="input-group input-group-sm form-group">
		
		{!! Form::label('solicitante', 'Solicitante', array('class'=>'control-label')) !!}
		
		@if(Route::is('servicos.create'))
		{!! Form::select('solicitante',$solicitantes ,null, ['class'=>'form-control','id'=>'solicitante']) !!}
		@else
		{!! Form::select('solicitante',$solicitantes, null, ['class'=>'form-control','id'=>'solicitante']) !!}
		@endif
		<span class="input-group-btn" style="vertical-align: bottom;">
			<a href="{{route('solicitantes.create')}}" class="btn btn-warning" target="_blank">Novo Solicitante</a>
		</span>
		
	</div>

	
</div>

<div class="col-md-2">
	<div class="form-group">
		{!! Form::label('departamento', 'Departamento', array('class'=>'control-label')) !!}

		{!! Form::select('departamento', array(
				null,
				'licenciamento' => 'Licenciamento',
				'permits' => 'Permits',
				'permitsAmbiental'=>'Permits Ambiental',
				'regulatorio'=>'Regulatório',
				'regulatorioAmbiental'=>'Regulatório Ambiental',
				'obras'=>'Obras',
				'expansao'=>'Expansão',
				'compras' => 'Compras',
				'arquitetura' => 'Arquitetura',
				'farmaceutico' => 'Farmacêutico',
				'outros' => 'Outros'
				), null, ['class'=>'form-control'])!!}
		
	</div>
</div>

<div class="col-md-3">
	
	<div class="form-group">
		
		{!! Form::label('dataFinal', 'Data Final', array('class'=>'control-label')) !!}
		{!! Form::text('dataFinal', null, ['class'=>'form-control','id'=>'dataFinal','data-date-format'=>'dd/mm/yyyy']) !!}
		
	</div>
</div>
<div class="col-md-3">
	
	<div class="form-group">
		
		{!! Form::label('dataLimiteCiclo', 'Data Limite Ciclo', array('class'=>'control-label')) !!}
		{!! Form::text('dataLimiteCiclo', null, ['class'=>'form-control','id'=>'dataLimiteCiclo','data-date-format'=>'dd/mm/yyyy']) !!}
		
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
    		
    		<a href="{{ url("uploads/$servico->protocolo_anexo") }}" class="btn btn-block btn-xs btn-warning" target="_blank" id="btnProtocolo">Ver Protocolo</a>
			<a href="#" class="btn btn-xs btn-danger" alt="Remover Protocolo" id="removerProtocolo">X</a>
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
    		
    		<a href="{{ url("uploads/$servico->laudo_anexo") }}" class="btn btn-block btn-xs btn-warning" target="_blank" id="btnLaudo">Ver Laudo</a>
			<a href="#" class="btn btn-xs btn-danger" alt="Remover Laudo" id="removerLaudo">X</a>
		@endunless
		
	</div>
</div>
	
</div>




<div class="row">
	<div class="col-md-12">
		
					

				@if(Route::is('servicos.create'))
				<div class="col-md-2">
					<div class="form-group">

				{!! Form::label('valorTotal', 'Valor Total', array('class'=>'control-label')) !!}
				{!! Form::text('valorTotal', null, ['class'=>'form-control','id'=>'valorTotal']) !!}

					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
				{!! Form::label('proposta', 'Proposta', array('class'=>'control-label')) !!}
				{!! Form::text('proposta', null, ['class'=>'form-control','id'=>'proposta']) !!}
					</div>
				</div>
				@else

				<div class="col-md-2">
					<div class="form-group">
				{!! Form::label('valorTotal', 'Valor Total', array('class'=>'control-label')) !!}
				{!! Form::text('valorTotal', $servico->financeiro->valorTotal, ['class'=>'form-control','id'=>'valorTotal']) !!}
					</div>
				</div>	
				<div class="col-md-2">
					<div class="form-group">
						{!! Form::label('valorAberto', 'Valor em Aberto', array('class'=>'control-label')) !!}
						{!! Form::text('valorAberto', $servico->financeiro->valorAberto, ['class'=>'form-control','id'=>'valorAberto']) !!}
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
					{!! Form::label('proposta', 'Proposta', array('class'=>'control-label')) !!}

					@if($servico->proposta_id)
						<p><a href="{{route('proposta.edit',$servico->proposta_id)}}" class="btn btn-info btn-xs">{{$servico->proposta_id}}</a></p>
					@else
					
					{!! Form::text('proposta', $servico->proposta, ['class'=>'form-control','id'=>'proposta']) !!}
					@endif
				
					</div>
				</div>

				
				<div class="col-md-2">
					<div class="form-group">
						{!! Form::label('nf', 'NF', array('class'=>'control-label')) !!}
						{!! Form::text('nf', $servico->nf, ['class'=>'form-control','id'=>'nf']) !!}

					</div>
				</div>



				

				
						@if($servico->faturamento)
						<div class="col-md-12">
							<div class="form-group">
						
								<div class="alert alert-success alert-dismissible">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<h4><i class="icon fa fa-check"></i> Esse serviço já foi faturado!</h4>
									<a class="btn btn-warning" href="{{route('faturamento.show', $servico->faturamento->id)}}" target="_blank"><i class="fa fa-file"></i> <span>Acessar relatório</span> </a>
								</div>
							</div>
						</div>
						@endif
					
				@endif
			
		
	</div>
	
</div>



<div class="col-md-12">
	
	<div class="form-group">
		
		{!! Form::label('observacoes', 'Observações', array('class'=>'control-label')) !!}
		{!! Form::textarea('observacoes', null, ['class'=>'form-control','id'=>'observacoes']) !!}
		
	</div>
</div>

<div class="col-md-12">
	
	<div class="form-group">
		
		{!! Form::label('escopo', 'Escopo', array('class'=>'control-label')) !!}
		{!! Form::textarea('escopo', null, ['class'=>'form-control','id'=>'escopo']) !!}
		
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
    		
    		<a href="{{ url("uploads/$servico->licenca_anexo") }}" class="btn btn-block btn-xs btn-warning" target="_blank" id="btnLicenca">Ver Licença</a>
			<a href="#" class="btn btn-xs btn-danger" alt="Remover Licenca" id="removerLicenca">X</a>
		@endunless
	</div>
</div>

</div>





</div>
