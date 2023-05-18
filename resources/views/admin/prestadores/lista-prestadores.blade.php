@extends('adminlte::page')
@section('content_header')
    <h1>Listagem de prestadores</h1>
@stop


@section('content')
	
	<div class="box">
				<div class="box-header">
					<a class="btn btn-app" href="{{route('prestador.create')}}">
						<i class="fa fa-plus"></i> Cadastrar
					 </a>
				</div>
				<table id="lista-prestadores" class="table table-bordered table-hover">
					<thead>
						<tr>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Qualificação</th>
                            <th>UF</th>
                            <th>Cidade(s)</th>
                            <th></th>                          
						</tr>
					</thead>

                    <tbody>
                        @foreach($prestadores as $p)
                        <tr>
                            
                            <td><a href="{{route('prestador.edit',$p->id)}}" class="link">{{$p->nome}}</a></td>
                            <td>{{$p->telefone}}</td>
                            <td>{{$p->email}}</td>
                            <td>{{$p->qualificacao}}</td>
                            <td>{{strtoupper($p->ufAtuacao)}}</td>
                            <td>
                                @foreach(json_decode($p->cidadeAtuacao) as $c)
                                    <span class="btn btn-default btn-xs">{{$c}}</span>
                                @endforeach
                            </td>
                            <td><a href="{{route('prestador.delete', $p->id)}}" class="confirmation danger"> <i class="glyphicon glyphicon-trash
                                "></i></a></td>
                       
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
        $('#lista-prestadores').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": true,
           "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
        }           
});
$('.confirmation').on('click', function () {
            return confirm('Você deseja excluir esse prestador?');
            });
         
        });

</script>

@stop
