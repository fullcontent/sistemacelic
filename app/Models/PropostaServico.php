<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropostaServico extends Model
{
    public function proposta()
    {
        return $this->belongsTo('App\Models\Proposta');
    }

    public function servicoLpu()
        {
        	return $this->belongsTo('App\Models\ServicoLpu','servicoLpu_id');
        }
    
    public function faturado()
    {
        return $this->hasOneThrough('App\Models\ServicoFinanceiro', 'App\Models\Servico','propostaServico_id')->where('valorFaturado','=','valorTotal');
    }

    public function servicoCriado()
    {
        return $this->hasOne('App\Models\Servico','propostaServico_id');
    }

    public function servicoP()
    {
        return $this->hasOne('App\Models\PropostaServico','servicoPrincipal');
    }
   
    
}
