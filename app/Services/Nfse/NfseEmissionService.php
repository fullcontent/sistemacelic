<?php

namespace App\Services\Nfse;

use App\Models\Faturamento;
use App\Models\FaturamentoServico;
use App\Models\NfseConfiguration;
use App\Models\NfseEmission;
use App\Models\NfseEmissionItem;
use App\Models\Servico;
use App\Helpers\IbgeHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NfseEmissionService
{
    /** @var PlugNotasClient */
    private $plugNotasClient;

    public function __construct(PlugNotasClient $plugNotasClient)
    {
        $this->plugNotasClient = $plugNotasClient;
    }

    public function emitirAutomatico(array $data)
    {
        $faturamento = Faturamento::with('servicosFaturados.detalhes.unidade', 'empresa')->findOrFail($data['faturamento_id']);
        $config = $this->resolveConfig($data);

        $servicoIds = isset($data['servico_ids']) ? $data['servico_ids'] : [];
        $opcao = $data['opcao_automatica']; // 1, 2, 3, 4
        $tomadorOverride = isset($data['tomador_override']) ? $data['tomador_override'] : null;
        $camposAdicionais = isset($data['campos_adicionais']) ? $data['campos_adicionais'] : [];

        $faturamentoServicos = FaturamentoServico::with('detalhes.unidade', 'detalhes.financeiro')
            ->where('faturamento_id', $faturamento->id)
            ->whereIn('servico_id', $servicoIds)
            ->get();

        if ($faturamentoServicos->isEmpty()) {
            throw new \InvalidArgumentException('Nenhum serviço válido encontrado para o faturamento informado.');
        }

        $this->ensureServicosNotDuplicated($servicoIds);

        $emission = NfseEmission::create([
            'faturamento_id' => $faturamento->id,
            'nfse_configuration_id' => $config->id,
            'modo' => 'automatico',
            'opcao_automatica' => $opcao,
            'status' => 'processando',
            'observacoes' => json_encode(['tomador_override' => $tomadorOverride]),
        ]);

        try {
            return DB::transaction(function () use ($data, $faturamento, $config, $servicoIds, $opcao, $tomadorOverride, $camposAdicionais, $faturamentoServicos, $emission) {
            // Determinar o modo PlugNotas com base na opção
            // Opções 1 e 2 são INDIVIDUAIS
            // Opções 3 e 4 são AGRUPADAS
            $isAgrupada = ($opcao == '3' || $opcao == '4');
            $useOverride = ($opcao == '2' || $opcao == '4' || !empty($tomadorOverride));

            $items = [];
            $payloads = [];
            $retornos = [];

            if ($isAgrupada) {
                // Opção 3 ou 4: Agrupado
                $tomadorData = $this->resolveTomadorData($faturamento, null, $opcao, $tomadorOverride);
                $groupedItem = $this->buildGroupedItem($faturamentoServicos, $tomadorData['cpfCnpj']);
                
                $payload = NfsePayloadFactory::buildBasePayload($config->toArray(), $groupedItem, $camposAdicionais);
                $payload['tomador'] = $tomadorData;
                
                // Call API without inner catch
                $retorno = $this->plugNotasClient->emitirNfse($payload);

                $item = NfseEmissionItem::create([
                    'nfse_emission_id' => $emission->id,
                    'cnpj_tomador' => $tomadorData['cpfCnpj'],
                    'descricao_servico' => $groupedItem['descricao_servico'],
                    'valor_servico' => $groupedItem['valor_servico'],
                ]);

                $this->updateItemFromPlugNotasStatus($item, $retorno);
                $items[] = $item;

                $payloads[] = $payload;
                $retornos[] = $retorno;
            } else {
                // Opção 1 ou 2: Individual
                foreach ($faturamentoServicos as $faturamentoServico) {
                    $servico = $faturamentoServico->detalhes;
                    if (!$servico) continue;

                    $tomadorData = $this->resolveTomadorData($faturamento, $servico, $opcao, $tomadorOverride);
                    $itemData = $this->buildItemData($servico, $faturamentoServico, $tomadorData['cpfCnpj']);

                    $payload = NfsePayloadFactory::buildBasePayload($config->toArray(), $itemData, $camposAdicionais);
                    $payload['tomador'] = $tomadorData;
                    
                        // Call API without inner catch
                        $retorno = $this->plugNotasClient->emitirNfse($payload);

                    $item = NfseEmissionItem::create([
                        'nfse_emission_id' => $emission->id,
                        'servico_id' => $servico->id,
                        'faturamento_servico_id' => $faturamentoServico->id,
                        'cnpj_tomador' => $tomadorData['cpfCnpj'],
                        'descricao_servico' => $itemData['descricao_servico'],
                        'valor_servico' => $itemData['valor_servico'],
                    ]);
                    
                    $this->updateItemFromPlugNotasStatus($item, $retorno);
                    $items[] = $item;

                    $payloads[] = $payload;
                    $retornos[] = $retorno;
                }
            }

            $emission->payload = json_encode($payloads);
            $emission->retorno = json_encode($retornos);
            
            // Finalize emission summarizing from items
            $emission->status = $this->resolveEmissionStatus($items);
            $emission->valor_total = collect($items)->sum('valor_servico');
            
            if (count($items) > 0) {
                $first = $items[0];
                $emission->pdf_url = $first->pdf_url;
                $emission->xml_url = $first->xml_url;
                $emission->numero_nf = $first->numero_nf;
            }

            $emission->save();

            return $emission->load('itens');
            });
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $msg .= ' | Resposta: ' . (string) $e->getResponse()->getBody();
            }
            $emission->status = 'erro';
            $emission->mensagem_erro = $msg;
            // Persist as much as we have
            if (isset($payloads)) {
                $emission->payload = json_encode($payloads);
            }
            $emission->save();
            throw new \Exception($msg, $e->getCode(), $e);
        }
    }

    protected function resolveTomadorData(Faturamento $faturamento, $servico, $opcao, $override)
    {
        $clean = function($val) { return preg_replace('/\D/', '', (string)$val); };

        // Se houver override (Opção 2 ou 4), usar os dados manuais
        if (($opcao == '2' || $opcao == '4') && !empty($override)) {
            return [
                'cpfCnpj' => $clean($override['cnpj']),
                'razaoSocial' => $override['razaoSocial'],
                'email' => $override['email'] ?? null,
                'endereco' => [
                    'logradouro' => $override['logradouro'] ?? '',
                    'numero' => $override['numero'] ?? '',
                    'bairro' => $override['bairro'] ?? '',
                    'codigoCidade' => $override['codigoCidade'] ?? '',
                    'cep' => $clean($override['cep'] ?? ''),
                    'uf' => $override['uf'] ?? ''
                ]
            ];
        }

        // Opção 1: Individual com dados da Unidade
        if ($opcao == '1' && $servico && !empty($servico->unidade)) {
            return [
                'cpfCnpj' => $clean($servico->unidade->cnpj),
                'razaoSocial' => $servico->unidade->razaoSocial ?? $servico->unidade->nomeFantasia,
                'email' => $servico->unidade->email,
                'endereco' => [
                    'logradouro' => $servico->unidade->endereco,
                    'numero' => $servico->unidade->numero,
                    'bairro' => $servico->unidade->bairro,
                    'codigoCidade' => IbgeHelper::getIbgeCode($servico->unidade->cidade, $servico->unidade->uf) ?? $servico->unidade->codigo_cidade ?? $servico->unidade->inscricaoMun, 
                    'cep' => $clean($servico->unidade->cep),
                    'uf' => $servico->unidade->uf
                ]
            ];
        }

        // Opção 3: Agrupada com dados da Empresa do Faturamento
        if ($opcao == '3' && $faturamento->empresa) {
            return [
                'cpfCnpj' => $clean($faturamento->empresa->cnpj),
                'razaoSocial' => $faturamento->empresa->razaoSocial ?? $faturamento->empresa->nomeFantasia,
                'email' => $faturamento->empresa->email,
                'endereco' => [
                    'logradouro' => $faturamento->empresa->endereco,
                    'numero' => $faturamento->empresa->numero,
                    'bairro' => $faturamento->empresa->bairro,
                    'codigoCidade' => IbgeHelper::getIbgeCode($faturamento->empresa->cidade, $faturamento->empresa->uf) ?? $faturamento->empresa->codigo_cidade ?? $faturamento->empresa->inscricaoMun,
                    'cep' => $clean($faturamento->empresa->cep),
                    'uf' => $faturamento->empresa->uf
                ]
            ];
        }

        throw new \InvalidArgumentException('Não foi possível determinar os dados do tomador para a opção ' . $opcao);
    }

    public function cancelar($emissionId, $motivo)
    {
        $emission = NfseEmission::with('itens')->findOrFail($emissionId);
        
        foreach ($emission->itens as $item) {
            if ($item->external_id) {
                $this->plugNotasClient->cancelarNfse($item->external_id, $motivo);
                $item->status = 'cancelada';
                $item->mensagem_erro = $motivo;
                $item->save();
            }
        }

        $emission->status = 'cancelada';
        $emission->save();

        return $emission;
    }

    public function anexarManual(array $data)
    {
        return DB::transaction(function () use ($data) {
            $faturamento = Faturamento::findOrFail($data['faturamento_id']);
            $servicoIds = isset($data['servico_ids']) ? $data['servico_ids'] : [];

            $this->ensureServicosNotDuplicated($servicoIds);

            $emission = NfseEmission::create([
                'faturamento_id' => $faturamento->id,
                'modo' => 'manual',
                'status' => 'anexada',
                'observacoes' => isset($data['observacoes']) ? $data['observacoes'] : null,
            ]);

            $pdfPath = isset($data['pdf_path']) ? $data['pdf_path'] : null;

            foreach ($servicoIds as $servicoId) {
                NfseEmissionItem::create([
                    'nfse_emission_id' => $emission->id,
                    'servico_id' => $servicoId,
                    'numero_nf' => $data['numero_nf'],
                    'status' => 'anexada',
                    'pdf_path' => $pdfPath,
                ]);
            }

            return $emission->load('itens');
        });
    }

    public function naoEmitir(array $data)
    {
        return NfseEmission::create([
            'faturamento_id' => $data['faturamento_id'],
            'modo' => 'nao_emitir',
            'status' => 'nao_emitir',
            'observacoes' => isset($data['observacoes']) ? $data['observacoes'] : null,
        ]);
    }

    public function processarWebhook(array $payload)
    {
        $externalId = isset($payload['id']) ? $payload['id'] : null;
        if (empty($externalId)) {
            throw new \InvalidArgumentException('Payload de webhook sem id externo.');
        }

        $item = NfseEmissionItem::where('external_id', $externalId)->firstOrFail();
        $this->updateItemFromPlugNotasStatus($item, $payload);

        $emission = $item->emissao;
        $emission->status = $this->resolveEmissionStatus($emission->itens()->get()->all());
        $emission->retorno = json_encode($payload);
        $emission->save();

        return $item;
    }

    public function consultarStatus($emissionId)
    {
        $emission = NfseEmission::with('itens')->findOrFail($emissionId);
        $totalItems = $emission->itens->count();
        $valorTotal = 0;

        foreach ($emission->itens as $item) {
            if ($item->external_id) {
                try {
                    $statusPlug = $this->plugNotasClient->consultarNfse($item->external_id);
                    $this->updateItemFromPlugNotasStatus($item, $statusPlug);
                } catch (\Exception $e) {
                    \Log::error("Erro ao consultar status PlugNotas para item {$item->id}: " . $e->getMessage());
                }
            }
        }

        // Refresh items explicitly to get the new URLs and status
        $items = $emission->itens()->get();
        
        foreach($items as $item) {
            if ($item->status === 'concluido' || $item->status === 'emitida' || $item->status === 'CONCLUIDA' || $item->status === 'anexada') {
                $valorTotal += (float) $item->valor_servico;
            }
        }

        $emission->status = $this->resolveEmissionStatus($items->all());
        $emission->valor_total = $valorTotal;

        // Se houver pelo menos um item, pegamos os dados da primeira nota como principal
        if ($items->count() > 0) {
            $first = $items->first();
            $emission->pdf_url = $first->pdf_url;
            $emission->xml_url = $first->xml_url;
            $emission->numero_nf = $first->numero_nf;
        }

        $emission->save();

        return $emission;
    }

    private function updateItemFromPlugNotasStatus(NfseEmissionItem $item, array $data)
    {
        $item->status = isset($data['status']) ? $data['status'] : $item->status;
        $item->numero_nf = isset($data['numero']) ? $data['numero'] : $item->numero_nf;

        $baseUrl = $this->plugNotasClient->getBaseUrl();
        $externalId = isset($data['id']) ? $data['id'] : $item->external_id;

        if ($externalId) {
            $item->pdf_url = "{$baseUrl}/nfse/pdf/{$externalId}";
            $item->xml_url = "{$baseUrl}/nfse/xml/{$externalId}";
        }

        if (!empty($data['mensagem'])) {
            $item->mensagem_erro = $data['mensagem'];
        }

        // Se for um retorno de submissão (array de documentos)
        if (isset($data['documents']) && is_array($data['documents']) && isset($data['documents'][0]['id'])) {
            $item->external_id = $data['documents'][0]['id'];
            if (isset($data['documents'][0]['status'])) {
                $item->status = $data['documents'][0]['status'];
            }
        } elseif (isset($data['id'])) {
            // Se for um retorno de consulta individual
            $item->external_id = $data['id'];
        }

        $item->additional_data = json_encode($data);
        $item->save();
    }

    public function gerarZipNotas($emissionId)
    {
        $emission = NfseEmission::with('itens')->findOrFail($emissionId);
        $zip = new \ZipArchive();

        $directory = storage_path('app/nfse/zip');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'nfse_faturamento_' . $emission->faturamento_id . '_emissao_' . $emission->id . '.zip';
        $zipPath = $directory . DIRECTORY_SEPARATOR . $fileName;

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Não foi possível gerar o arquivo ZIP.');
        }

        foreach ($emission->itens as $item) {
            if (!empty($item->pdf_path) && Storage::disk('local')->exists($item->pdf_path)) {
                $zip->addFile(storage_path('app/' . $item->pdf_path), basename($item->pdf_path));
            }
        }

        $zip->close();

        $emission->zip_path = 'nfse/zip/' . $fileName;
        $emission->save();

        return $emission;
    }

    private function resolveConfig(array $data)
    {
        if (!empty($data['nfse_configuration_id'])) {
            return NfseConfiguration::with('dadosCastro')->findOrFail($data['nfse_configuration_id']);
        }

        $query = NfseConfiguration::ativa()->with('dadosCastro');
        if (!empty($data['dados_castro_id'])) {
            $query->where('dados_castro_id', $data['dados_castro_id']);
        }

        $config = $query->orderBy('id', 'desc')->first();
        if (!$config) {
            throw new \InvalidArgumentException('Configuração ativa de NFS-e não encontrada.');
        }

        return $config;
    }

    private function ensureServicosNotDuplicated(array $servicoIds)
    {
        if (empty($servicoIds)) {
            return;
        }

        $duplicados = NfseEmissionItem::whereIn('servico_id', $servicoIds)
            ->whereIn('status', ['emitida', 'anexada', 'processando', 'concluido'])
            ->pluck('servico_id')
            ->toArray();

        if (!empty($duplicados)) {
            throw new \InvalidArgumentException('Existem serviços já vinculados a nota fiscal ativa: ' . implode(', ', $duplicados));
        }
    }

    private function buildItemData(Servico $servico, FaturamentoServico $faturamentoServico, $cnpj)
    {
        $valor = !empty($faturamentoServico->valorFaturado)
            ? (float) $faturamentoServico->valorFaturado
            : (!empty($servico->financeiro) ? (float) $servico->financeiro->valorFaturar : 0);

        return [
            'cnpj_tomador' => $cnpj,
            'descricao_servico' => !empty($servico->nome) ? $servico->nome : 'Serviço ' . $servico->id,
            'valor_servico' => $valor,
        ];
    }

    private function buildGroupedItem($faturamentoServicos, $cnpj)
    {
        $lines = [];
        $valorTotal = 0;

        foreach ($faturamentoServicos as $fs) {
            $servico = $fs->detalhes;
            if (!$servico) {
                continue;
            }

            $valor = !empty($fs->valorFaturado)
                ? (float) $fs->valorFaturado
                : (!empty($servico->financeiro) ? (float) $servico->financeiro->valorFaturar : 0);

            $valorTotal += $valor;

            $lines[] = [
                'codigo_unidade' => !empty($servico->unidade->codigo) ? $servico->unidade->codigo : '-',
                'nome_unidade' => !empty($servico->unidade->nomeFantasia) ? $servico->unidade->nomeFantasia : 'Unidade',
                'nome_servico' => !empty($servico->nome) ? $servico->nome : 'Serviço',
                'valor' => $valor,
            ];
        }

        return [
            'cnpj_tomador' => $cnpj,
            'descricao_servico' => NfsePayloadFactory::buildGroupedDescription($lines),
            'valor_servico' => round($valorTotal, 2),
        ];
    }

    private function mapStatus(array $retorno)
    {
        if (isset($retorno['status']) && !empty($retorno['status'])) {
            return $retorno['status'];
        }

        if (isset($retorno['numero']) && !empty($retorno['numero'])) {
            return 'emitida';
        }

        return 'processando';
    }

    private function resolveEmissionStatus(array $items)
    {
        $statuses = [];
        foreach ($items as $item) {
            $statuses[] = is_object($item) ? $item->status : (isset($item['status']) ? $item['status'] : null);
        }

        if (in_array('erro', $statuses, true)) {
            return 'erro';
        }

        if (in_array('processando', $statuses, true) || in_array('pendente', $statuses, true)) {
            return 'processando';
        }

        if (in_array('emitida', $statuses, true) || in_array('concluido', $statuses, true)) {
            return 'concluido';
        }

        if (in_array('anexada', $statuses, true)) {
            return 'anexada';
        }

        return 'pendente';
    }
}
