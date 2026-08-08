@php
    $isUnidade = isset($dados->empresa_id) || isset($dados->endereco);
    $statusMap = [
        'ativa' => '#10b981',
        'active' => '#10b981',
        'inativa' => '#ef4444',
        'inactive' => '#ef4444',
        'prospeccao' => '#0ea5e9',
        'inauguracao' => '#f59e0b',
    ];
    $statusKey = strtolower($dados->status ?? '');
    $statusColor = isset($statusMap[$statusKey]) ? $statusMap[$statusKey] : '#64748b';
@endphp

<div class="box box-primary" style="border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 20px;">
    <!-- Header -->
    <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="background: #f1f5f9; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #2563eb; font-size: 18px;">
                <i class="fa {{ $isUnidade ? 'fa-building-o' : 'fa-building' }}"></i>
            </div>
            <div>
                <h3 class="box-title" style="font-weight: 700; color: #1e293b; font-size: 17px; margin: 0; display: flex; align-items: center; gap: 8px;">
                    @if($isUnidade && isset($dados->id))
                        <a href="{{ route('cliente.unidade.show', $dados->id) }}" style="color: #1e293b; text-decoration: none;">
                            {{ $dados->nomeFantasia }}
                        </a>
                    @else
                        {{ $dados->nomeFantasia ?? '' }}
                    @endif
                    @if(!empty($dados->codigo))
                        <span class="label" style="background: #e2e8f0; color: #334155; font-size: 12px; font-weight: 600; border-radius: 4px; padding: 3px 8px;">
                            Cód: {{ $dados->codigo }}
                        </span>
                    @endif
                </h3>
                <span style="font-size: 13px; color: #64748b; margin-top: 2px; display: block;">
                    {{ $dados->razaoSocial ?? '' }}
                </span>
            </div>
        </div>

        <div>
            <span class="label" style="background: {{ $statusColor }}; color: #fff; font-size: 12px; font-weight: 600; border-radius: 50px; padding: 5px 14px; text-transform: uppercase;">
                {{ ucfirst($dados->status ?? 'Ativa') }}
            </span>
        </div>
    </div>

    <!-- Body -->
    <div class="box-body" style="padding: 20px;">
        <div class="row" style="display: flex; flex-wrap: wrap; row-gap: 15px;">
            <!-- Column 1: Cadastral Information -->
            <div class="col-md-4 col-sm-6" style="border-right: 1px solid #f1f5f9;">
                <div style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                    <i class="fa fa-id-card-o text-primary" style="margin-right: 6px;"></i> Dados Cadastrais
                </div>
                <div style="font-size: 13px; line-height: 1.8; color: #334155;">
                    <div><strong style="color: #64748b;">CNPJ:</strong> {{ $dados->cnpj ?? '-' }}</div>
                    <div><strong style="color: #64748b;">Insc. Estadual:</strong> {{ $dados->inscricaoEst ?? '-' }}</div>
                    <div><strong style="color: #64748b;">Insc. Municipal:</strong> {{ $dados->inscricaoMun ?? '-' }}</div>
                    @if(!empty($dados->telefone))
                        <div><strong style="color: #64748b;">Telefone:</strong> {{ $dados->telefone }}</div>
                    @endif
                </div>
            </div>

            <!-- Column 2: Property / Registries Information -->
            <div class="col-md-4 col-sm-6" style="border-right: 1px solid #f1f5f9;">
                <div style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                    <i class="fa fa-map-o text-primary" style="margin-right: 6px;"></i> Registros & Imóvel
                </div>
                <div style="font-size: 13px; line-height: 1.8; color: #334155;">
                    <div><strong style="color: #64748b;">Insc. Imobiliária:</strong> {{ $dados->inscricaoImo ?? '-' }}</div>
                    <div><strong style="color: #64748b;">Matrícula RI:</strong> {{ $dados->matriculaRI ?? '-' }}</div>
                    <div><strong style="color: #64748b;">Área da Loja:</strong> {{ $dados->area ? $dados->area . ' m²' : '-' }}</div>
                    <div><strong style="color: #64748b;">Tipo Imóvel:</strong> {{ $dados->tipoImovel ?? '-' }}</div>
                </div>
            </div>

            <!-- Column 3: Location / Address -->
            <div class="col-md-4 col-sm-12">
                <div style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                    <i class="fa fa-map-marker text-primary" style="margin-right: 6px;"></i> Localização
                </div>
                <div style="font-size: 13px; line-height: 1.8; color: #334155;">
                    <div><strong style="color: #64748b;">Endereço:</strong> {{ $dados->endereco ?? '-' }}{{ !empty($dados->numero) ? ', ' . $dados->numero : '' }}</div>
                    @if(!empty($dados->complemento))
                        <div><strong style="color: #64748b;">Complemento:</strong> {{ $dados->complemento }}</div>
                    @endif
                    <div><strong style="color: #64748b;">Bairro:</strong> {{ $dados->bairro ?? '-' }}</div>
                    <div><strong style="color: #64748b;">Cidade/UF:</strong> {{ $dados->cidade ?? '-' }}/{{ $dados->uf ?? '-' }}</div>
                    <div><strong style="color: #64748b;">CEP:</strong> {{ $dados->cep ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>