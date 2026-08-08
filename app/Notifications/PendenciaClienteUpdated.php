<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class PendenciaClienteUpdated extends Notification
{
    use Queueable, SerializesModels;

    public $pendencia;
    public $mensagem;
    public $route;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($pendencia, $mensagem, $route = 'cliente.pendencia.show')
    {
        $this->pendencia = $pendencia;
        $this->mensagem = $mensagem;
        $this->route = $route;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'pendencia_id' => $this->pendencia->id,
            'servico_id' => $this->pendencia->servico_id,
            'mensagem' => $this->mensagem,
            'route' => $this->route,
        ];
    }
}
