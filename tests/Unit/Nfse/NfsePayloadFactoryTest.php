<?php

namespace Tests\Unit\Nfse;

use App\Services\Nfse\NfsePayloadFactory2;
use PHPUnit\Framework\TestCase;

class NfsePayloadFactoryTest extends TestCase
{
    public function test_build_grouped_description()
    {
        $description = NfsePayloadFactory2::buildGroupedDescription([
            [
                'codigo_unidade' => '101',
                'nome_unidade' => 'Espaçolaser Blumenau',
                'nome_servico' => 'Projeto PPCI',
                'valor' => 450,
            ],
            [
                'codigo_unidade' => '1235',
                'nome_unidade' => 'Espaçolaser Joinville',
                'nome_servico' => 'Licença Vigilância',
                'valor' => 600,
            ],
        ]);

        $this->assertStringContainsString('101 Espaçolaser Blumenau - Projeto PPCI - R$ 450,00', $description);
        $this->assertStringContainsString('1235 Espaçolaser Joinville - Licença Vigilância - R$ 600,00', $description);
    }

    public function test_sanitize_document()
    {
        $this->assertEquals('12345678000199', NfsePayloadFactory2::sanitizeDocument('12.345.678/0001-99'));
    }

    public function test_build_payload_sets_default_automatic_fields()
    {
        $payload = NfsePayloadFactory2::buildBasePayload([
            'emit_as' => 'Prestador',
            'simples_regime' => 'Regime de apuração dos tributos federais e municipal pelo Simples Nacional',
            'tomador_tipo' => 'Brasil',
            'intermediario_tipo' => 'Intermediario nao informado',
            'local_prestacao' => 'Brasil',
            'municipio_nome' => 'Balneário Camboriú/SC',
            'codigo_tributacao_nacional' => '17.02.02',
            'suspensao_exigibilidade_issqn' => false,
            'item_nbs' => '118064000',
            'issqn_exigibilidade_suspensa' => false,
            'issqn_retido' => false,
            'beneficio_municipal' => false,
            'pis_cofins_situacao' => '00',
            'aliquota_simples' => 9.90,
            'valor_aproximado_tributos' => 9.90,
            'codigo_tributacao_nacional' => '17.02.02',
        ], [
            'cnpj_tomador' => '12.345.678/0001-99',
            'descricao_servico' => 'Serviço teste',
            'valor_servico' => 100.00,
        ]);

        $this->assertIsArray($payload['tomador']);
        $this->assertEquals('17.02.02', $payload['servico'][0]['codigo']);
        $this->assertEquals('Serviço teste', $payload['servico'][0]['discriminacao']);
        $this->assertEquals(100.00, $payload['servico'][0]['valor']['servico']);
        $this->assertArrayHasKey('iss', $payload['servico'][0]);
        $this->assertFalse($payload['servico'][0]['iss']['retido']);
        $this->assertEquals(9.9, $payload['servico'][0]['iss']['aliquota']);
    }

    public function test_build_payload_sanitizes_discriminacao()
    {
        $payload = NfsePayloadFactory2::buildBasePayload([
            'emit_as' => 'Prestador',
            'simples_regime' => 'Regime de apuração dos tributos federais e municipal pelo Simples Nacional',
            'tomador_tipo' => 'Brasil',
            'intermediario_tipo' => 'Intermediario nao informado',
            'local_prestacao' => 'Brasil',
            'municipio_nome' => 'Balneário Camboriú/SC',
            'codigo_tributacao_nacional' => '17.02.02',
            'item_nbs' => '118064000',
        ], [
            'descricao_servico' => "Linha 1\n\nLinha 2\r\n\tLinha 3",
            'valor_servico' => 50.00,
        ]);

        $this->assertEquals('Linha 1 | Linha 2 | Linha 3', $payload['servico'][0]['discriminacao']);
    }
}
