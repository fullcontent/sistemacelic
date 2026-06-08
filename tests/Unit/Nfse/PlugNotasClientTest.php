<?php

namespace Tests\Unit\Nfse;

use App\Services\Nfse\PlugNotasClient;
use PHPUnit\Framework\TestCase;

class PlugNotasClientTest extends TestCase
{
    public function test_emitir_nfse_in_mock_mode_without_subscription()
    {
        $client = new PlugNotasClient(null, [
            'mock_mode' => true,
            'api_key' => null,
        ]);

        $response = $client->emitirNfse([
            'servico' => [
                'descricao' => 'Teste sem assinatura',
                'valor' => 150,
            ],
        ]);

        $this->assertTrue($response['mock']);
        $this->assertArrayHasKey('numero', $response);
        $this->assertArrayHasKey('id', $response);
    }

    public function test_emitir_nfse_without_api_key_and_mock_disabled_should_fail()
    {
        $this->expectException(\RuntimeException::class);

        $client = new PlugNotasClient(null, [
            'mock_mode' => false,
            'api_key' => null,
        ]);

        $client->emitirNfse([
            'servico' => [
                'descricao' => 'Teste',
                'valor' => 100,
            ],
        ]);
    }
}
