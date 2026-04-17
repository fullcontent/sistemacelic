<?php

namespace App\Services\Nfse;

class NfsePayloadFactory2
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
		$descricao = self::sanitizeDiscriminacao(isset($item['descricao_servico']) ? $item['descricao_servico'] : '');
		$valor = isset($item['valor_servico']) ? (float) $item['valor_servico'] : 0;
		$iss = self::buildIssPayload($config, $item);

		$payload = [
			'dataCompetencia' => date('Y-m-d'),
			'emitirComo' => isset($config['emit_as']) ? $config['emit_as'] : null,
			'regimeApuracao' => isset($config['simples_regime']) ? $config['simples_regime'] : null,
			'tomadorTipo' => isset($config['tomador_tipo']) ? $config['tomador_tipo'] : null,
			'intermediarioTipo' => isset($config['intermediario_tipo']) ? $config['intermediario_tipo'] : null,
			'localPrestacao' => isset($config['local_prestacao']) ? $config['local_prestacao'] : null,
			'municipio' => isset($config['municipio_nome']) ? $config['municipio_nome'] : null,
			'codigoTributacaoNacional' => isset($config['codigo_tributacao_nacional']) ? $config['codigo_tributacao_nacional'] : null,
			'suspensaoExigibilidadeIssqn' => (bool) ($config['suspensao_exigibilidade_issqn'] ?? false),
			'itemNbs' => isset($config['item_nbs']) ? $config['item_nbs'] : null,
			'issqnExigibilidadeSuspensa' => (bool) ($config['issqn_exigibilidade_suspensa'] ?? false),
			'issqnRetido' => (bool) ($config['issqn_retido'] ?? false),
			'beneficioMunicipal' => (bool) ($config['beneficio_municipal'] ?? false),
			'pisCofinsSituacao' => isset($config['pis_cofins_situacao']) ? $config['pis_cofins_situacao'] : null,
			'aliquotaSimples' => isset($config['aliquota_simples']) ? (float) $config['aliquota_simples'] : null,
			'valorAproximadoTributos' => isset($config['valor_aproximado_tributos']) ? (float) $config['valor_aproximado_tributos'] : null,
			'prestador' => [
				'cpfCnpj' => self::sanitizeDocument($config['cnpj'] ?? ($config['dados_castro']['cnpj'] ?? null)),
				'inscricaoMunicipal' => isset($config['inscricao_municipal']) ? self::sanitizeDocument($config['inscricao_municipal']) : null,
				'certificado' => $config['certificado'] ?? null,
			],
			'tomador' => [],
			'servico' => [
				[
					'codigo' => self::resolveServicoCodigo($config, $item),
					'discriminacao' => $descricao,
					'valor' => [
						'servico' => round($valor, 2),
					],
					'iss' => $iss,
				]
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

	private static function buildIssPayload(array $config, array $item = [])
	{
		$aliquota = self::resolveIssAliquota($config, $item);

		return [
			'retido' => (bool) ($config['issqn_retido'] ?? false),
			'aliquota' => $aliquota,
		];
	}

	private static function resolveIssAliquota(array $config, array $item = [])
	{
		$candidates = [
			$item['iss_aliquota'] ?? null,
			$item['aliquota_iss'] ?? null,
			$item['aliquota'] ?? null,
			$config['iss_aliquota'] ?? null,
			$config['aliquota_iss'] ?? null,
			$config['aliquota_simples'] ?? null,
			$config['aliquota_simples_nacional'] ?? null,
			$config['aliquota'] ?? null,
		];

		foreach ($candidates as $candidate) {
			$parsed = self::parseDecimal($candidate);
			if ($parsed !== null) {
				return $parsed;
			}
		}

		// Mantem o contrato da API mesmo sem configuracao de aliquota.
		return 0.0;
	}

	private static function parseDecimal($value)
	{
		if ($value === null || $value === '') {
			return null;
		}

		if (is_string($value)) {
			$value = str_replace(',', '.', trim($value));
		}

		if (!is_numeric($value)) {
			return null;
		}

		return round((float) $value, 4);
	}

	private static function sanitizeDiscriminacao($descricao)
	{
		$descricao = (string) $descricao;

		// PlugNotas pode rejeitar quebras de linha/controles em servico.discriminacao.
		$descricao = preg_replace('/[\r\n]+/', ' | ', $descricao);
		$descricao = preg_replace('/[\x00-\x1F\x7F]+/', ' ', $descricao);
		$descricao = preg_replace('/\s{2,}/', ' ', trim($descricao));

		if (function_exists('mb_substr')) {
			return mb_substr($descricao, 0, 2000);
		}

		return substr($descricao, 0, 2000);
	}

	private static function resolveServicoCodigo(array $config, array $item)
	{
		if (!empty($item['codigo_servico'])) {
			return (string) $item['codigo_servico'];
		}

		if (!empty($config['codigo_servico'])) {
			return (string) $config['codigo_servico'];
		}

		if (!empty($config['codigo_tributacao_nacional'])) {
			return (string) $config['codigo_tributacao_nacional'];
		}

		return '1';
	}
}

