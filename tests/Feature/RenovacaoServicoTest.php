<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Models\Servico;
use App\Models\Historico;
use App\Models\Empresa;
use App\Models\Unidade;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use App\Mail\RenovacaoServicoMail;
use Carbon\Carbon;

class RenovacaoServicoTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $empresa;
    protected $unidade;

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
    }

    public function test_can_save_service_with_renewal_notification_enabled()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('servicos.store'), [
            'tipo' => 'licencaOperacao',
            'os' => 'OS-TEST-123',
            'nome' => 'AVCB',
            'situacao' => 'andamento',
            'responsavel_id' => $this->admin->id,
            'valorTotal' => 1000,
            'solicitante' => 'Solicitante Test',
            'ativar_notificacao_renovacao' => 1,
            'dias_para_notificacao_renovacao' => 45,
            't' => 'unidade',
            'unidade_id' => $this->unidade->id,
        ]);

        $response->assertStatus(302);

        $servico = Servico::where('os', 'OS-TEST-123')->first();
        $this->assertNotNull($servico);
        $this->assertTrue($servico->ativar_notificacao_renovacao);
        $this->assertEquals(45, $servico->dias_para_notificacao_renovacao);
        $this->assertNull($servico->notificacao_renovacao_enviada_at);
    }

    public function test_can_save_service_with_default_180_days_when_days_not_provided()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('servicos.store'), [
            'tipo' => 'licencaOperacao',
            'os' => 'OS-TEST-456',
            'nome' => 'AVCB',
            'situacao' => 'andamento',
            'responsavel_id' => $this->admin->id,
            'valorTotal' => 1000,
            'solicitante' => 'Solicitante Test',
            'ativar_notificacao_renovacao' => 1,
            't' => 'unidade',
            'unidade_id' => $this->unidade->id,
        ]);

        $response->assertStatus(302);

        $servico = Servico::where('os', 'OS-TEST-456')->first();
        $this->assertNotNull($servico);
        $this->assertTrue($servico->ativar_notificacao_renovacao);
        $this->assertEquals(180, $servico->dias_para_notificacao_renovacao);
    }

    public function test_artisan_command_sends_notification_at_calculated_date()
    {
        Mail::fake();

        // Create a service manually to avoid mass assignment block
        $servico = new Servico();
        $servico->tipo = 'licencaOperacao';
        $servico->os = 'OS-CMD-123';
        $servico->nome = 'AVCB';
        $servico->situacao = 'finalizado';
        $servico->responsavel_id = $this->admin->id;
        $servico->empresa_id = $this->empresa->id;
        $servico->unidade_id = $this->unidade->id;
        $servico->licenca_validade = Carbon::today()->addDays(30)->toDateString();
        $servico->ativar_notificacao_renovacao = true;
        $servico->dias_para_notificacao_renovacao = 30; // should fire today
        $servico->save();

        $exitCode = Artisan::call('servicos:check-renovacao');
        $this->assertEquals(0, $exitCode);

        // Verify mail sent
        Mail::assertSent(RenovacaoServicoMail::class, function ($mail) use ($servico) {
            return $mail->servico->id === $servico->id && $mail->hasTo($this->admin->email);
        });

        // Verify database marked as sent
        $servico->refresh();
        $this->assertNotNull($servico->notificacao_renovacao_enviada_at);

        // Verify history logged
        $history = Historico::where('servico_id', $servico->id)->first();
        $this->assertNotNull($history);
        $this->assertStringContainsString('Notificação de renovação de serviço enviada por e-mail', $history->observacoes);
    }

    public function test_artisan_command_does_not_send_notification_before_date()
    {
        Mail::fake();

        // Create a service manually
        $servico = new Servico();
        $servico->tipo = 'licencaOperacao';
        $servico->os = 'OS-CMD-456';
        $servico->nome = 'AVCB';
        $servico->situacao = 'finalizado';
        $servico->responsavel_id = $this->admin->id;
        $servico->empresa_id = $this->empresa->id;
        $servico->unidade_id = $this->unidade->id;
        $servico->licenca_validade = Carbon::today()->addDays(31)->toDateString();
        $servico->ativar_notificacao_renovacao = true;
        $servico->dias_para_notificacao_renovacao = 30; // target is tomorrow
        $servico->save();

        $exitCode = Artisan::call('servicos:check-renovacao');
        $this->assertEquals(0, $exitCode);

        // Verify no mail sent
        Mail::assertNotSent(RenovacaoServicoMail::class);

        $servico->refresh();
        $this->assertNull($servico->notificacao_renovacao_enviada_at);
    }

    public function test_artisan_command_does_not_send_notification_twice()
    {
        Mail::fake();

        // Create a service manually
        $servico = new Servico();
        $servico->tipo = 'licencaOperacao';
        $servico->os = 'OS-CMD-789';
        $servico->nome = 'AVCB';
        $servico->situacao = 'finalizado';
        $servico->responsavel_id = $this->admin->id;
        $servico->empresa_id = $this->empresa->id;
        $servico->unidade_id = $this->unidade->id;
        $servico->licenca_validade = Carbon::today()->addDays(30)->toDateString();
        $servico->ativar_notificacao_renovacao = true;
        $servico->dias_para_notificacao_renovacao = 30;
        $servico->notificacao_renovacao_enviada_at = Carbon::now()->subDay();
        $servico->save();

        $exitCode = Artisan::call('servicos:check-renovacao');
        $this->assertEquals(0, $exitCode);

        Mail::assertNotSent(RenovacaoServicoMail::class);
    }

    public function test_update_resets_notification_enviada_at_when_settings_change()
    {
        // Create a service manually
        $servico = new Servico();
        $servico->tipo = 'licencaOperacao';
        $servico->os = 'OS-EDIT-123';
        $servico->nome = 'AVCB';
        $servico->situacao = 'finalizado';
        $servico->responsavel_id = $this->admin->id;
        $servico->empresa_id = $this->empresa->id;
        $servico->unidade_id = $this->unidade->id;
        $servico->licenca_validade = Carbon::today()->addDays(30)->toDateString();
        $servico->ativar_notificacao_renovacao = true;
        $servico->dias_para_notificacao_renovacao = 30;
        $servico->notificacao_renovacao_enviada_at = Carbon::now();
        $servico->save();

        // Create mock financeiro record manually
        $financeiro = new \App\Models\ServicoFinanceiro();
        $financeiro->servico_id = $servico->id;
        $financeiro->valorTotal = 1000;
        $financeiro->valorAberto = 1000;
        $financeiro->status = 'aberto';
        $financeiro->save();

        $this->actingAs($this->admin);

        // Update it with a different validity date
        $response = $this->put(route('servicos.update', $servico->id), [
            'tipo' => 'licencaOperacao',
            'os' => 'OS-EDIT-123',
            'nome' => 'AVCB',
            'situacao' => 'finalizado',
            'responsavel_id' => $this->admin->id,
            'valorTotal' => 1000,
            'valorAberto' => 1000,
            'solicitante' => 'Solicitante Test',
            'licenca_validade' => Carbon::today()->addDays(40)->format('d/m/Y'), // Changed!
            'ativar_notificacao_renovacao' => 1,
            'dias_para_notificacao_renovacao' => 30,
        ]);

        $response->assertStatus(302);

        $servico->refresh();
        // Since parameters changed, the status should have been reset to null
        $this->assertNull($servico->notificacao_renovacao_enviada_at);
    }
}
