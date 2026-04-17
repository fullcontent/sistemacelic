<?php

namespace Tests\Feature\Nfse;

use App\Models\DadosCastro;
use App\Services\NfseService;
use Tests\TestCase;

class PlugNotasCompanyRegistrationTest extends TestCase
{
    public function test_cadastrar_empresa_na_plugnotas_em_producao_sem_erro_de_campos_obrigatorios()
    {
        // Forca o ambiente da PlugNotas para producao durante o teste.
        putenv('PLUGNOTAS_ENV=production');
        $_ENV['PLUGNOTAS_ENV'] = 'production';

        $token = env('PLUGNOTAS_TOKEN');
        if (empty($token)) {
            $this->markTestSkipped('PLUGNOTAS_TOKEN nao configurado para executar teste real.');
        }

        $emitente = DadosCastro::with('nfseConfiguration')
            ->where('ativo', true)
            ->get()
            ->first(function ($dc) {
                return !empty($dc->cnpj)
                    && !empty($dc->razaoSocial)
                    && $dc->nfseConfiguration
                    && !empty($dc->nfseConfiguration->inscricao_municipal)
                    && !empty($dc->nfseConfiguration->certificado)
                    && !empty($dc->nfseConfiguration->logradouro)
                    && !empty($dc->nfseConfiguration->numero)
                    && !empty($dc->nfseConfiguration->bairro)
                    && !empty($dc->nfseConfiguration->codigo_cidade)
                    && !empty($dc->nfseConfiguration->uf);
            });

        if (!$emitente) {
            $this->markTestSkipped('Nao ha emitente com os campos minimos preenchidos para teste de cadastro PlugNotas.');
        }

        $config = $emitente->nfseConfiguration;
        $emailEmitente = !empty($config->email_emitente)
            ? $config->email_emitente
            : env('MAIL_FROM_ADDRESS');

        $payload = [
            'cpfCnpj' => preg_replace('/\D/', '', (string) $emitente->cnpj),
            'razaoSocial' => $emitente->razaoSocial,
            'nomeFantasia' => $emitente->razaoSocial,
            'email' => $emailEmitente,
            'inscricaoMunicipal' => $config->inscricao_municipal,
            'regimeTributario' => (int) ($config->regime_tributario ?: 1),
            'simplesNacional' => in_array((int) $config->regime_tributario, [1, 2], true),
            'certificado' => $config->certificado,
            'endereco' => [
                'bairro' => $config->bairro,
                'cep' => preg_replace('/\D/', '', (string) $config->cep),
                'codigoCidade' => (string) $config->codigo_cidade,
                'estado' => strtoupper((string) $config->uf),
                'tipoLogradouro' => 'Rua',
                'logradouro' => $config->logradouro,
                'numero' => (string) $config->numero,
                'descricaoCidade' => $config->municipio_nome,
                'codigoPais' => '1058',
                'descricaoPais' => 'Brasil',
            ],
            'nfse' => [
                'ativo' => (bool) $config->ativo,
                'tipoContrato' => 0,
                'config' => [
                    'producao' => true,
                    'prefeitura' => [
                        'login' => $config->login_prefeitura,
                        'senha' => $config->senha_prefeitura,
                    ],
                ],
            ],
        ];

        $service = app(NfseService::class);
        $result = $service->cadastrarEmpresa($payload);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);

        if (empty($result['success'])) {
            $message = (string) ($result['message'] ?? '');
            $this->assertStringNotContainsString('ausencia de parametros obrigatorios', mb_strtolower($message, 'UTF-8'));
            $this->assertStringNotContainsString('ausência de parâmetros obrigatórios', mb_strtolower($message, 'UTF-8'));
        }
    }
}
