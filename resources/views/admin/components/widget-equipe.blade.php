<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-users text-blue"></i> Equipe do Serviço & Histórico de Transferências</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <!-- Membros Ativos -->
            <div class="col-md-6">
                <h4 style="font-weight: 600; margin-bottom: 15px; color: #3c8dbc;">Membros Ativos</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr style="background-color: #f4f4f4;">
                                <th>Papel</th>
                                <th>Membro</th>
                                <th>Vinculado em</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $membrosAtivos = $servico->membrosEquipeAtivos()->with('user')->get();
                            @endphp
                            @if($membrosAtivos->count() > 0)
                                @foreach($membrosAtivos as $membro)
                                    <tr>
                                        <td>
                                            @if($membro->papel == 'coordenador')
                                                <span class="label label-primary">Coordenador</span>
                                            @elseif($membro->papel == 'responsavel_tecnico')
                                                <span class="label label-success">Resp. Técnico</span>
                                            @else
                                                <span class="label label-info">Analista</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($membro->user)
                                                <strong>{{ $membro->user->name }}</strong>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $membro->data_vinculo ? \Carbon\Carbon::parse($membro->data_vinculo)->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                {{-- Fallback para colunas clássicas caso a pivot esteja vazia --}}
                                @if($servico->responsavel)
                                    <tr>
                                        <td><span class="label label-success">Resp. Técnico</span></td>
                                        <td><strong>{{ $servico->responsavel->name }}</strong></td>
                                        <td>-</td>
                                    </tr>
                                @endif
                                @if($servico->coresponsavel)
                                    <tr>
                                        <td><span class="label label-default">Co-Responsável</span></td>
                                        <td><strong>{{ $servico->coresponsavel->name }}</strong></td>
                                        <td>-</td>
                                    </tr>
                                @endif
                                @if($servico->analista1)
                                    <tr>
                                        <td><span class="label label-info">Analista 1</span></td>
                                        <td><strong>{{ $servico->analista1->name }}</strong></td>
                                        <td>-</td>
                                    </tr>
                                @endif
                                @if($servico->analista2)
                                    <tr>
                                        <td><span class="label label-info">Analista 2</span></td>
                                        <td><strong>{{ $servico->analista2->name }}</strong></td>
                                        <td>-</td>
                                    </tr>
                                @endif
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Histórico de Transferências -->
            <div class="col-md-6">
                <h4 style="font-weight: 600; margin-bottom: 15px; color: #dd4b39;">Histórico de Transferências</h4>
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-bordered">
                        <thead>
                            <tr style="background-color: #f4f4f4;">
                                <th>Membro</th>
                                <th>Papel</th>
                                <th>Período</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $historicoEquipe = $servico->membrosEquipe()->with('user')->orderBy('created_at', 'desc')->get();
                            @endphp
                            @if($historicoEquipe->count() > 0)
                                @foreach($historicoEquipe as $reg)
                                    @if($reg->user)
                                        <tr class="{{ !$reg->ativo ? 'text-muted' : '' }}" style="{{ !$reg->ativo ? 'background-color: #fafafa;' : '' }}">
                                            <td>{{ $reg->user->name }}</td>
                                            <td>
                                                @if($reg->papel == 'coordenador')
                                                    Coordenador
                                                @elseif($reg->papel == 'responsavel_tecnico')
                                                    Resp. Técnico
                                                @else
                                                    Analista
                                                @endif
                                            </td>
                                            <td>
                                                <span style="font-size: 0.85em;">
                                                    {{ $reg->data_vinculo ? \Carbon\Carbon::parse($reg->data_vinculo)->format('d/m/Y') : '' }}
                                                    a 
                                                    {{ $reg->data_desvinculo ? \Carbon\Carbon::parse($reg->data_desvinculo)->format('d/m/Y') : ($reg->ativo ? 'Atual' : '-') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($reg->ativo)
                                                    <span class="badge bg-green">Ativo</span>
                                                @else
                                                    <span class="badge bg-gray">Transferido</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-muted" style="padding: 20px;">Nenhuma transferência de responsabilidade registrada ainda.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
