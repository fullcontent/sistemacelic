<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;




class VencimentoTaxaToday extends Notification
{
    use Queueable, SerializesModels;

    public $taxa;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($taxa)
    {
        //
        $this->taxa = $taxa;

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
            'mensagem'=>''.$this->taxa->nome.' vence hoje!',
            'taxa'=>$this->taxa,
            'action'=> route('taxas.show', $this->taxa->id),


        ];
    }
}