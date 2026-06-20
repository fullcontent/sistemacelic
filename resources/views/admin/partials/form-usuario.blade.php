<div class="box-body" style="padding: 25px;">
    
    <div class="row">
        <!-- Coluna 1: Informações Gerais -->
        <div class="col-md-6">
            <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #354256; border-bottom: 2px solid #f4f4f4; padding-bottom: 10px;">
                <i class="fa fa-user"></i> Identificação
            </h4>
            
            <div class="form-group">
                {!! Form::label('name', 'Nome Completo', array('class'=>'control-label', 'style'=>'color: #7f8c8d;')) !!}
                {!! Form::text('name', null, ['class'=>'form-control','id'=>'name', 'style'=>'border-radius: 6px; padding: 10px;']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('email', 'E-mail', array('class'=>'control-label', 'style'=>'color: #7f8c8d;')) !!}
                {!! Form::text('email', null, ['class'=>'form-control','id'=>'email', 'style'=>'border-radius: 6px; padding: 10px;']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('password', 'Senha', array('class'=>'control-label', 'style'=>'color: #7f8c8d;')) !!}
                {!! Form::password('password', ['class'=>'form-control','id'=>'password', 'style'=>'border-radius: 6px; padding: 10px;']) !!}
            </div>
        </div>

        <!-- Coluna 2: Permissões e Status -->
        <div class="col-md-6">
            <h4 style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #354256; border-bottom: 2px solid #f4f4f4; padding-bottom: 10px;">
                <i class="fa fa-sliders"></i> Acesso e Status
            </h4>

            @unless(auth()->user()->privileges != 'admin')
            <div class="form-group">
                {!! Form::label('privileges', 'Tipo de Usuário', array('class'=>'control-label', 'style'=>'color: #7f8c8d;')) !!}
                {!! Form::select('privileges', array('admin' => 'Administrador', 'user' => 'Usuário Comum','cliente'=>'Cliente'), null,['class'=>'form-control','id'=>'privileges', 'style'=>'border-radius: 6px; height: auto; padding: 8px;']) !!}
            </div>

            <div class="form-group">
                {{ Form::label('Status', null, ['class' => 'control-label', 'style'=>'color: #7f8c8d;']) }}
                {!! Form::select('active', array('1' => 'Ativo', '0' => 'Inativo'), null,['class'=>'form-control','id'=>'active', 'style'=>'border-radius: 6px; height: auto; padding: 8px;']) !!}
            </div>

            <div class="form-group" style="margin-top: 25px; margin-bottom: 25px;">
                {{ Form::label('Permissões de Interação', null, ['class' => 'control-label', 'style'=>'color: #7f8c8d;']) }}
                <div class="checkbox" style="margin-top: 5px;">
                    <label style="padding-left: 0; font-weight: 600;">
                        {!! Form::checkbox('permitir_interacoes', 1, isset($usuario) ? $usuario->permitir_interacoes : true, ['id'=>'permitir_interacoes']) !!}
                        Permitir interagir em pendências e mensagens (criar/responder)
                    </label>
                </div>
            </div>

            <div class="form-group" style="margin-top: 25px; margin-bottom: 25px;">
                {{ Form::label('Acesso a Serviços', null, ['class' => 'control-label', 'style'=>'color: #7f8c8d;']) }}
                <div class="checkbox" style="margin-top: 5px;">
                    <label style="padding-left: 0; font-weight: 600;">
                        {!! Form::checkbox('permitir_acesso_servicos', 1, isset($usuario) ? $usuario->permitir_acesso_servicos : true, ['id'=>'permitir_acesso_servicos']) !!}
                        Permitir acesso de usuários nas telas de serviços (listar/visualizar/atribuir)
                    </label>
                </div>
            </div>
            @endunless
        </div>
    </div>

    @unless(auth()->user()->privileges != 'admin')
    <div class="row" style="margin-top: 30px;">
        <div class="col-md-12">
            <h4 style="margin-bottom: 20px; font-weight: 700; color: #354256; border-bottom: 2px solid #f4f4f4; padding-bottom: 10px;">
                <i class="fa fa-key"></i> Escopo de Acessos Autorizados
            </h4>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('Empresas Autorizadas', null, ['class' => 'control-label', 'style'=>'color: #7f8c8d;']) }}
                {{ Form::select('empresas_user_access[]', $empresas, null,['class'=>'form-control','multiple'=>'multiple','id'=>'empresas_user_access', 'style'=>'width: 100%; border-radius: 6px;']) }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('Unidades Autorizadas', null, ['class' => 'control-label', 'style'=>'color: #7f8c8d;']) }}
                {{ Form::select('unidades_user_access[]', $unidades, null,['class'=>'form-control','multiple'=>'multiple','id'=>'unidades_user_access','disabled'=>'disabled', 'style'=>'width: 100%; border-radius: 6px;']) }}
                <p class="help-block" style="font-size: 0.85em; margin-top: 5px; color: #95a5a6;"><i class="fa fa-info-circle text-muted"></i> Desabilitado temporariamente.</p>
            </div>
        </div>

        <div class="col-md-12" style="margin-top: 15px;">
            <div class="form-group">
                {{ Form::label('Departamentos Autorizados (Apenas para Clientes)', null, ['class' => 'control-label', 'style'=>'color: #7f8c8d;']) }}
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
                ), isset($usuario) ? $usuario->departamentos : null, ['class'=>'form-control', 'multiple'=>'multiple', 'id'=>'departamentos_user_access', 'style'=>'width: 100%; border-radius: 6px;']) !!}
                <p class="help-block" style="margin-top: 5px; color: #7f8c8d;"><i class="fa fa-info-circle text-info"></i> Se nenhum for selecionado, o cliente terá acesso a <strong>todos</strong> os departamentos.</p>
            </div>
        </div>
    </div>
    @endunless

</div>