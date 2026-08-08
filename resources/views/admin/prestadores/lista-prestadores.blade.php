@extends('adminlte::page')

@section('title', 'Listagem de prestadores')

@section('css')
<style>
	.table-container {
		background: #fff;
		border-radius: 8px;
		padding: 20px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
		border: 1px solid #ebf0f5;
		margin-bottom: 25px;
	}

	.btn-action {
		width: 32px;
		height: 32px;
		line-height: 32px;
		padding: 0;
		text-align: center;
		border-radius: 6px;
		margin: 0 2px;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		transition: all 0.2s;
		border: 1px solid #ddd;
		background: #fff;
	}

	.btn-action:hover {
		transform: scale(1.1);
		box-shadow: 0 2px 5px rgba(0,0,0,0.1);
		text-decoration: none;
	}

	.content-header .breadcrumb { display: none !important; }

    :root {
      --star-size: 20px;
      --star-color: #ddd;
      --star-background: #fc0;
    }

    .Stars {
      --percent: calc(var(--rating) / 5 * 100%);
      font-size: var(--star-size);
      line-height: 1;
      display: inline-block;
    }

    .Stars::before {
      content: "★★★★★";
      letter-spacing: 3px;
      background: linear-gradient(90deg, var(--star-background) var(--percent), var(--star-color) var(--percent));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
</style>
@stop

@section('content_header')
<div class="row" style="margin-bottom: 15px;">
	<div class="col-sm-6">
		<h1 style="margin: 0; font-weight: 700; color: #333;">Listagem de Prestadores</h1>
	</div>
	<div class="col-sm-6 text-right">
		<a class="btn btn-primary" href="{{route('prestador.create')}}" style="border-radius: 50px; padding: 8px 20px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
			<i class="fa fa-plus"></i> Cadastrar
		</a>
	</div>
</div>
@stop

@section('content')
<div class="table-container">
	<table id="lista-prestadores" class="table table-hover" style="width: 100%;">
		<thead>
			<tr style="background: #fcfcfc;">
				<th>Nome</th>
				<th>Telefone</th>
				<th>Email</th>
				<th>Qualificação</th>
				<th>UF</th>
				<th>Cidade(s)</th>
				<th>Avaliações</th>
				<th width="120" class="text-center">Ações</th>
			</tr>
		</thead>
		<tbody>
			@foreach($prestadores as $p)
				<tr>
					<td><a href="{{route('prestador.edit', $p->id)}}" style="font-weight: 600; color: #3c8dbc;">{{$p->nome}}</a></td>
					<td>{{$p->telefone}}</td>
					<td>{{$p->email}}</td>
					<td>{{$p->qualificacao}}</td>
					<td>{{strtoupper($p->ufAtuacao)}}</td>
					<td>
						@foreach(json_decode($p->cidadeAtuacao) ?? [] as $c)
							<span class="btn btn-default btn-xs" style="margin: 1px; border-radius: 4px;">{{$c}}</span>
						@endforeach
					</td>
					<td>
						@if(optional($p->rating)->count())
							<span class="pull-right" style="margin-left: 5px;">({{$p->rating->median('rating')}})</span>
							<div class="Stars" style="--rating: {{$p->rating->median('rating')}};"></div>
						@endif
					</td>
					<td class="text-center" style="white-space: nowrap;">
						<a href="{{route('prestador.edit', $p->id)}}" class="btn btn-default btn-action" title="Editar">
							<i class="fa fa-edit text-warning"></i>
						</a>
						<a href="{{route('prestador.delete', $p->id)}}" class="btn btn-default btn-action confirmation" title="Excluir">
							<i class="fa fa-trash text-danger"></i>
						</a>
					</td>
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