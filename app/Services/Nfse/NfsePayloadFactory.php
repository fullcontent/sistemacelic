<?php

namespace App\Services\Nfse;

class NfsePayloadFactory
{
    public static function sanitizeDocument($document)
    {
        return preg_replace('/\D+/', '', (string) $document);
    }

    public static function buildGroupedDescription(array $items)
    {
        $lines = [];

        foreach ($items as $item) {
            $codigo = isset($item['codigo_unidade']) ? $item['codigo_unidade'] : '-';
            $unidade = isset($item['nome_unidade']) ? $item['nome_unidade'] : 'Unidade';
            $servico = isset($item['nome_servico']) ? $item['nome_servico'] : 'Serviço';
            $valor = isset($item['valor']) ? $item['valor'] : 0;

            $lines[] = sprintf('%s %s - %s - R$ %s', $codigo, $unidade, $servico, number_format($valor, 2, ',', '.'));
        }

        return implode("\n", $lines);
    }

    public static function buildBasePayload(array $config, array $item, array $extraFields = [])
    {
        $descricao = isset($item['descricao_servico']) ? $item['descricao_servico'] : '';
        $valor = isset($item['valor_servico']) ? (float) $item['valor_servico'] : 0;
        $cnpj = isset($item['cnpj_tomador']) ? self::sanitizeDocument($item['cnpj_tomador']) : '';

        $payload = [
            'dataCompetencia' => date('Y-m-d'),
            'emitirComo' => $config['emit_as'],
            'regimeApuracao' => $config['simples_regime'],
            'tomadorTipo' => $config['tomador_tipo'],
            'intermediarioTipo' => $config['intermediario_tipo'],
            'localPrestacao' => $config['local_prestacao'],
            'municipio' => $config['municipio_nome'],
            'codigoTributacaoNacional' => $config['codigo_tributacao_nacional'],
            'suspensaoExigibilidadeIssqn' => (bool) $config['suspensao_exigibilidade_issqn'],
            'itemNbs' => $config['item_nbs'],
            'issqnExigibilidadeSuspensa' => (bool) $config['issqn_exigibilidade_suspensa'],
            'issqnRetido' => (bool) $config['issqn_retido'],
            'beneficioMunicipal' => (bool) $config['beneficio_municipal'],
            'pisCofinsSituacao' => $config['pis_cofins_situacao'],
            'aliquotaSimples' => (float) $config['aliquota_simples'],
            'valorAproximadoTributos' => isset($config['valor_aproximado_tributos']) ? (float) $config['valor_aproximado_tributos'] : null,
            'tomador' => [
                'cnpj' => $cnpj,
                'indicadorMunicipal' => '',
                'telefone' => '',
                'email' => '',
            ],
            'servico' => [
                'descricao' => $descricao,
                'valor' => round($valor, 2),
            ],
        ];

        $optionalFields = [
            'numeroDocumentoResponsabilidadeTecnica',
            'documentoReferencia',
            'informacoesComplementares',
            'numeroPedidoOrdemOuProjeto',
        ];

        foreach ($optionalFields as $field) {
            if (array_key_exists($field, $extraFields) && $extraFields[$field] !== null && $extraFields[$field] !== '') {
                $payload[$field] = $extraFields[$field];
            }
        }

        return $payload;
    }
}
