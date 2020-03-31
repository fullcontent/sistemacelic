<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

use App\Mail\VencimentoLicenca60days;

use App\User;


class Licenca60days extends Notification
{
    use Queueable, SerializesModels;

    public $servico;
    public $user;
    

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($servico,$user)
    {
        //
        $this->servico = $servico;
        $this->user = $user;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
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
            'mensagem'=>''.$this->servico->nome.' vencerá em 60 dias.',
            'servico'=>$this->servico,
            'action'=> route('servicos.show', $this->servico->id),

        ];
    }

    public function toMail($notifiable)
    {   

        return (new VencimentoLicenca60days($this->servico))
                                ->subject('Vencimento de licença!')
                                ->to($this->user->email);

       
    }
}