<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Models\Servico;
use App\Models\ServicoEquipe;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServicosEquipeTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_vincular_equipe_dinamicamente_via_request()
    {

        $admin = User::create([
            'name' => 'Admin Teste',
            'email' => 'admin_test@sistemacelic.com',
            'password' => bcrypt('password'),
            'privileges' => 'admin',
            'permitir_acesso_servicos' => true,
            'active' => true
        ]);

        $coordenador = User::create([
            'name' => 'Coordenador Teste',
            'email' => 'coord_test@sistemacelic.com',
            'password' => bcrypt('password'),
            'privileges' => 'admin',
            'permitir_acesso_servicos' => true,
            'is_coordinator' => true,
            'active' => true
        ]);

        $responsavel = User::create([
            'name' => 'Responsavel Teste',
            'email' => 'resp_test@sistemacelic.com',
            'password' => bcrypt('password'),
            'privileges' => 'admin',
            'permitir_acesso_servicos' => true,
            'active' => true
        ]);

        $analista = User::create([
            'name' => 'Analista Teste',
            'email' => 'analista_test@sistemacelic.com',
            'password' => bcrypt('password'),
            'privileges' => 'admin',
            'permitir_acesso_servicos' => true,
            'active' => true
        ]);

        // Simula o login do admin
        $this->actingAs($admin);

        // Dispara o POST para criar o serviço
        $response = $this->post(route('servicos.store'), [
            'tipo' => 'alvara',
            'nome' => 'Servico Teste Dinamico',
            'os' => 'OS12345',
            'situacao' => 'andamento',
            'solicitante' => 'Solicitante Teste',
            'valorTotal' => 1000.00,
            'ativar_notificacao_renovacao' => 1,
            'dias_para_notificacao_renovacao' => 180,
            'equipe_cargo' => [
                'coordenador',
                'responsavel_tecnico',
                'analista'
            ],
            'equipe_user_id' => [
                $coordenador->id,
                $responsavel->id,
                $analista->id
            ]
        ]);

        // Verifica se houve redirecionamento de sucesso para pendencia.create
        $response->assertStatus(302);

        // Busca o serviço cadastrado
        $servico = Servico::where('os', 'OS12345')->first();
        $this->assertNotNull($servico);

        // Verifica se a pivot foi criada corretamente
        $this->assertTrue($servico->membrosEquipeAtivos()->where('papel', 'coordenador')->exists());
        $this->assertEquals($coordenador->id, $servico->membrosEquipeAtivos()->where('papel', 'coordenador')->first()->user_id);
        $this->assertEquals($responsavel->id, $servico->membrosEquipeAtivos()->where('papel', 'responsavel_tecnico')->first()->user_id);
        $this->assertEquals($analista->id, $servico->membrosEquipeAtivos()->where('papel', 'analista')->first()->user_id);

        // Verifica as colunas clássicas/sincronizadas
        $this->assertEquals($responsavel->id, $servico->responsavel_id);
        $this->assertEquals($coordenador->id, $servico->coresponsavel_id);
        $this->assertEquals($analista->id, $servico->analista1_id);
    }

    public function test_dashboard_exibe_apenas_pendencias_proprias()
    {
        $userA = User::create([
            'name' => 'Kauane',
            'email' => 'kauane@sistemacelic.com',
            'password' => bcrypt('password'),
            'privileges' => 'usuario',
            'permitir_acesso_servicos' => true,
            'active' => true
        ]);

        $userB = User::create([
            'name' => 'Rodrigo',
            'email' => 'rodrigo@sistemacelic.com',
            'password' => bcrypt('password'),
            'privileges' => 'usuario',
            'permitir_acesso_servicos' => true,
            'active' => true
        ]);

        $servico = Servico::create([
            'tipo' => 'alvara',
            'nome' => 'Servico Compartilhado',
            'os' => 'OS99999',
            'situacao' => 'andamento',
            'responsavel_id' => $userB->id,
            'coresponsavel_id' => $userA->id
        ]);

        // Pendência atribuída EXCLUSIVAMENTE ao Rodrigo (userB)
        $pendenciaRodrigo = \App\Models\Pendencia::create([
            'servico_id' => $servico->id,
            'pendencia' => 'Elaborar documento',
            'responsavel_id' => $userB->id,
            'status' => 'pendente'
        ]);

        // Simula login de Kauane (userA)
        $this->actingAs($userA);
        $adminController = new \App\Http\Controllers\AdminController();

        $pendenciasKauane = $adminController->pendencias();
        $this->assertCount(0, $pendenciasKauane);

        // Simula login de Rodrigo (userB)
        $this->actingAs($userB);
        $pendenciasRodrigo = $adminController->pendencias();
        $this->assertCount(1, $pendenciasRodrigo);
        $this->assertEquals($pendenciaRodrigo->id, $pendenciasRodrigo->first()->id);
    }
}
