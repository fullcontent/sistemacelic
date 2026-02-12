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

    public $servico, $route;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($servico, $route)
    {
        //
        $this->servico = $servico;
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
        return ['database', 'mail'];
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
                'route' => $this->route
            ]);
    }


    public function toDatabase($notifiable)
    {



        return [
            //
            'mensagem' => 'Voce foi mencionado nesse servico',
            'servico' => $this->servico->id,
            'action' => route($this->route, $this->servico->id),

        ];
    }
}
