@extends('adminlte::page')



@section('content')

<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Novo Faturamento</h3>
	</div>


{!! Form::open(['route'=>'faturamento.step2','id'=>'cadastroFaturamento']) !!}

<div class="box-body">
	
	{!! Form::label('empresa_id', 'Selecione a empresa:', array('class'=>'control-label')) !!}
	{!! Form:: select('empresa_id',$empresas,null,['class'=>'form-control']) !!}
	
</div>

<div class="box-footer">
                <a href="#" class="btn btn-default">Voltar</a>
                <button type="submit" class="btn btn-info">Próximo Passo</button>
              	</div>
    
{!! Form::close() !!}





@endsection