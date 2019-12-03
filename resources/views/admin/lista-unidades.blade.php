@extends('adminlte::page')


@section('content_header')
    <h1>Unidades</h1>
@stop

@section('content')
	
	<table id="lista-unidades" class="table table-bordered table-hover">
                <thead>
                <tr>
                	<td>ID</td>
                	<td>Nome Fantasia</td>
                	<td>Empresa</td>
                	
                </tr>
                </thead>
                <tbody>
                	@foreach($unidades as $unidade)
				<tr>
					<td>{{$unidade->id}}</td>
					<td>{{$unidade->nomeFantasia}}</td>
					<td>{{$unidade->empresa->nomeFantasia}}</td>
				</tr>
				@endforeach
                </tbody>
              </table>
@stop

@section('js')
<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
  <script>
		$(function () {
		    $('#lista-unidades').DataTable({
		      "paging": true,
		      "lengthChange": false,
		      "searching": true,
		      "ordering": true,
		      "info": true,
		      "autoWidth": false,
		       "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }
		     
		    });
  });
</script>
@stop
