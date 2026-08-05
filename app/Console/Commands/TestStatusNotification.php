<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Servico;
use App\Services\WebhookService;

class TestStatusNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:status-webhook {servico_id? : O ID do serviço a ser testado}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispara uma notificação de mudança de status de teste com laudos/protocolos para o n8n';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $servicoId = $this->argument('servico_id') ?: Servico::first()->id;

        $servico = Servico::with(['unidade.empresa', 'responsavel', 'arquivos', 'analiseProtocoloLaudos.protocolo', 'analiseProtocoloLaudos.laudo'])->find($servicoId);

        if (!$servico) {
            $this->error("Serviço #{$servicoId} não encontrado.");
            return 1;
        }

        $this->info("Iniciando teste de mudança de status para o Serviço #{$servico->id} - {$servico->nome}");

        $previousStatus = 'andamento';
        $newStatus = 'protocolado';
        $observacoes = 'Teste de disparo automático de alteração de status via linha de comando.';

        try {
            $this->comment('-> Disparando Webhook de Mudança de Status para o n8n...');
            $webhookService = new WebhookService();
            $success = $webhookService->sendStatusChangeEmail($servico, $previousStatus, $newStatus, $observacoes);

            if ($success) {
                $this->info('✓ Webhook de mudança de status disparado com sucesso para o n8n!');
            } else {
                $this->error('X Falha ao disparar o Webhook. Verifique se a URL em WEBHOOK_EMAIL_URL está correta.');
            }
        } catch (\Exception $e) {
            $this->error('X Erro no Webhook: ' . $e->getMessage());
        }

        $this->info('Fim do teste.');
        return 0;
    }
}
