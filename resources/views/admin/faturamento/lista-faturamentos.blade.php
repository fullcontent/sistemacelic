@extends('adminlte::page')
@section('content_header')
    <h1>Listagem de faturamentos</h1>
@stop


@section('content')
	
	<div class="box" style="padding: 5px;">
				<div class="box-header">
					
				</div>
				<table id="lista-faturamentos" class="table table-bordered table-hover">
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
				@foreach($faturamentos as $f)

				<tr>
				<td><a href="{{route('faturamento.show',$f->id)}}">{{$f->nome}}</a></td>
				<td>{{$f->empresa->nomeFantasia}}</td>
				<td>{{$f->created_at}}</td>
				<td>R$ {{number_format($f->valorTotal,2,'.',',')}}</td>
				<td><a href="{{route('faturamento.destroy',$f->id)}}" class="confirmation">Excluir faturamento</a></td>
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
		    $('#lista-servicos').DataTable({
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
        		return confirm('Você deseja excluir o serviço?');
    			});
		     
		    });

    </script>
  @stop