<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\UserLoggedIn;
use Auth;
use App\Models\Taxa;
use Carbon\Carbon;
use App\Notifications\VencimentoTaxaTomorrow;
use App\User;

class NotificacaoTaxaTomorrow
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        

        $taxas = Taxa::whereDate('vencimento','=',Carbon::now()->subDays(1))->where('situacao','aberto')->get();
        $groupTaxas = $taxas->pluck('id');
        $notifications = auth()->user()->notifications->whereIn('data.taxa.id',$groupTaxas)->pluck('data.taxa.id');

        if(!$notifications->count()==$taxas->count())
        {
            foreach($taxas as $t)
            {   
                dump($t->servico->responsavel_id);
                $user = User::find($t->servico->responsavel_id);
                $user->notify(new VencimentoTaxaToday($t)); 
            }
        }
                

        
    }
}
