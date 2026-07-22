<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comprovante;
use App\Mail\RelatorioAuditoriaBoletos;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class GerarRelatorioAuditoriaBoletos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auditoria:boletos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera e envia o relatório semanal de auditoria de boletos via IA.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando geração de relatório de auditoria...');

        $seteDiasAtras = Carbon::now()->subDays(7);

        $comprovantesAuditados = Comprovante::with('boleto')
            ->where('created_at', '>=', $seteDiasAtras)
            ->where('status_auditoria', 'extraido')
            ->get();

        $divergentes = $comprovantesAuditados->filter(function ($comp) {
            return $comp->divergencia == true;
        });

        $bloqueados = $comprovantesAuditados->filter(function ($comp) {
            return $comp->reembolso_bloqueado == true;
        });

        $estatisticas = [
            'total_auditado' => $comprovantesAuditados->count(),
            'total_divergentes' => $divergentes->count(),
            'reembolsos_bloqueados' => $bloqueados->count(),
        ];

        if ($divergentes->count() > 0) {
            // Em produção, isso iria para a diretoria financeira
            Mail::to('financeiro@castroempresarial.com.br')->send(new RelatorioAuditoriaBoletos($divergentes, $estatisticas));
            $this->info('Relatório enviado com ' . $divergentes->count() . ' divergências encontradas.');
        } else {
            $this->info('Nenhuma divergência encontrada nesta semana.');
        }

        return 0;
    }
}
