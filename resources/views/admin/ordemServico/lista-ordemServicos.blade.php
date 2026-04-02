@extends('adminlte::page')

@section('title', 'Ordens de Serviço')

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
        font-size: 1.8em;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1;
    }

    .filter-box {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        border: 1px solid #ebf0f5;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .table-container {
        background: #fff;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    /* Rating Stars */
    :root {
      --star-size: 18px;
      --star-color: #fff;
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
      letter-spacing: 2px;
      background: linear-gradient(90deg, var(--star-background) var(--percent), var(--star-color) var(--percent));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .mb-4 { margin-bottom: 20px !important; }
</style>
@stop

@section('content_header')
<div class="row" style="margin-bottom: 20px;">
    <div class="col-sm-6">
        <h1 style="margin: 0; font-weight: 700; color: #333;">Ordens de Serviço</h1>
    </div>
    <div class="col-sm-6 text-right">
        <a class="btn btn-primary" href="{{route('ordemServico.criar')}}" style="border-radius: 50px; padding: 8px 20px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-right: 10px;">
            <i class="fa fa-plus"></i> Nova OS
        </a>
        <a class="btn btn-default" href="{{route('servico.lista')}}" style="border-radius: 50px; padding: 8px 20px; font-weight: 600; border: 1px solid #ddd; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <i class="fa fa-wrench"></i> Ir para Serviços
        </a>
    </div>
</div>
@stop

@section('content')

    <!-- Dashboard Section -->
    <div class="row" style="margin: 0 -10px;">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="dashboard-card" style="border-left-color: #3c8dbc;">
                <span class="card-label">Total de O.S.</span>
                <span class="card-value">{{ $stats['total'] }}</span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="dashboard-card" style="border-left-color: #00c0ef;">
                <span class="card-label">Criadas este Mês</span>
                <span class="card-value">{{ $stats['mes_atual'] }}</span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="dashboard-card" style="border-left-color: #f39c12;">
                <span class="card-label">Em Aberto</span>
                <span class="card-value">{{ $stats['abertas'] }}</span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="dashboard-card" style="border-left-color: #00a65a;">
                <span class="card-label">Valor Total</span>
                <span class="card-value">R$ {{ number_format($stats['valor_total'], 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Box -->
    <div class="filter-box">
        <div class="row">
            <div class="col-md-4">
                <label style="color: #7f8c8d; font-size: 0.9em;">Prestador</label>
                <select id="filtro_prestador" class="form-control select2" style="width: 100%;">
                    <option value="">Todos os Prestadores</option>
                    @foreach($prestadores as $id => $nome)
                        <option value="{{ $id }}">{{ $nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label style="color: #7f8c8d; font-size: 0.9em;">Situação</label>
                <select id="filtro_situacao" class="form-control select2" style="width: 100%;">
                    <option value="">Todas</option>
                    <option value="aberto">Em Aberto</option>
                    <option value="pago">Pago</option>
                </select>
            </div>
            <div class="col-md-2" style="padding-top: 25px;">
                <button id="btn_limpar_filtros" class="btn btn-default" style="border-radius: 50px;">
                    <i class="fa fa-eraser"></i> Limpar
                </button>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table id="tabela-ordens" class="table table-hover" style="width:100%">
            <thead>
                <tr style="background: #fcfcfc;">
                    <th width="80">ID</th>
                    <th>Prestador</th>
                    <th>Serviço</th>
                    <th>Escopo</th>
                    <th>Valor</th>
                    <th width="100">Pagamento</th>
                    <th width="120">Situação</th>
                    <th width="150" class="text-center">Avaliação</th>
                    <th width="80" class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                {{-- DataTables Server-Side will populate this --}}
            </tbody>
        </table>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="modal-avaliacoes">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 8px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Avaliações do Prestador</h4>
                </div>
                <div class="modal-body">
                    <ul class="products-list product-list-in-box"></ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-rate">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 8px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Avaliar prestador</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['route' => 'prestador.rate', 'id' => 'prestadorRate']) !!}
                    @include('admin.prestadores.form-prestadorComentario')
                    <button type="submit" class="btn btn-primary" style="margin-top: 15px; border-radius: 50px;">Enviar Avaliação</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(function () {
            $('.select2').select2();

            var table = $('#tabela-ordens').DataTable({
                "processing": true,
                "serverSide": true,
                "pageLength": 25,
                "ajax": {
                    "url": "{{ route('ordemServico.listData') }}",
                    "data": function (d) {
                        d.prestador_id = $('#filtro_prestador').val();
                        d.situacao = $('#filtro_situacao').val();
                    }
                },
                "columns": [
                    { 
                        "data": "id",
                        "render": function(data, type, row) {
                            return '<a href="' + row.edit_url + '" style="font-weight: 700; color: #3c8dbc;">#' + data + '</a>';
                        }
                    },
                    { 
                        "data": "prestador_nome",
                        "render": function(data, type, row) {
                            return '<div style="font-weight: 600;">' + data + '</div>';
                        }
                    },
                    { 
                        "data": "servico_os",
                        "render": function(data, type, row) {
                            var link = row.servico_show_url ? '<a href="' + row.servico_show_url + '" style="font-weight: 700; color: #354256;">' + data + '</a>' : '<div style="font-weight: 600; color: #354256;">' + data + '</div>';
                            return link + '<br><small class="text-muted">' + row.servico_nome + '</small>';
                        }
                    },
                    { "data": "escopo", "className": "text-muted", "style": "font-size: 0.9em;" },
                    { "data": "valor", "className": "font-weight-600" },
                    { "data": "formaPagamento" },
                    { "data": "situacao_html" },
                    { 
                        "data": "rating_html",
                        "className": "text-center",
                        "render": function(data, type, row) {
                            return data + row.view_ratings_btn;
                        }
                    },
                    { 
                        "data": "acoes", 
                        "className": "text-center",
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "order": [[0, 'desc']],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
                }
            });

            // Filters
            $('#filtro_prestador, #filtro_situacao').change(function() {
                table.draw();
            });

            $('#btn_limpar_filtros').click(function() {
                $('#filtro_prestador, #filtro_situacao').val('').trigger('change');
            });

            // Rate Button
            $(document).on("click", ".rate-btn", function () {
                var ordemServico_id = $(this).data('id');
                var prestador_id = $(this).data('prestador');
                $("#ordemServico_id").val(ordemServico_id);
                $("#prestador_id").val(prestador_id);
                $('#modal-rate').modal('show');
            });

            // Show Ratings
            $(document).on("click", ".rates-show-btn", function () {
                var id = $(this).data('id');
                var prestador_id = $(this).data('prestador');
                $(".modal-body .products-list").empty();

                $.ajax({
                    url: "/admin/prestador/ratings/" + prestador_id,
                    type: "GET",
                    data: {
                        ordemServico_id: id,
                        prestador_id: prestador_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        $.each(response, function (key, value) {
                            var ratingStars = "<div class='Stars' style='--rating:" + value.rating + ";'></div>";
                            $(".modal-body .products-list").append(
                                "<li class='item'><div class='product-info'>" +
                                "<span class='product-title'>" + value.user.name + " " + ratingStars + "</span>" +
                                "<span class='product-description'>" + (value.comentario || 'Sem comentário') + "</span>" +
                                "</div></li>"
                            );
                        });
                        $('#modal-avaliacoes').modal('show');
                    }
                });
            });

            // Delete Button
            $(document).on("click", ".delete-btn", function () {
                var id = $(this).data('id');
                var url = $(this).data('url');
                
                if (confirm('Tem certeza que deseja excluir esta Ordem de Serviço #'+id+'? Esta ação não pode ser desfeita.')) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(result) {
                            table.ajax.reload();
                            // Toastr or Alert notification could be added here
                        },
                        error: function(xhr) {
                            alert('Erro ao excluir a Ordem de Serviço. Tente novamente.');
                        }
                    });
                }
            });
        });
    </script>
@endsection