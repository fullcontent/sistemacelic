@extends('adminlte::page')
@section('content_header')
    <h1>Listagem de prestadores</h1>
@stop


@section('content')
	
	<div class="box">
				<div class="box-header">
					
				</div>
				<table id="prestadores" class="table table-bordered table-hover">
					<thead>
						<tr>
                            <th>Nome</th>
                            <th>Qualificação</th>
                            <th>UF</th>
                            <th>Cidade(s)</th>
                            <th>actions</th>                          
						</tr>
					</thead>

                    <tbody>
                        @foreach($prestadores as $p)
                        <tr>
                            
                            <td><a href="{{route('prestador.edit',$p->id)}}" class="link">{{$p->nome}}</a></td>
                            <td>{{$p->qualificacao}}</td>
                            <td>{{strtoupper($p->ufAtuacao)}}</td>
                            <td>{{$p->cidadeAtuacao}}</td>
                            <td></td>
                       
                        </tr>
                        @endforeach
                    </tbody>
				  </table>
				  
			</div>
	 		

@endsection


