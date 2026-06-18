<div class="box-body">
	

	<div class="col-md-12">
		
		<div class="form-group">
			{!! Form::label('name', 'Nome', array('class'=>'control-label')) !!}
			{!! Form::text('name', null, ['class'=>'form-control','id'=>'name']) !!}

		</div>
	</div>

	<div class="col-md-12">
		
		<div class="form-group">
			{!! Form::label('email', 'E-mail', array('class'=>'control-label')) !!}
			{!! Form::text('email', null, ['class'=>'form-control','id'=>'email']) !!}

		</div>
	</div>

	<div class="col-md-12">
		
		<div class="form-group">
			{!! Form::label('password', 'Senha', array('class'=>'control-label')) !!}
			{!! Form::password('password', null, ['class'=>'form-control','id'=>'password']) !!}
		</div>
	</div>
	

	@unless(auth()->user()->privileges != 'admin')
	<div class="col-md-12">
		
		<div class="form-group">
			{!! Form::label('privileges', 'Tipo', array('class'=>'control-label')) !!}
			{!! Form::select('privileges', array('admin' => 'Admin', 'user' => 'Usuario','cliente'=>'Cliente'), null,['class'=>'form-control','id'=>'privileges']) !!}

		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			{{ Form::label('Status', null, ['class' => 'control-label']) }}
			{!! Form::select('active', array('1' => 'Ativo', '0' => 'Inativo'), null,['class'=>'form-control','id'=>'active']) !!}
		</div>
	</div>


	<div class="col-md-12">
		
		<div class="form-group">
			{{ Form::label('Empresas', null, ['class' => 'control-label']) }}
 			{{ Form::select('empresas_user_access[]', $empresas, null,['class'=>'form-control','multiple'=>'multiple','id'=>'empresas_user_access']) }}

						
		</div>
	</div>

	<div class="col-md-12">
		
		<div class="form-group">
			 {{ Form::label('Unidades', null, ['class' => 'control-label']) }}
 			{{ Form::select('unidades_user_access[]', $unidades, null,['class'=>'form-control','multiple'=>'multiple','id'=>'unidades_user_access','disabled'=>'disabled']) }}
			*desabilitado temporariamente
						
		</div>
	</div>

	<div class="col-md-12">
		<div class="form-group">
			{{ Form::label('Departamentos Autorizados (Apenas para Clientes)', null, ['class' => 'control-label']) }}
			{!! Form::select('departamentos[]', array(
				'licenciamento' => 'Licenciamento',
				'permits' => 'Permits',
				'permitsAmbiental' => 'Permits Ambiental',
				'regulatorio' => 'Regulatório',
				'regulatorioAmbiental' => 'Regulatório Ambiental',
				'obras' => 'Obras',
				'expansao' => 'Expansão',
				'compras' => 'Compras',
				'arquitetura' => 'Arquitetura',
				'farmaceutico' => 'Farmacêutico',
				'hubSaude' => 'Hub de Saúde',
				'outros' => 'Outros'
			), isset($usuario) ? $usuario->departamentos : null, ['class'=>'form-control', 'multiple'=>'multiple', 'id'=>'departamentos_user_access', 'style'=>'width: 100%;']) !!}
			<p class="help-block" style="margin-top: 5px; color: #737373;"><i class="fa fa-info-circle text-info"></i> Se nenhum for selecionado, o cliente terá acesso a <strong>todos</strong> os departamentos.</p>
		</div>
	</div>
	
	@endunless



</div>