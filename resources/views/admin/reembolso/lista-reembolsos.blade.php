@extends('adminlte::page')
@section('content_header')
    <h1>Listagem de reembolsos</h1>
@stop


@section('content')
	
	<div class="box" style="padding: 5px;">
				<div class="box-header">
					<a class="btn btn-app" href="{{route('reembolso.create')}}">
						<i class="fa fa-plus"></i> Cadastrar
					 </a>
				</div>
				<table id="lista-reembolsos" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>#</th>
				  <th>Cliente</th>
                  <th>Data</th>
				  <th>Total</th>
				  <th>Actions</th>
				<tr>
                </thead>
                <tbody>
				@foreach($reembolsos as $r)

				<tr>
					<td>{{$r->nome}}</td>
					<td>{{$r->empresa->nomeFantasia}}</td>
					<td>{{$r->created_at}}</td>
					<td>{{$r->valorTotal}}</td>
					<td><a href="{{route('reembolso.destroy',$r->id)}}" class="confirmation"> <i class="glyphicon glyphicon-trash
						"></i></a></td>				
					<td><a href="{{route('reembolso.show',$r->id)}}">ver</a></td>
				</tr>

				@endforeach
                </tbody>
              </table>   
			</div>
			
			
			

@endsection



@section('js')


<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
<script>
		$(function () {
		    $('#lista-reembolsos').DataTable({
		      "paging": true,
		      "lengthChange": true,
		      "searching": true,
		      "ordering": true,
		      "info": true,
		      "autoWidth": false,
		       "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }           
  });
$('.confirmation').on('click', function () {
        		return confirm('Você deseja excluir o reembolso?');
    			});
		     
		    });
			
		
			
</script>
  @stop