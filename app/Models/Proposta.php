<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposta extends Model
{
    public function servicos()
        {
        	return $this->hasMany('App\Models\PropostaServico');
        }

    public function empresa()
    {
        return $this->belongsTo('App\Models\Empresa');
    }

    public function unidade()
    {
        return $this->belongsTo('App\Models\Unidade');
    }

    public function servicosFaturados()
    {
        return $this->hasManyThrough('App\Models\Servico','App\Models\PropostaServico','proposta_id','propostaServico_id')->whereHas('faturamento');
    }
    
}
