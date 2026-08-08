<div class="box box-default" style="border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 25px;">
    <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 15px 20px;">
        <h3 class="box-title" style="font-weight: 700; color: #64748b; font-size: 16px; margin: 0;">
            <i class="fa fa-archive text-gray" style="margin-right: 8px;"></i> Serviços Arquivados ({{ count($servicos->where('situacao', 'arquivado')) }})
        </h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="padding: 20px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="servicosArquivadosTable" style="width: 100%;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="font-weight: 600; color: #475569; padding: 12px;">OS</th>
                        <th style="font-weight: 600; color: #475569; padding: 12px;">Serviço</th>
                        <th style="font-weight: 600; color: #475569; padding: 12px;">Tipo</th>
                        <th style="font-weight: 600; color: #475569; padding: 12px;">Unidade</th>
                        <th style="font-weight: 600; color: #475569; padding: 12px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($servicos->where('situacao', 'arquivado') as $servico)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; font-weight: 600; color: #64748b;"><code>{{ $servico->os }}</code></td>
                        <td style="padding: 12px;">
                            <a href="{{ route('cliente.servico.show', $servico->id) }}" style="color: #2563eb; font-weight: 600; text-decoration: none;">
                                {{ $servico->nome }}
                            </a>
                        </td>
                        <td style="padding: 12px; color: #64748b;">
                            @switch($servico->tipo)
                                @case('licencaOperacao')
                                    Licença de Operação
                                    @break
                                @case('nRenovaveis')
                                    Não Renovável
                                    @break
                                @case('controleCertidoes')
                                    Certidão
                                    @break
                                @case('controleTaxas')
                                    Taxa
                                    @break
                                @case('facilitiesRealEstate')
                                    Facilities/Real Estate
                                    @break
                                @default
                                    Outro
                            @endswitch
                        </td>
                        <td style="padding: 12px;">
                            @if($servico->unidade)
                                <a href="{{ route('cliente.unidade.show', $servico->unidade->id) }}" style="color: #475569; font-weight: 500;">
                                    {{ $servico->unidade->nomeFantasia }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            <span class="label label-default" style="border-radius: 4px; padding: 4px 8px; font-weight: 500; background: #94a3b8;">Arquivado</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
