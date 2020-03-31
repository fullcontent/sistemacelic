<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Notifications\VencimentoTaxaToday;
use App\User;
use App\Models\Taxa;
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
        //

        $taxas = Taxa::whereDate('vencimento','<=', Carbon::now())->where('situacao','aberto')->get();

        foreach($taxas as $t)
            {   
                $user = User::find($t->servico->responsavel_id);
                $user->notify(new VencimentoTaxaToday($t)); 
            }

        
    }
}
