<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Notifications\VencimentoTaxaToday;
use App\Notifications\PendenciaLimiteToday;
use App\Notifications\PendenciaLimiteTomorrow;
use App\Notifications\Licenca60days;
use App\User;
use App\Models\Taxa;
use App\Models\Servico;
use App\Models\Pendencia;
use Carbon\Carbon;

class SendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendNotifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Enviar notificações das taxas que vencem no dia

        $taxasToday = Taxa::whereDate('vencimento','=', Carbon::now())->where('situacao','aberto')->get();

        foreach($taxasToday as $t)
            {   
                $user = User::find($t->servico->responsavel_id);
                $user->notify(new VencimentoTaxaToday($t)); 
            }

        //--------------------------------------------------------------------------------

        //Enviar notificações das taxas que vencem no dia seguinte

        $taxasTomorrow = Taxa::whereDate('vencimento','=', Carbon::now()->subDays(1))->where('situacao','aberto')->get();

        foreach($taxasTomorrow as $d)
            {   
                $user = User::find($d->servico->responsavel_id);
                $user->notify(new VencimentoTaxaToday($d)); 
            }

        //--------------------------------------------------------------------------------

        //Enviar notificações das pendências com limite hoje


        $pendenciasToday = Pendencia::whereDate('vencimento','=', Carbon::now())->where('status','pendente')->get();

        foreach($pendenciasToday as $p)
        {

            $user = User::find($p->responsavel_id);
            $user->notify(new PendenciaLimiteToday($p)); 

        }

        //--------------------------------------------------------------------------------

        //Enviar notificações das pendências com limite amanhã


        $pendenciasTomorrow = Pendencia::whereDate('vencimento','=', Carbon::now()->subDays(1))->where('status','pendente')->get();

        foreach($pendenciasTomorrow as $p2)
        {

            $user = User::find($p2->responsavel_id);
            $user->notify(new PendenciaLimiteTomorrow($p2)); 

        }

        //--------------------------------------------------------------------------------

        //Enviar notificações de licença 60 dias antes

        $licenca = Servico::where('tipo','primario')->where('situacao','finalizado')->whereDate('licenca_validade','=',Carbon::now()->addDays(60))->get();

        foreach($licenca as $l)
        {

            $user = User::find($l->responsavel_id);
            $user->notify(new Licenca60days($l)); 

        }

        //--------------------------------------------------------------------------------

        
    }
}
