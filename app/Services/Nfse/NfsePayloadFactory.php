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

    public static function sanitizeString($text, $limit = 255)
    {
        $text = (string) $text;
        // Remove non-printable and potentially problematic characters
        $text = preg_replace('/[^\x20-\x7E\xA1-\xFF]/u', '', $text);
        return mb_substr(trim($text), 0, $limit);
    }

    public static function sanitizeDescription($text)
    {
        $text = (string) $text;
        
        // Normalize line breaks to \n (required by PlugNotas)
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Remove characters that often cause issues in XML/JSON for NFS-e
        // keeping common accented characters for PT-BR
        $text = preg_replace('/[^\x20-\x7E\xA1-\xFF\n]/u', '', $text);
        
        // Limit to 2000 characters (PlugNotas limit)
        return mb_substr($text, 0, 2000);
    }

    public static function buildGroupedDescription(array $items)
    {
        $lines = [];
        $totalItems = count($items);
        $currentLength = 0;
        $maxChars = 1950; // Leave some safety margin for the footer

        foreach ($items as $index => $item) {
            $codigo = isset($item['codigo_unidade']) ? $item['codigo_unidade'] : '-';
            $unidade = isset($item['nome_unidade']) ? $item['nome_unidade'] : 'Unidade';
            $servico = isset($item['nome_servico']) ? $item['nome_servico'] : 'Serviço';
            $valor = isset($item['valor']) ? $item['valor'] : 0;

            $line = sprintf('%s %s - %s - R$ %s', $codigo, $unidade, $servico, number_format($valor, 2, ',', '.'));
            
            // Check if adding this line exceeds the limit
            if ($currentLength + mb_strlen($line) + 1 > $maxChars) {
                $lines[] = "... (e mais " . ($totalItems - $index) . " itens)";
                break;
            }
            
            $lines[] = $line;
            $currentLength += mb_strlen($line) + 1;
        }

        return self::sanitizeDescription(implode("|", $lines));
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
        $descricao = self::sanitizeDescription(isset($item['descricao_servico']) ? $item['descricao_servico'] : '');
        $valor = isset($item['valor_servico']) ? (float) $item['valor_servico'] : 0;
        
        \Log::info('NfsePayloadFactory: Building payload', [
            'has_certificado' => !empty($config['certificado']),
            'certificado_val' => $config['certificado'] ?? 'MISSING'
        ]);
        
        // Extrair código da cidade
        $codigoCidade = $config['codigo_cidade'] ?? '4202008';
        
        // Tipo de emitente (1 = Prestador)
        $tipoEmitente = 1;
        
        // Código de serviço (LC116) - Priorizar o que está na configuração
        $codigoServico = !empty($config['codigo_tributacao_nacional']) ? $config['codigo_tributacao_nacional'] : (isset($extraFields['codigo_servico']) ? $extraFields['codigo_servico'] : '100101');
        
        // Tipo de tributação do ISS (Padrão Nacional DPS)
        // 1 = Tributável no Município; 2 = Tributável fora do Município; 3 = Isenção; 
        // 4 = Imunidade; 5 = Suspensa por Decisão Judicial; 6 = Suspensa por Proc. Administrativo;
        // 7 = Não Incidência; 8 = Exportação
        $issRetido = (bool) ($config['issqn_retido'] ?? false);
        $defaultTipoTrib = 1; // Padrão: Tributável no Município
        
        if (!empty($config['tipo_tributacao_iss'])) {
            $tipoTributacao = $config['tipo_tributacao_iss'];
        } else {
            $tipoTributacao = isset($extraFields['tipo_tributacao']) ? $extraFields['tipo_tributacao'] : $defaultTipoTrib;
        }
        
        // Exigibilidade ISS (Padrão Nacional DPS)
        // 1 = Exigível; 2 = Imunidade; 3 = Isenção; 4 = Não Incidência; 5 = Suspensa por Decisão Judicial; 6 = Suspensa por Proc. Administrativo
        $isSuspensa = (bool) ($config['issqn_exigibilidade_suspensa'] ?? false);
        $defaultExigibilidade = $isSuspensa ? 6 : 1;
        
        if (!empty($config['exigibilidade_iss'])) {
            $exigibilidade = $config['exigibilidade_iss'];
        } else {
            $exigibilidade = isset($extraFields['exigibilidade']) ? $extraFields['exigibilidade'] : $defaultExigibilidade;
        }
        
        // Alíquota ISS (padrão 2%)
        $aliquotaIss = isset($config['aliquota_simples']) ? (float) $config['aliquota_simples'] : 2;
        
        $regimeTrib = isset($config['regime_tributario']) ? (int) $config['regime_tributario'] : 1;
        
        $prestador = [
            'cpfCnpj' => self::sanitizeDocument($config['cnpj'] ?? ($config['dados_castro']['cnpj'] ?? null)),
            'simplesNacional' => ($regimeTrib === 1 || $regimeTrib === 6),
        ];

        if (!empty($config['inscricao_municipal'])) {
            $prestador['inscricaoMunicipal'] = self::sanitizeInscricao($config['inscricao_municipal']);
        }

        $regimeApuracaoTributaria = null;
        if ($regimeTrib === 1) {
            $regimeApuracaoTributaria = 1; // 1 = Regime de apuração dos tributos federais e municipal pelo SN
        } elseif ($regimeTrib === 6) {
            $regimeApuracaoTributaria = 1; 
        }

        $emitenteBlock = [
            'tipo' => $tipoEmitente,
            'codigoCidade' => (string) $codigoCidade,
        ];

        if (isset($prestador['inscricaoMunicipal'])) {
            $emitenteBlock['inscricaoMunicipal'] = $prestador['inscricaoMunicipal'];
        }

        $payload = [
            'idIntegracao' => self::generateIdIntegracao(),
            'emitente' => $emitenteBlock,
            'prestador' => $prestador,
            'tomador' => [], // To be populated by resolveTomadorData
            'servico' => [
                [
                    'codigo' => (string) $codigoServico,
                    'discriminacao' => $descricao,
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
            'razaoSocial' => self::sanitizeString($tomadorData['razaoSocial'] ?? '', 150),
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
            'tipoLogradouro' => self::sanitizeString($endereco['tipoLogradouro'] ?? 'Rua', 10),
            'logradouro' => self::sanitizeString($endereco['logradouro'] ?? '', 100),
            'numero' => self::sanitizeString($endereco['numero'] ?? 'S/N', 10),
            'complemento' => !empty($endereco['complemento']) ? self::sanitizeString($endereco['complemento'], 100) : null,
            'tipoBairro' => self::sanitizeString($endereco['tipoBairro'] ?? 'Centro', 20),
            'bairro' => self::sanitizeString($endereco['bairro'] ?? '', 60),
            'descricaoCidade' => self::sanitizeString($endereco['descricaoCidade'] ?? $endereco['cidade'] ?? '', 60),
            'estado' => self::sanitizeString($endereco['uf'] ?? '', 2),
        ];

        $cep = self::sanitizeDocument($endereco['cep'] ?? '');
        if (!empty($cep)) {
            $data['cep'] = substr(str_pad($cep, 8, '0', STR_PAD_LEFT), 0, 8);
        }

        if (!empty($codigoCidade)) {
            $data['codigoCidade'] = $codigoCidade;
        } elseif (!empty($data['descricaoCidade']) && !empty($data['estado'])) {
            throw new \RuntimeException("Código IBGE não encontrado para a cidade '{$data['descricaoCidade']}' ({$data['estado']}). Verifique se há erros de digitação no nome da cidade.");
        }

        return $data;
    }
}
