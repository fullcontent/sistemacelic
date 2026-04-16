@extends('adminlte::page')

@section('title', 'Demandas GitHub - ' . ($repoInfo['name'] ?? 'Dashboard'))

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1 style="font-weight: 700;">
            <i class="fa fa-github"></i> Gestão de Demandas
            <small>{{ $repoInfo['full_name'] ?? 'sistemacelic' }}</small>
        </h1>
    </div>
    <div class="col-sm-6 text-right">
        <div class="btn-group shadow-sm" style="margin-right: 15px;">
            <a href="{{ route('admin.github', ['state' => 'open']) }}"
                class="btn btn-default {{ $state == 'OPEN' ? 'active' : '' }}"
                style="font-weight: 600; border-radius: 8px 0 0 8px;">
                <i class="fa fa-folder-open text-green"></i> Abertas
            </a>
            <a href="{{ route('admin.github', ['state' => 'closed']) }}"
                class="btn btn-default {{ $state == 'CLOSED' ? 'active' : '' }}"
                style="font-weight: 600; border-radius: 0 8px 8px 0;">
                <i class="fa fa-check-circle text-red"></i> Fechadas
            </a>
        </div>
        <button class="btn btn-success shadow-sm" style="border-radius: 8px; font-weight: 700; padding: 8px 20px;"
            data-toggle="modal" data-target="#modalCreateIssue">
            <i class="fa fa-plus-circle"></i> NOVA DEMANDA
        </button>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Pulse Chart Box -->
        <div class="box box-success"
            style="border-radius: 12px; border-top-width: 3px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-bottom: 20px;">
            <div class="box-header with-border" style="padding: 12px 15px;">
                <h3 class="box-title" style="font-weight: 700; color: #333; font-size: 15px;">
                    <i class="fa fa-line-chart text-success"></i> PULSO DE ATIVIDADE MENSAL
                </h3>
            </div>
            <div class="box-body" style="padding: 15px;">
                <canvas id="monthlyPulseChart" style="height: 100px; width: 100%;"></canvas>
            </div>
        </div>

        <div class="box box-primary"
            style="border-radius: 12px; border-top-width: 3px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
            <div class="box-header with-border" style="padding: 12px 15px;">
                <h3 class="box-title" style="font-weight: 700; color: #333; font-size: 15px;">
                    <i class="fa fa-list-ul text-primary"></i>
                    {{ $state == 'OPEN' ? 'DEMANDAS EM ABERTO' : 'HISTÓRICO DE FECHADAS' }}
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover align-middle" id="issues-table">
                    <thead>
                        <tr
                            style="background: #f9fafb; color: #555; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">
                            <th style="width: 60px; padding: 10px; text-align: center; border-bottom: 1px solid #eee;">
                                Nº</th>
                            <th style="padding: 10px; border-bottom: 1px solid #eee;">Título da Demanda</th>
                            <th style="padding: 10px; width: 140px; text-align: right; border-bottom: 1px solid #eee;">
                                Data de Cadastro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issues as $i)
                            <tr style="cursor: pointer; transition: all 0.2s;" class="btn-show-issue"
                                data-number="{{ $i['number'] }}">
                                <td style="padding: 10px; text-align: center; vertical-align: middle;">
                                    <span class="text-muted"
                                        style="font-weight: 600; font-size: 13px;">#{{ $i['number'] }}</span>
                                </td>
                                <td style="padding: 10px; vertical-align: middle;">
                                    <span style="color: #2c3e50; font-weight: 600; font-size: 14px;">
                                        {{ $i['title'] }}
                                    </span>
                                </td>
                                <td style="padding: 10px; text-align: right; vertical-align: middle;">
                                    <span class="text-muted" style="font-size: 13px;">
                                        <i class="fa fa-calendar-o"></i> {{ date('d/m/Y', strtotime($i['createdAt'])) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center" style="color: #999; padding: 30px;">
                                    <i class="fa fa-folder-open-o fa-2x" style="opacity: 0.5;"></i><br>
                                    Nenhuma demanda encontrada neste estado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="box-footer text-center"
                style="background: #fcfcfc; border-radius: 0 0 12px 12px; padding: 10px;">
                <span class="text-muted small">Total: <b>{{ count($issues) }}</b> demandas exibidas</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Criar Nova Demanda -->
<div class="modal fade" id="modalCreateIssue" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="formCreateIssue">
            @csrf
            <div class="modal-content" style="border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header bg-green" style="border-radius: 12px 12px 0 0;">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" style="font-weight: 700;"><i class="fa fa-plus-circle"></i> CRIAR NOVA
                        DEMANDA</h4>
                </div>
                <div class="modal-body" style="padding: 25px;">
                    <div class="form-group">
                        <label style="color: #555;">TÍTULO DA TAREFA</label>
                        <input type="text" name="title" class="form-control input-lg" style="border-radius: 8px;"
                            placeholder="Ex: Ajustar carregamento do relatório..." required>
                    </div>
                    @if(isset($projects) && !empty($projects))
                        <div class="form-group">
                            <label style="color: #555;">PROJETO (OPCIONAL)</label>
                            <select name="project_id" class="form-control" style="border-radius: 8px;">
                                <option value="">-- Adicionar a um Kanban --</option>
                                @foreach($projects as $proj)
                                    @if(is_array($proj) && isset($proj['id']))
                                        <option value="{{ $proj['id'] }}">{{ $proj['title'] ?? 'Novo Kanban' }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="form-group">
                        <label style="color: #555;">DESCRIÇÃO DETALHADA</label>
                        <textarea name="body" class="form-control" rows="6"
                            style="border-radius: 8px; resize: vertical;"
                            placeholder="Descreva os requisitos ou o bug encontrado..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f9f9f9; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"
                        style="font-weight: 600;">CANCELAR</button>
                    <button type="submit" class="btn btn-success" id="btnSaveNewIssue"
                        style="font-weight: 700; padding: 8px 25px;">CRIAR DEMANDA AGORA</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Ver Detalhes da Issue -->
<div class="modal fade" id="modalIssue" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"
            style="border-radius: 15px; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: #f8f9fa; border-bottom: 2px solid #eee; padding: 20px;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="issueTitleDisplay"
                    style="font-weight: 800; color: #2c3e50; font-size: 20px;">Carregando...</h4>
            </div>
            <div class="modal-body" style="padding: 30px; background: #fff;">
                <div id="issueBodyDisplay"
                    style="min-height: 100px; font-size: 15px; color: #34495e; line-height: 1.6; background: #fdfdfd; padding: 25px; border-radius: 10px; border: 1px solid #f0f0f0;">
                </div>
                <hr style="border-top: 1px solid #eaeaea; margin: 30px 0;">
                <h5 style="font-weight: 700; color: #555; margin-bottom: 20px;"><i class="fa fa-comments-o"></i>
                    INTERAÇÕES:</h5>
                <div id="issueCommentsDisplay"></div>
            </div>
            <div class="modal-footer" style="background: #f8f9fa; padding: 15px 25px;">
                <span id="issueMeta" class="pull-left text-muted" style="margin-top: 8px; font-weight: 600;"></span>
                <button type="button" class="btn btn-default" data-dismiss="modal"
                    style="font-weight: 600;">FECHAR</button>
                <a id="issueGithubLink" href="#" target="_blank" class="btn btn-primary"
                    style="font-weight: 700; padding: 6px 20px;"><i class="fa fa-github"></i> ABRIR NO GITHUB</a>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .shadow-sm {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }

    .table>tbody>tr:hover {
        background-color: #f4f7f9 !important;
    }

    .btn-group .active {
        background-color: #3c8dbc !important;
        color: #fff !important;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Sparkline Custom */
    .sparkline-container {
        display: flex;
        align-items: flex-end;
        gap: 3px;
        height: 35px;
        padding: 0 10px;
        border-left: 1px solid #eee;
    }

    .spark-bar {
        width: 4px;
        background: #3c8dbc;
        border-radius: 2px;
    }

    /* Transition */
    .btn-link:hover {
        transform: scale(1.2);
        transition: all 0.2s;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    $(function () {
        // ChartJS Pulse
        var ctxDom = document.getElementById('monthlyPulseChart');
        if (ctxDom) {
            var ctx = ctxDom.getContext('2d');
            var chartLabels = {!! json_encode(array_keys($chartData)) !!};
            var chartValues = {!! json_encode(array_values($chartData)) !!};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Commits',
                        data: chartValues,
                        backgroundColor: 'rgba(0, 166, 90, 0.15)',
                        borderColor: '#00a65a',
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#00a65a',
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { display: false, min: 0 }
                    }
                }
            });
        }

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // Abrir Modal de Detalhes
        $(document).on('click', '.btn-show-issue', function () {
            var number = $(this).data('number');

            $('#issueTitleDisplay').text('Carregando...');
            $('#issueBodyDisplay').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Buscando detalhes técnicos...</div>');
            $('#modalIssue').modal('show');

            $.get('{{ route('admin.github') }}/' + number, function (data) {
                if (data) {
                    $('#issueTitleDisplay').text(data.title + ' #' + number);

                    var mdBody = data.body ? marked.parse(data.body) : '<p class="text-muted">Esta demanda não possui uma descrição estruturada.</p>';
                    $('#issueBodyDisplay').html(mdBody);

                    $('#issueMeta').html('<i class="fa fa-user"></i> ' + data.author.login + ' • <i class="fa fa-clock-o"></i> ' + new Date(data.createdAt).toLocaleDateString());
                    $('#issueGithubLink').attr('href', data.url);

                    // Renderizar Comentários
                    var commentsHtml = '';
                    if (data.comments && data.comments.nodes && data.comments.nodes.length > 0) {
                        data.comments.nodes.forEach(function (comment) {
                            var parsedComment = marked.parse(comment.body);
                            var dateComment = new Date(comment.createdAt).toLocaleDateString() + ' às ' + new Date(comment.createdAt).toLocaleTimeString();
                            commentsHtml += `
                                <div style="margin-bottom: 20px; padding: 15px; border-left: 4px solid #3c8dbc; background: #f9fbff; border-radius: 6px;">
                                    <div style="font-size: 12px; color: #666; margin-bottom: 8px;">
                                        <strong><i class="fa fa-user-circle"></i> ${comment.author.login}</strong> comentou em ${dateComment}:
                                    </div>
                                    <div style="font-size: 14px; color: #444; line-height: 1.5;">${parsedComment}</div>
                                </div>
                            `;
                        });
                    } else {
                        commentsHtml = '<p class="text-muted small" style="font-style: italic;">Nenhuma interação registrada ainda.</p>';
                    }
                    $('#issueCommentsDisplay').html(commentsHtml);

                } else {
                    $('#issueTitleDisplay').text('Erro');
                    $('#issueBodyDisplay').html('<p class="text-danger">Ocorreu um problema ao carregar os dados.</p>');
                    $('#issueCommentsDisplay').html('');
                }
            });
        });

        // Criar Nova Demanda
        $('#formCreateIssue').submit(function (e) {
            e.preventDefault();
            var btn = $('#btnSaveNewIssue');
            var formData = $(this).serialize();

            btn.html('<i class="fa fa-spinner fa-spin"></i> CRIANDO...').attr('disabled', true);
            $.post('{{ route('admin.github.issues.store') }}', formData, function () {
                location.reload();
            }).fail(function (xhr) {
                alert('Erro ao criar demanda: ' + (xhr.status == 419 ? 'Sessão expirada. Atualize a página.' : 'Erro desconhecido.'));
                btn.html('CRIAR DEMANDA AGORA').attr('disabled', false);
            });
        });
    });
</script>
@stop