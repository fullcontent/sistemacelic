<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Servico;
use App\Models\Historico;
use App\User;
use App\Mail\RenovacaoServicoMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckRenovacaoServicoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'servicos:check-renovacao';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica os serviços próximos da renovação e dispara as notificações configuradas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = Carbon::today();

        $servicos = Servico::where('ativar_notificacao_renovacao', true)
            ->whereNotNull('licenca_validade')
            ->whereNull('notificacao_renovacao_enviada_at')
            ->whereNotIn('situacao', ['arquivado', 'cancelado'])
            ->with(['responsavel', 'coresponsavel', 'unidade', 'empresa'])
            ->get();

        foreach ($servicos as $servico) {
            $dias = $servico->dias_para_notificacao_renovacao ?? 180;
            $validade = Carbon::parse($servico->licenca_validade);
            $dataNotificacao = $validade->copy()->subDays($dias);

            if ($today->greaterThanOrEqualTo($dataNotificacao)) {
                $recipients = [];
                if ($servico->responsavel && $servico->responsavel->email) {
                    $recipients[] = $servico->responsavel->email;
                }
                if ($servico->coresponsavel && $servico->coresponsavel->email) {
                    $recipients[] = $servico->coresponsavel->email;
                }

                // Se não houver e-mail de responsáveis, envia para os admins ativos
                if (empty($recipients)) {
                    $admins = User::where('privileges', 'admin')->where('active', 1)->pluck('email')->toArray();
                    $recipients = array_filter($admins);
                }

                $recipients = array_unique($recipients);

                if (!empty($recipients)) {
                    Mail::to($recipients)->send(new RenovacaoServicoMail($servico));
                    
                    // Salva a data do envio no serviço
                    $servico->notificacao_renovacao_enviada_at = Carbon::now();
                    $servico->save();

                    // Cria registro de histórico
                    $history = new Historico();
                    $history->servico_id = $servico->id;
                    $history->user_id = null; // Ação do sistema
                    $history->observacoes = "Notificação de renovação de serviço enviada por e-mail para: " . implode(', ', $recipients);
                    $history->created_at = Carbon::now('america/sao_paulo');
                    $history->save();
                    
                    $this->info("Notificação enviada para o serviço ID: {$servico->id} (OS: {$servico->os})");
                }
            }
        }
    }
}

