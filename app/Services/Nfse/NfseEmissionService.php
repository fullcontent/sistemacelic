<?php

namespace App\Services\Nfse;

use App\Models\Faturamento;
use App\Models\FaturamentoServico;
use App\Models\NfseConfiguration;
use App\Models\NfseEmission;
use App\Models\NfseEmissionItem;
use App\Models\Servico;
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
        return DB::transaction(function () use ($data) {
            $faturamento = Faturamento::with('servicosFaturados.detalhes.unidade')->findOrFail($data['faturamento_id']);
            $config = $this->resolveConfig($data);

            $servicoIds = isset($data['servico_ids']) ? $data['servico_ids'] : [];
            $opcao = $data['opcao_automatica'];
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
            ]);

            $items = [];

            if ($opcao === 'agrupado') {
                $manualCnpj = isset($data['cnpj_manual_agrupado']) ? $data['cnpj_manual_agrupado'] : null;
                if (empty($manualCnpj)) {
                    throw new \InvalidArgumentException('CNPJ manual é obrigatório para emissão agrupada.');
                }

                $grouped = $this->buildGroupedItem($faturamentoServicos, $manualCnpj);
                $payload = NfsePayloadFactory::buildBasePayload($config->toArray(), $grouped, $camposAdicionais);
                $retorno = $this->plugNotasClient->emitirNfse($payload);

                $items[] = NfseEmissionItem::create([
                    'nfse_emission_id' => $emission->id,
                    'cnpj_tomador' => $grouped['cnpj_tomador'],
                    'descricao_servico' => $grouped['descricao_servico'],
                    'valor_servico' => $grouped['valor_servico'],
                    'numero_nf' => isset($retorno['numero']) ? $retorno['numero'] : null,
                    'external_id' => isset($retorno['id']) ? $retorno['id'] : null,
                    'status' => $this->mapStatus($retorno),
                    'additional_data' => json_encode([
                        'servico_ids' => $servicoIds,
                        'retorno' => $retorno,
                    ]),
                ]);

                $emission->payload = json_encode([$payload]);
                $emission->retorno = json_encode([$retorno]);
                $emission->status = $items[0]->status;
                $emission->save();

                return $emission->load('itens');
            }

            $cnpjManualPorServico = isset($data['cnpj_manual_por_servico']) ? $data['cnpj_manual_por_servico'] : [];
            $payloads = [];
            $retornos = [];

            foreach ($faturamentoServicos as $faturamentoServico) {
                $servico = $faturamentoServico->detalhes;
                if (!$servico) {
                    continue;
                }

                $cnpj = $this->resolveCnpjByOption($opcao, $servico, $cnpjManualPorServico);
                $itemData = $this->buildItemData($servico, $faturamentoServico, $cnpj);

                $payload = NfsePayloadFactory::buildBasePayload($config->toArray(), $itemData, $camposAdicionais);
                $retorno = $this->plugNotasClient->emitirNfse($payload);

                $items[] = NfseEmissionItem::create([
                    'nfse_emission_id' => $emission->id,
                    'servico_id' => $servico->id,
                    'faturamento_servico_id' => $faturamentoServico->id,
                    'cnpj_tomador' => $itemData['cnpj_tomador'],
                    'descricao_servico' => $itemData['descricao_servico'],
                    'valor_servico' => $itemData['valor_servico'],
                    'numero_nf' => isset($retorno['numero']) ? $retorno['numero'] : null,
                    'external_id' => isset($retorno['id']) ? $retorno['id'] : null,
                    'status' => $this->mapStatus($retorno),
                    'additional_data' => json_encode(['retorno' => $retorno]),
                ]);

                $payloads[] = $payload;
                $retornos[] = $retorno;
            }

            $emission->payload = json_encode($payloads);
            $emission->retorno = json_encode($retornos);
            $emission->status = $this->resolveEmissionStatus($items);
            $emission->save();

            return $emission->load('itens');
        });
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

        $item->status = isset($payload['status']) ? $payload['status'] : $item->status;
        $item->numero_nf = isset($payload['numero']) ? $payload['numero'] : $item->numero_nf;

        if (!empty($payload['mensagem'])) {
            $item->mensagem_erro = $payload['mensagem'];
        }

        $item->additional_data = json_encode($payload);
        $item->save();

        $emission = $item->emissao;
        $emission->status = $this->resolveEmissionStatus($emission->itens()->get()->all());
        $emission->retorno = json_encode($payload);
        $emission->save();

        return $item;
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
            return NfseConfiguration::findOrFail($data['nfse_configuration_id']);
        }

        $query = NfseConfiguration::ativa();
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
            ->whereIn('status', ['emitida', 'anexada', 'processando'])
            ->pluck('servico_id')
            ->toArray();

        if (!empty($duplicados)) {
            throw new \InvalidArgumentException('Existem serviços já vinculados a nota fiscal: ' . implode(', ', $duplicados));
        }
    }

    private function resolveCnpjByOption($opcao, Servico $servico, array $cnpjManualPorServico)
    {
        if ($opcao === 'individual_cnpj_padrao') {
            if (!empty($servico->unidade) && !empty($servico->unidade->cnpj)) {
                return $servico->unidade->cnpj;
            }

            throw new \InvalidArgumentException('Serviço ' . $servico->id . ' sem CNPJ padrão da unidade.');
        }

        if ($opcao === 'individual_cnpj_manual') {
            if (!isset($cnpjManualPorServico[$servico->id]) || empty($cnpjManualPorServico[$servico->id])) {
                throw new \InvalidArgumentException('CNPJ manual obrigatório para o serviço ' . $servico->id . '.');
            }

            return $cnpjManualPorServico[$servico->id];
        }

        throw new \InvalidArgumentException('Opção automática inválida.');
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

        if (in_array('emitida', $statuses, true)) {
            return 'emitida';
        }

        if (in_array('anexada', $statuses, true)) {
            return 'anexada';
        }

        return 'pendente';
    }
}
