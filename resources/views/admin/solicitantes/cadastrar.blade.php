@extends('adminlte::page')


@section('content')
	

	<div class="box box-primary">
		
		<div class="box-header with-border">
		<h3 class="box-title">Cadastrar solicitante</h3>
	</div>
	
	{!! Form::open(['route'=>'solicitantes.store']) !!}

	<div class="box-body">
        
        <div class="col-md-12">
        
            <div class="form-group">
              
              {!! Form::label('empresa_id', 'Empresa', array('class'=>'control-label')) !!}
              {!! Form::select('empresa_id', $empresas, null, ['class'=>'form-control empresas','name'=>'empresas[]']) !!}
    
            </div>
    
          </div>

        <div class="col-md-12">
            
            <div class="form-group">
                {!! Form::label('nome', 'Nome', array('class'=>'control-label')) !!}
                {!! Form::text('nome', null, ['class'=>'form-control','id'=>'nome']) !!}
    
            </div>
        </div>
    
        <div class="col-md-6">
            
            <div class="form-group">
                {!! Form::label('email', 'E-mail', array('class'=>'control-label')) !!}
                {!! Form::text('email', null, ['class'=>'form-control','id'=>'email']) !!}
    
            </div>
        </div>

        <div class="col-md-6">
            
            <div class="form-group">
                {!! Form::label('telefone', 'Telefone', array('class'=>'control-label')) !!}
                {!! Form::text('telefone', null, ['class'=>'form-control','id'=>'telefone']) !!}
    
            </div>
        </div>
        
        <div class="col-md-12">
            
            <div class="form-group">
                {!! Form::label('departamento', 'Departamento', array('class'=>'control-label')) !!}
                {!! Form::text('departamento', null, ['class'=>'form-control','id'=>'departamento']) !!}
    
            </div>
        </div>
    
        
        
              
    
    
    </div>

	<div class="box-footer">
                <a href="{{route('solicitantes.index')}}" class="btn btn-default">Voltar</a>
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button>
              	</div>

	{!! Form::close() !!}

		
	</div>
	

@stop

@section('js')

<script>
	$(document).ready(function() {

        $(".empresas").select2({
            placeholder: 'Selecione a empresa',
            allowClear: true,
            multiple: true,
        });

        $(".empresas").val('').trigger('change');


	  	
 
});
</script>

@stop