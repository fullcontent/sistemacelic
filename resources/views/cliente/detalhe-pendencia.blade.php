@extends('adminlte::page')



@section('content')

	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Detalhes pendência</h3>
	</div>

	
	<div class="box-body">
		<div class="col-md-1">
		  <div class="form-group">
			<label for="etapa" class="control-label">Etapa</label>
			<select class="form-control" id="etapa" disabled>
				<option value="">{{$pendencia->etapa}}</option>
			</select>
		  </div>
		</div>
	  
		<div class="col-md-3">
		  <div class="form-group">
			<label for="servico_id" class="control-label">Ordem de serviço</label>
			<select class="form-control" id="servico_id" disabled>
				<option value="">{{$pendencia->servico->os}}</option>
			</select>
		  </div>
		</div>
	  
		<div class="col-md-5">
		  <div class="form-group">
			<label for="pendencia" class="control-label">Descrição</label>
			<select class="form-control" id="pendencia" disabled>
				<option value="">`
					{{$pendencia->pendencia}}
				</option>
			</select>
		  </div>
		</div>
	  
		<div class="col-md-3">
		  <div class="form-group">
			<label for="status" class="control-label">Status</label>
			<select class="form-control" id="status" disabled>
				<option value="">
					{{$pendencia->status}}
				</option>
			</select>
		  </div>
		</div>
	  
		<div class="col-md-3">
		  <div class="form-group">
			<label for="responsavel_tipo" class="control-label">Responsabilidade</label>
			{!! Form::select('responsavel_tipo', array('usuario' => 'Castro', 'cliente' => 'Cliente','op'=>'Orgão Público'), $pendencia->responsavel_tipo, ['class'=>'form-control','id'=>'responsavel_tipo','disabled'=>'disabled']) !!}

		  </div>
		</div>
	  
		<div class="col-md-3">
		  <div class="form-group">
			<label for="responsavel_id" class="control-label">Responsável</label>
			{!! Form::select('responsavel_id', $responsaveis , $pendencia->responsavel->name, ['class'=>'form-control','id'=>'responsavel_id','disabled'=>'disabled']) !!}
		  </div>
		</div>
	  
		<div class="col-md-3">
		  <div class="form-group">
			<label for="vencimento" class="control-label">Data limite</label>
			{!! Form::text('vencimento' , \Carbon\Carbon::parse($pendencia->vencimento)->format('d/m/Y'), ['class'=>'form-control','id'=>'vencimento','data-date-format'=>'dd/mm/yyyy','disabled'=>'disabled']) !!}
		  </div>
		</div>
		
	  
		<div class="col-md-12">
		  <div class="form-group">
			<label for="observacoes" class="control-label">Observações</label>
			{!! Form::textarea('observacoes', $pendencia->observacoes, ['class'=>'form-control','id'=>'observacoes','disabled'=>'disabled']) !!}
		  </div>
		</div>
	  </div>
	  

	  <div class="col-md-12">
		<h3>Arquivos</h3>
		<table class="table table-bordered">
			<thead>
			  <tr>
				<td>Arquivo</td>
				<td>Cadastrado por:</td>
				<td>Download</td>
			  </tr>
			  <!-- No data rows here -->
			</thead>
			<tbody>
				@foreach($arquivos as $a)
				<tr>
					<td>{{$a->nome}}</td>
					<td>{{$a->user->name}}</td>
					<td><a href="{{$a->arquivo}}" class="btn btn-xs btn-success">Download</a></td>
				</tr>
				@endforeach
			</tbody>
		  </table>

	  </div>
		
	
	

      			<div class="box-footer">
      			
                
                
              	</div>
    	


@endsection

