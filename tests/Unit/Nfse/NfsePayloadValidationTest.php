<?php

namespace Tests\Unit\Nfse;

use App\Helpers\IbgeHelper;
use App\Services\Nfse\NfsePayloadFactory2;
use PHPUnit\Framework\TestCase;

class NfsePayloadValidationTest extends TestCase
{
    public function test_ibge_helper_normalization()
    {
        // Test accented characters
        $this->assertEquals('SAO PAULO', IbgeHelper::getIbgeCode('São Paulo', 'SP') ? 'SAO PAULO' : null);
        $this->assertEquals('4202008', IbgeHelper::getIbgeCode('Balneário Camboriú', 'SC'));
        $this->assertEquals('2604106', IbgeHelper::getIbgeCode('Caruaru', 'PE'));
        
        // Test lowercase and spaces
        $this->assertEquals('4208203', IbgeHelper::getIbgeCode('  itajai  ', 'sc'));
    }

    public function test_payload_factory_base_structure()
    {
        $config = [
            'emit_as' => 'Prestador',
            'simples_regime' => 'Regime de apuração dos tributos federais e municipal pelo Simples Nacional',
            'tomador_tipo' => 'Brasil',
            'intermediario_tipo' => 'Intermediario nao informado',
            'local_prestacao' => 'Brasil',
            'municipio_nome' => 'Balneário Camboriú',
            'codigo_tributacao_nacional' => '17.02.02',
            'suspensao_exigibilidade_issqn' => false,
            'item_nbs' => '118064000',
            'issqn_exigibilidade_suspensa' => false,
            'issqn_retido' => false,
            'beneficio_municipal' => false,
            'pis_cofins_situacao' => '00',
            'aliquota_simples' => 9.90,
            'cnpj' => '11.377.588/0001-13',
            'inscricao_municipal' => '123456',
        ];

        $item = [
            'descricao_servico' => 'Teste Unitário',
            'valor_servico' => 1500.50,
        ];

        $payload = NfsePayloadFactory2::buildBasePayload($config, $item);

        // Required top-level fields
        $this->assertArrayHasKey('dataCompetencia', $payload);
        $this->assertEquals('17.02.02', $payload['codigoTributacaoNacional']);

        // Prestador structure
        $this->assertArrayHasKey('prestador', $payload);
        $this->assertEquals('11377588000113', $payload['prestador']['cpfCnpj']);
        $this->assertEquals('123456', $payload['prestador']['inscricaoMunicipal']);

        // Servico structure (Array of objects)
        $this->assertArrayHasKey('servico', $payload);
        $this->assertIsArray($payload['servico']);
        $this->assertEquals('Teste Unitário', $payload['servico'][0]['discriminacao']);
        $this->assertEquals(1500.50, $payload['servico'][0]['valor']['servico']);
        $this->assertArrayHasKey('iss', $payload['servico'][0]);
        $this->assertEquals(9.9, $payload['servico'][0]['iss']['aliquota']);

        // Tomador should be an empty array base, ready for resolveTomadorData
        $this->assertIsArray($payload['tomador']);
    }

    public function test_full_payload_validation_simulation()
    {
        $payload = [
            'prestador' => ['cpfCnpj' => '11377588000113'],
            'tomador' => [
                'cpfCnpj' => '08845676007109',
                'razaoSocial' => 'Teste Tomador',
                'endereco' => [
                    'logradouro' => 'Rua Teste',
                    'numero' => '123',
                    'bairro' => 'Centro',
                    'codigoCidade' => IbgeHelper::getIbgeCode('São Paulo', 'SP'),
                    'cep' => '01242010',
                    'uf' => 'SP'
                ]
            ],
            'servico' => [
                'descricao' => 'Serviço de Teste',
                'valor' => 100.00
            ]
        ];

        $this->assertEquals('3550308', $payload['tomador']['endereco']['codigoCidade']);
        $this->assertEquals(14, strlen($payload['tomador']['cpfCnpj']));
        $this->assertNotEmpty($payload['servico']['descricao']);
    }
}
