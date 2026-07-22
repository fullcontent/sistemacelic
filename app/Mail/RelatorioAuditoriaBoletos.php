<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RelatorioAuditoriaBoletos extends Mailable
{
    use Queueable, SerializesModels;

    public $comprovantesDivergentes;
    public $estatisticas;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($comprovantesDivergentes, $estatisticas)
    {
        $this->comprovantesDivergentes = $comprovantesDivergentes;
        $this->estatisticas = $estatisticas;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('[CELIC-IA] Relatório de Auditoria de Boletos e Reembolsos')
                    ->markdown('emails.financeiro.auditoria_boletos');
    }
}
