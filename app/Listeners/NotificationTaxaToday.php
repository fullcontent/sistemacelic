<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\UserLoggedIn;
use Auth;
use App\Models\Taxa;
use Carbon\Carbon;
use App\Notifications\VencimentoTaxaToday;
use App\User;

class NotificationTaxaToday 
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
        
        $taxas = Taxa::whereDate('vencimento','=',Carbon::now())->where('situacao','aberto')->get();
        $groupTaxas = $taxas->pluck('id');
        $notifications = auth()->user()->notifications->whereIn('data.taxa.id',$groupTaxas)->pluck('data.taxa.id');

        if(!$notifications->count()==$taxas->count())
        {
            foreach($taxas as $t)
            {   
                $user = User::find($t->servico->responsavel_id);
                $user->notify(new VencimentoTaxaToday($t)); 
            }
        }



        
    }
}
