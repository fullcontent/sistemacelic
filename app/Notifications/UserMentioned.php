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

    public $servico;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($servico)
    {
        //
        $this->servico = $servico;
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

   
    public function toDatabase($notifiable)
    {
        return [
            //
            'mensagem'=>'Voce foi mencionado nesse servico',
            'servico'=>$this->servico,
            'action'=> route('servicos.show', $this->servico),

        ];
    }
}
