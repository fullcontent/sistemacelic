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

class SendMailLicencas60Days extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SendMailLicencas60Days';

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
        

        //Enviar notificações de licença 60 dias antes

        $licenca = Servico::where('tipo','primario')->where('situacao','finalizado')->whereDate('licenca_validade','=',Carbon::now()->addDays(60))->get();

        foreach($licenca as $l)
        {

            $user = User::find($l->responsavel_id);
            $user->notify(new Licenca60days($l,$user)); 

        }

        //--------------------------------------------------------------------------------



    }
}
