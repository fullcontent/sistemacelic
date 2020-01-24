<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;




class VencimentoLicenca60days extends Notification
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            //
            'mensagem'=>'A licença do '.$this->servico->nome.' vencerá em 60 dias.',
            'servico'=>$this->servico,
            'action'=> route('servicos.show', $this->servico->id),
            

        ];
    }
}
