<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestMentionNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mention {user_id? : O ID do usuário a ser notificado} {servico_id? : O ID do serviço relacionado}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispara uma notificação de menção de teste (Interna + Webhook n8n)';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userId = $this->argument('user_id') ?: \App\User::first()->id;
        $servicoId = $this->argument('servico_id') ?: \App\Models\Servico::first()->id;

        $user = \App\User::find($userId);
        $servico = \App\Models\Servico::with(['unidade.empresa'])->find($servicoId);

        if (!$user || !$servico) {
            $this->error('Usuário ou Serviço não encontrado.');
            return;
        }

        $this->info("Iniciando teste de menção para: {$user->name} no serviço #{$servico->id}");

        $resumo = "Este é um resumo de teste gerado pelo comando artisan para validar o fluxo de webhook e notificações internas.";
        $observacoes = "@{$user->name} teste de notificação via linha de comando.";

        // 1. Notificação Interna
        try {
            $this->comment('-> Enviando notificação interna (sininho)...');
            $route = $user->privileges == 'admin' ? 'servicos.show' : 'cliente.servico.show';
            $user->notify(new \App\Notifications\UserMentioned($servico, $route, $resumo));
            $this->info('✓ Notificação interna enviada.');
        } catch (\Exception $e) {
            $this->error('X Erro na notificação interna: ' . $e->getMessage());
        }

        // 2. Webhook
        try {
            $this->comment('-> Disparando Webhook para o n8n...');
            $webhookService = new \App\Services\WebhookService();
            $success = $webhookService->sendMentionEmail($user, $servico, $resumo, $observacoes);
            
            if ($success) {
                $this->info('✓ Webhook disparado com sucesso para o n8n!');
            } else {
                $this->error('X Falha ao disparar o Webhook. Verifique se a URL em WEBHOOK_EMAIL_URL está correta.');
            }
        } catch (\Exception $e) {
            $this->error('X Erro no Webhook: ' . $e->getMessage());
        }

        $this->info('Fim do teste.');
    }
}
