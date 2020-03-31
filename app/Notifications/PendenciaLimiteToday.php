<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;




class PendenciaLimiteToday extends Notification
{
    use Queueable, SerializesModels;

    public $pendencia;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($pendencia)
    {
        //
        $this->pendencia = $pendencia;

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
    public function toDatabase($notifiable)
    {
        return [
            //
            'mensagem'=>'Resolver '.$this->pendencia->pendencia.' hoje!',
            'pendencia'=>$this->pendencia,
            'action'=> route('pendencia.index', ['servico_id'=>$this->pendencia->servico_id]),

        ];
    }
}