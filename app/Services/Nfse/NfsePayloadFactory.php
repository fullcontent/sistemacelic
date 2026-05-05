<?php

namespace App\Services\Nfse;

use Illuminate\Support\Str;

class NfsePayloadFactory
{
    public static function sanitizeDocument($document)
    {
        return preg_replace('/\D+/', '', (string) $document);
    }

    public static function sanitizeInscricao($inscricao)
    {
        if (strtoupper(trim((string) $inscricao)) === 'ISENTO') {
            return 'ISENTO';
        }
        return preg_replace('/[^a-zA-Z0-9]+/', '', (string) $inscricao);
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

    /**
     * Gera um ID de integração único para cada payload
     */
    public static function generateIdIntegracao()
    {
        return strtoupper(substr(Str::uuid(), 0, 20));
    }

    /**
     * Constrói o payload no formato correto para a API PlugNotas
     */
    public static function buildBasePayload(array $config, array $item, array $extraFields = [])
    {
        $descricao = isset($item['descricao_servico']) ? $item['descricao_servico'] : '';
        $valor = isset($item['valor_servico']) ? (float) $item['valor_servico'] : 0;
        
        \Log::info('NfsePayloadFactory: Building payload', [
            'has_certificado' => !empty($config['certificado']),
            'certificado_val' => $config['certificado'] ?? 'MISSING'
        ]);
        
        // Extrair código da cidade do local_prestacao (geralmente é o IBGE)
        $codigoCidade = $config['local_prestacao'] ?? '4202008';
        
        // Tipo de emitente (1 = Prestador)
        $tipoEmitente = 1;
        
        // Código de serviço padrão (100101 = Serviços profissionais)
        $codigoServico = isset($extraFields['codigo_servico']) ? $extraFields['codigo_servico'] : '100101';
        
        // Tipo de tributação do ISS (valores padrão)
        // 1 = Prestador; 2 = Tomador; 3 = Intermediário; 4 = Parcela; 5 = Isento; 6 = Substituição Tributária
        $tipoTributacao = isset($extraFields['tipo_tributacao']) ? $extraFields['tipo_tributacao'] : 6;
        
        // Exigibilidade ISS (1 = Exigível; 2 = Não incidência; 3 = Isenção; 4 = Exportação; 5 = Imunidade; 6 = Exigibilidade suspensa)
        $exigibilidade = isset($extraFields['exigibilidade']) ? $extraFields['exigibilidade'] : 1;
        
        // Alíquota ISS (padrão 2%)
        $aliquotaIss = isset($config['aliquota_simples']) ? (float) $config['aliquota_simples'] : 2;
        $issRetido = (bool) ($config['issqn_retido'] ?? false);
        
        $regimeTrib = isset($config['regime_tributario']) ? (int) $config['regime_tributario'] : 1;
        
        $prestador = [
            'cpfCnpj' => self::sanitizeDocument($config['cnpj'] ?? ($config['dados_castro']['cnpj'] ?? null)),
            'simplesNacional' => ($regimeTrib === 1 || $regimeTrib === 6),
            'regimeTributario' => $regimeTrib,
        ];

        if (!empty($config['inscricao_municipal'])) {
            $prestador['inscricaoMunicipal'] = self::sanitizeInscricao($config['inscricao_municipal']);
        }

        $regimeApuracaoTributaria = null;
        if ($regimeTrib === 1) {
            $prestador['regimeTributarioEspecial'] = 6; // 6 = ME/EPP optante pelo SN
            $regimeApuracaoTributaria = 1; // 1 = Regime de apuração dos tributos federais e municipal pelo SN
        } elseif ($regimeTrib === 6) {
            $prestador['regimeTributarioEspecial'] = 5; // 5 = MEI
            $regimeApuracaoTributaria = 1; 
        } else {
            $prestador['regimeTributarioEspecial'] = 0; // 0 = Nenhum
        }

        $issBlock = [
            'tipoTributacao' => (int) $tipoTributacao,
            'exigibilidade' => (int) $exigibilidade,
            'retido' => (bool) $issRetido,
        ];

        // Regra Padrão Nacional (E0625): Não enviar alíquota se for ME/EPP do Simples Nacional 
        // com apuração no próprio SN e sem retenção de ISS.
        if (!($regimeTrib === 1 && $issRetido === false && $regimeApuracaoTributaria === 1)) {
            $issBlock['aliquota'] = (float) $aliquotaIss;
        }

        $payload = [
            'idIntegracao' => self::generateIdIntegracao(),
            'emitente' => [
                'tipo' => $tipoEmitente,
                'codigoCidade' => (string) $codigoCidade,
            ],
            'prestador' => $prestador,
            'tomador' => [], // To be populated by resolveTomadorData
            'servico' => [
                [
                    'codigo' => (string) $codigoServico,
                    'discriminacao' => $descricao,
                    'iss' => $issBlock,
                    'valor' => [
                        'servico' => round($valor, 2),
                    ],
                ]
            ],
        ];

        if ($regimeApuracaoTributaria !== null) {
            $payload['regimeApuracaoTributaria'] = $regimeApuracaoTributaria;
        }

        return $payload;
    }

    /**
     * Constrói a estrutura do tomador conforme esperado pela API
     */
    public static function buildTomadorData(array $tomadorData)
    {
        return [
            'cpfCnpj' => self::sanitizeDocument($tomadorData['cpfCnpj'] ?? ''),
            'razaoSocial' => $tomadorData['razaoSocial'] ?? '',
            'inscricaoMunicipal' => !empty($tomadorData['inscricaoMunicipal']) ? self::sanitizeInscricao($tomadorData['inscricaoMunicipal']) : null,
            'email' => $tomadorData['email'] ?? null,
            'endereco' => self::buildEnderecoData($tomadorData['endereco'] ?? []),
        ];
    }

    /**
     * Constrói a estrutura do endereço conforme esperado pela API
     */
    public static function buildEnderecoData(array $endereco)
    {
        // O codigoCidade é o IBGE code do município
        $codigoCidade = (string) ($endereco['codigoCidade'] ?? '');
        
        $data = [
            'tipoLogradouro' => $endereco['tipoLogradouro'] ?? 'Rua',
            'logradouro' => $endereco['logradouro'] ?? '',
            'numero' => $endereco['numero'] ?? '',
            'complemento' => !empty($endereco['complemento']) ? $endereco['complemento'] : null,
            'tipoBairro' => $endereco['tipoBairro'] ?? 'Centro',
            'bairro' => $endereco['bairro'] ?? '',
            'descricaoCidade' => $endereco['descricaoCidade'] ?? $endereco['cidade'] ?? '',
            'cep' => self::sanitizeDocument($endereco['cep'] ?? ''),
            'estado' => $endereco['uf'] ?? '',
        ];

        if (!empty($codigoCidade)) {
            $data['codigoCidade'] = $codigoCidade;
        } elseif (!empty($data['descricaoCidade']) && !empty($data['estado'])) {
            throw new \RuntimeException("Código IBGE não encontrado para a cidade '{$data['descricaoCidade']}' ({$data['estado']}). Verifique se há erros de digitação no nome da cidade.");
        }

        return $data;
    }
}
