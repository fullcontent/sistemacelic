<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Models\Servico;
use App\Models\AnaliseProtocoloLaudo;
use App\Models\Protocolo;
use App\Models\Laudo;
use App\Models\Documento;
use App\Models\Empresa;
use App\Models\Unidade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ServicosCicloAnaliseTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $empresa;
    protected $unidade;
    protected $servico;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user manually
        $this->admin = new User();
        $this->admin->name = 'Admin Test';
        $this->admin->email = 'admin_test_' . uniqid() . '@sistemacelic.com';
        $this->admin->password = bcrypt('password');
        $this->admin->privileges = 'admin';
        $this->admin->permitir_acesso_servicos = true;
        $this->admin->active = 1;
        $this->admin->save();

        // Create Empresa manually
        $this->empresa = new Empresa();
        $this->empresa->nomeFantasia = 'Empresa Test';
        $this->empresa->razaoSocial = 'Empresa Test LTDA';
        $this->empresa->cnpj = '00.000.000/0001-00';
        $this->empresa->inscricaoEst = '123';
        $this->empresa->inscricaoMun = '456';
        $this->empresa->inscricaoImo = '789';
        $this->empresa->status = 'Ativa';
        $this->empresa->tipoImovel = 'Comercial';
        $this->empresa->codigo = 'EMP01';
        $this->empresa->cidade = 'São Paulo';
        $this->empresa->uf = 'SP';
        $this->empresa->endereco = 'Av Paulista';
        $this->empresa->numero = '1000';
        $this->empresa->complemento = 'Sala 1';
        $this->empresa->cep = '01311-000';
        $this->empresa->bairro = 'Bela Vista';
        $this->empresa->email = 'empresa@test.com';
        $this->empresa->save();

        // Create Unidade manually
        $this->unidade = new Unidade();
        $this->unidade->empresa_id = $this->empresa->id;
        $this->unidade->nomeFantasia = 'Unidade Test';
        $this->unidade->razaoSocial = 'Unidade Test LTDA';
        $this->unidade->cnpj = '00.000.000/0001-01';
        $this->unidade->status = 'Ativa';
        $this->unidade->codigo = 'UNI01';
        $this->unidade->cidade = 'São Paulo';
        $this->unidade->uf = 'SP';
        $this->unidade->endereco = 'Av Paulista';
        $this->unidade->numero = '1001';
        $this->unidade->cep = '01311-000';
        $this->unidade->bairro = 'Bela Vista';
        $this->unidade->email = 'unidade@test.com';
        $this->unidade->save();

        // Create a user-access mapping manually
        $access = new \App\UserAccess();
        $access->user_id = $this->admin->id;
        $access->empresa_id = $this->empresa->id;
        $access->unidade_id = null;
        $access->save();

        // Create a basic service for cycle tests
        $this->servico = new Servico();
        $this->servico->tipo = 'licencaOperacao';
        $this->servico->os = 'OS-CICLO-123';
        $this->servico->nome = 'AVCB';
        $this->servico->situacao = 'andamento';
        $this->servico->responsavel_id = $this->admin->id;
        $this->servico->empresa_id = $this->empresa->id;
        $this->servico->unidade_id = $this->unidade->id;
        $this->servico->save();
    }

    public function test_can_start_analysis_cycle()
    {
        $this->actingAs($this->admin);
        Storage::fake('local');

        $protocolFile = UploadedFile::fake()->create('protocolo.pdf', 100);
        $doc1 = UploadedFile::fake()->create('juntada1.pdf', 100);
        $doc2 = UploadedFile::fake()->create('juntada2.pdf', 100);

        $response = $this->post(route('servico.ciclo.iniciar'), [
            'servico_id' => $this->servico->id,
            'descricao' => 'Primeira Análise',
            'protocolo_numero' => 'PROT-111222',
            'protocolo_emissao' => '05/08/2026',
            'protocolo_anexo' => $protocolFile,
            'documentos_juntados' => [$doc1, $doc2],
        ]);

        $response->assertStatus(302);

        // Verify cycle was created
        $ciclo = AnaliseProtocoloLaudo::where('servico_id', $this->servico->id)->first();
        $this->assertNotNull($ciclo);
        $this->assertEquals('em_andamento', $ciclo->status);
        $this->assertEquals('Primeira Análise', $ciclo->descricao);

        // Verify protocol was created
        $protocolo = Protocolo::where('analise_protocolo_laudo_id', $ciclo->id)->first();
        $this->assertNotNull($protocolo);
        $this->assertEquals('PROT-111222', $protocolo->numero);
        $this->assertEquals('2026-08-05', $protocolo->data_protocolo->toDateString());
        $this->assertNotNull($protocolo->anexo);

        // Verify documents were uploaded and recorded
        $documentos = Documento::where('analise_protocolo_laudo_id', $ciclo->id)->get();
        $this->assertCount(2, $documentos);
        $this->assertEquals('juntada1.pdf', $documentos[0]->nome);
        $this->assertEquals('juntada2.pdf', $documentos[1]->nome);
    }

    public function test_can_register_exigibilidade_and_start_next_cycle()
    {
        $this->actingAs($this->admin);
        Storage::fake('local');

        // Setup active cycle first
        $ciclo = AnaliseProtocoloLaudo::create([
            'servico_id' => $this->servico->id,
            'status' => 'em_andamento',
            'descricao' => 'Primeira Análise',
        ]);

        $laudoFile = UploadedFile::fake()->create('laudo_exigencias.pdf', 100);
        $novoProtFile = UploadedFile::fake()->create('novo_protocolo.pdf', 100);

        $response = $this->post(route('servico.ciclo.registrarExigencia'), [
            'servico_id' => $this->servico->id,
            'laudo_numero' => 'LAUDO-999888',
            'laudo_emissao' => '06/08/2026',
            'laudo_anexo' => $laudoFile,
            'novo_protocolo_numero' => 'PROT-333444',
            'novo_protocolo_emissao' => '07/08/2026',
            'novo_protocolo_anexo' => $novoProtFile,
        ]);

        $response->assertStatus(302);

        // Refresh first cycle and check it has status com_exigencia
        $ciclo->refresh();
        $this->assertEquals('com_exigencia', $ciclo->status);

        // Check laudo was attached to first cycle
        $laudo = Laudo::where('analise_protocolo_laudo_id', $ciclo->id)->first();
        $this->assertNotNull($laudo);
        $this->assertEquals('LAUDO-999888', $laudo->numero);
        $this->assertEquals('2026-08-06', $laudo->data_emissao->toDateString());

        // Check new cycle was started automatically
        $novoCiclo = AnaliseProtocoloLaudo::where('servico_id', $this->servico->id)
            ->where('id', '!=', $ciclo->id)
            ->first();
        $this->assertNotNull($novoCiclo);
        $this->assertEquals('em_andamento', $novoCiclo->status);
        $this->assertEquals('Análise de Exigência 1', $novoCiclo->descricao);

        // Check protocol attached to new cycle
        $novoProtocolo = Protocolo::where('analise_protocolo_laudo_id', $novoCiclo->id)->first();
        $this->assertNotNull($novoProtocolo);
        $this->assertEquals('PROT-333444', $novoProtocolo->numero);
        $this->assertEquals('2026-08-07', $novoProtocolo->data_protocolo->toDateString());
    }

    public function test_can_approve_cycle_and_emit_license()
    {
        $this->actingAs($this->admin);
        Storage::fake('local');

        // Setup active cycle first
        $ciclo = AnaliseProtocoloLaudo::create([
            'servico_id' => $this->servico->id,
            'status' => 'em_andamento',
            'descricao' => 'Análise de Exigência 1',
        ]);

        $licencaFile = UploadedFile::fake()->create('licenca.pdf', 100);

        $response = $this->post(route('servico.ciclo.finalizarCicloSucesso'), [
            'servico_id' => $this->servico->id,
            'tipoLicenca' => 'renovavel',
            'licenca_emissao' => '08/08/2026',
            'licenca_validade' => '08/08/2028',
            'licenca_anexo' => $licencaFile,
        ]);

        $response->assertStatus(302);

        // Verify cycle is approved
        $ciclo->refresh();
        $this->assertEquals('aprovada', $ciclo->status);

        // Verify service is finalized and updated
        $this->servico->refresh();
        $this->assertEquals('finalizado', $this->servico->situacao);
        $this->assertEquals('renovavel', $this->servico->tipoLicenca);
        $this->assertEquals('2026-08-08', $this->servico->licenca_emissao);
        $this->assertEquals('2028-08-08', $this->servico->licenca_validade);
        $this->assertNotNull($this->servico->licenca_anexo);
    }
}
