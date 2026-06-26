<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RenovacaoServicoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $servico;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($servico)
    {
        $this->servico = $servico;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.renovacao-servico')
                    ->subject('Notificação de Renovação de Serviço - OS ' . $this->servico->os)
                    ->with('servico', $this->servico);
    }
}
