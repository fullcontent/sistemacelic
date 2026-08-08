@php
    $targetSituacao = $situacao ?? 'andamento';
    $filteredServicos = $servicos->where('tipo','licencaOperacao')->where('situacao', $targetSituacao);
@endphp

<div class="box box-info" style="border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 20px; display: flex; flex-direction: column; flex: 1; height: 100%;">
    <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 12px 15px;">
        <h3 class="box-title" style="font-weight: 700; color: #2c3e50; font-size: 15px; margin: 0;">
            <i class="fa fa-certificate text-info"></i> Licenças de Operação
        </h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="flex: 1; padding: 15px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle no-margin" id="licencaOperacao-{{ $targetSituacao }}">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="font-weight: 600; color: #475569; padding: 10px;">Serviço</th>
                        <th style="font-weight: 600; color: #475569; padding: 10px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filteredServicos as $servico)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 10px;">
                                <a href="{{ route('cliente.servico.show', $servico->id) }}" style="color: #2563eb; font-weight: 600; text-decoration: none;">
                                    {{ $servico->os ? $servico->os . ' | ' : '' }}{{ $servico->nome }}
                                </a>
                            </td>
                            <td style="padding: 10px;">
                                @switch($servico->situacao)
                                    @case('andamento')
                                        @if($servico->licenca_validade >= date('Y-m-d'))
                                            <span class="label label-success" style="border-radius: 4px; padding: 4px 8px; font-weight: 500;">Andamento</span>
                                        @else
                                            <span class="label label-danger" style="border-radius: 4px; padding: 4px 8px; font-weight: 500;">Vencido</span>
                                        @endif
                                        @break
                                    @case('finalizado')
                                        <span class="label label-success" style="border-radius: 4px; padding: 4px 8px; font-weight: 500; background: #10b981;">Finalizado</span>
                                        @break
                                    @case('standBy')
                                        <span class="label label-warning" style="border-radius: 4px; padding: 4px 8px; font-weight: 500; background: #f59e0b;">Stand By</span>
                                        @break
                                    @case('cancelado')
                                        <span class="label label-danger" style="border-radius: 4px; padding: 4px 8px; font-weight: 500; background: #ef4444;">Cancelado</span>
                                        @break
                                    @default
                                        <span class="label label-default" style="border-radius: 4px; padding: 4px 8px; font-weight: 500;">{{ ucfirst($servico->situacao) }}</span>
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted" style="padding: 15px;">Nenhum serviço encontrado nesta situação.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
