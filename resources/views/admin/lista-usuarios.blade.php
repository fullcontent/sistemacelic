@extends('adminlte::page')

@section('title', 'Listagem de Usuários')

@section('css')
<style>
	.dashboard-card {
		border-radius: 8px;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
		padding: 15px;
		margin-bottom: 25px;
		transition: all 0.3s ease;
		border-left: 4px solid #354256;
		background-color: #fff;
		height: 100%;
		display: flex;
		flex-direction: column;
		justify-content: center;
		box-sizing: border-box;
	}

	.dashboard-card:hover {
		transform: translateY(-5px);
		box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
		text-decoration: none;
	}

	.card-label {
		color: #7f8c8d;
		font-size: 0.8em;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		margin-bottom: 5px;
	}

	.card-value {
		font-size: 2.2em;
		font-weight: 700;
		color: #2c3e50;
		line-height: 1;
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
	}

	.btn-action:hover {
		transform: scale(1.1);
	}

	.table-container {
		background: #fff;
		border-radius: 8px;
		padding: 20px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
		border: 1px solid #ebf0f5;
	}

	.user-avatar {
		width: 32px;
		height: 32px;
		border-radius: 50%;
		color: #fff;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		font-weight: 700;
		font-size: 0.9em;
		margin-right: 10px;
		vertical-align: middle;
	}

	.mb-4 { margin-bottom: 20px !important; }
	.content-header .breadcrumb { display: none !important; }
</style>
@stop

@section('content_header')
<div class="row" style="margin-bottom: 15px;">
	<div class="col-sm-6">
		<h1 style="margin: 0; font-weight: 700; color: #333;">Listagem de Usuários</h1>
	</div>
	<div class="col-sm-6 text-right">
		<a class="btn btn-primary" href="{{route('usuario.cadastro')}}"
			style="border-radius: 50px; padding: 8px 25px; font-weight: 600;">
			<i class="fa fa-plus"></i> Novo Usuário
		</a>
	</div>
</div>
@stop

@section('content')

	<!-- Dashboard Section -->
	<div class="row">
		<div class="col-md-2 col-sm-4 col-xs-6 mb-4">
			<div class="dashboard-card" style="border-left-color: #354256;">
				<span class="card-label">Total</span>
				<span class="card-value">{{ count($usuarios) }}</span>
			</div>
		</div>
		<div class="col-md-2 col-sm-4 col-xs-6 mb-4">
			<div class="dashboard-card" style="border-left-color: #00a65a;">
				<span class="card-label">Ativos</span>
				<span class="card-value">{{ count($usuarios->where('active', 1)) }}</span>
			</div>
		</div>
		<div class="col-md-2 col-sm-4 col-xs-6 mb-4">
			<div class="dashboard-card" style="border-left-color: #dd4b39;">
				<span class="card-label">Inativos</span>
				<span class="card-value">{{ count($usuarios->where('active', 0)) }}</span>
			</div>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-6 mb-4">
			<div class="dashboard-card" style="border-left-color: #3c8dbc;">
				<span class="card-label">Administradores</span>
				<span class="card-value">{{ count($usuarios->where('privileges', 'admin')) }}</span>
			</div>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-6 mb-4">
			<div class="dashboard-card" style="border-left-color: #f39c12;">
				<span class="card-label">Clientes</span>
				<span class="card-value">{{ count($usuarios->where('privileges', 'cliente')) }}</span>
			</div>
		</div>
	</div>

	<div class="table-container">
		<table id="lista-usuarios" class="table table-hover" style="width: 100%;">
			<thead>
				<tr style="background: #fcfcfc;">
					<th width="60">ID</th>
					<th>Nome</th>
					<th>Email</th>
					<th>Tipo</th>
					<th>Interações</th>
					<th>Acesso Serviços</th>
					<th width="100">Status</th>
					<th width="120" class="text-center">Ações</th>
				</tr>
			</thead>

			<tbody>
				@foreach($usuarios as $user)
				<tr>
					<td><code>#{{$user->id}}</code></td>
					<td>
						@php
							$words = explode(' ', $user->name);
							$initials = '';
							if (count($words) >= 2) {
								$initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
							} else if (count($words) == 1) {
								$initials = strtoupper(substr($words[0], 0, 2));
							} else {
								$initials = 'US';
							}
							
							$colorPalette = ['#34495e', '#2ecc71', '#3498db', '#9b59b6', '#e67e22', '#e74c3c', '#1abc9c'];
							$color = $colorPalette[($user->id) % count($colorPalette)];
						@endphp
							@if($user->avatar_url)
					<img src="{{ $user->avatar_url }}" class="user-avatar" style="object-fit: cover;" alt="{{ $user->name }}">
					@else
					<div class="user-avatar" style="background-color: {{ $color }};">
						{{ $initials }}
					</div>
					@endif
					<span style="font-weight: 600; color: #333; vertical-align: middle;">{{$user->name}}</span>
					</td>
					<td>{{$user->email}}</td>
					<td>
						@if($user->privileges == 'admin')
							<span class="label bg-blue" style="border-radius: 4px;">Admin</span>
						@elseif($user->privileges == 'cliente')
							<span class="label bg-orange" style="border-radius: 4px;">Cliente</span>
						@else
							<span class="label bg-gray" style="border-radius: 4px;">Usuário</span>
						@endif
					</td>
					<td>
						<button class="btn btn-xs btn-toggle-permission {{ $user->permitir_interacoes ? 'btn-success' : 'btn-default' }}" 
								data-url="{{ route('usuario.toggleInteracoes', $user->id) }}" 
								data-type="interacoes"
								style="border-radius: 50px; padding: 2px 10px; font-weight: 600; min-width: 90px; transition: all 0.2s;">
							@if($user->permitir_interacoes)
								<i class="fa fa-check-circle"></i> Liberado
							@else
								<i class="fa fa-ban"></i> Bloqueado
							@endif
						</button>
					</td>
					<td>
						<button class="btn btn-xs btn-toggle-permission {{ $user->permitir_acesso_servicos ? 'btn-success' : 'btn-default' }}" 
								data-url="{{ route('usuario.toggleAcessoServicos', $user->id) }}" 
								data-type="servicos"
								style="border-radius: 50px; padding: 2px 10px; font-weight: 600; min-width: 90px; transition: all 0.2s;">
							@if($user->permitir_acesso_servicos)
								<i class="fa fa-check-circle"></i> Liberado
							@else
								<i class="fa fa-ban"></i> Bloqueado
							@endif
						</button>
					</td>
					<td>
						@if($user->active == 1)
							<span class="label label-success" style="padding: 5px 10px; border-radius: 4px;">Ativo</span>
						@else
							<span class="label label-danger" style="padding: 5px 10px; border-radius: 4px;">Inativo</span>
						@endif
					</td>
					<td class="text-center">
						<a href="{{route('usuario.editar', $user->id)}}" class="btn btn-default btn-action" title="Editar">
							<i class="fa fa-edit text-primary"></i>
						</a>
						<a href="{{route('usuario.delete', $user->id)}}" class="btn btn-default btn-action confirmation" title="Excluir">
							<i class="fa fa-trash text-danger"></i>
						</a>
            		</td>
				</tr>
				@endforeach
			</tbody>
		</table>	
	</div>

@stop

@section('js')
<script>
	$(function () {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$('#lista-usuarios').DataTable({
			"paging": true,
			"lengthChange": false,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json",
				"search": "Buscar:"
			}
		});

		$('.confirmation').on('click', function (e) {
			e.preventDefault();
			var href = $(this).attr('href');
			if (confirm('Você realmente deseja excluir este usuário?')) {
				window.location.href = href;
			}
		});

		$(document).on('click', '.btn-toggle-permission', function (e) {
			e.preventDefault();
			var btn = $(this);
			var url = btn.data('url');
			var type = btn.data('type');

			btn.prop('disabled', true);

			$.ajax({
				url: url,
				method: 'POST',
				success: function (response) {
					if (response.success) {
						var allowed = (type === 'interacoes') ? response.permitir_interacoes : response.permitir_acesso_servicos;
						if (allowed) {
							btn.removeClass('btn-default').addClass('btn-success');
							btn.html('<i class="fa fa-check-circle"></i> Liberado');
						} else {
							btn.removeClass('btn-success').addClass('btn-default');
							btn.html('<i class="fa fa-ban"></i> Bloqueado');
						}
					} else {
						alert('Erro ao alterar permissão.');
					}
				},
				error: function () {
					alert('Erro na comunicação com o servidor.');
				},
				complete: function () {
					btn.prop('disabled', false);
				}
			});
		});
	});
</script>
@stop