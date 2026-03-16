<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class UserMentioned extends Notification
{
    use Queueable, SerializesModels;

    public $servico, $route, $resumo;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($servico, $route, $resumo = null)
    {
        //
        $this->servico = $servico;
        $this->route = $route;
        $this->resumo = $resumo;
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nova interação no serviço #' . $this->servico->id)
            ->markdown('emails.usuarioMencionado', [
                'servico' => $this->servico,
                'route' => $this->route,
                'resumo' => $this->resumo
            ]);
    }


    public function toDatabase($notifiable)
    {
        $unidade = $this->servico->unidade ? $this->servico->unidade->nomeFantasia : 'N/A';

        return [
            'mensagem' => $unidade,
            'servico' => $this->servico->os,
            'action' => route($this->route, $this->servico->id),
        ];
    }
}
