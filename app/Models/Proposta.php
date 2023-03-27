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

    public function servicosCriados()
    {
        return $this->hasManyThrough('App\Models\Servico','App\Models\PropostaServico','proposta_id','propostaServico_id');
    }


    
    public function valorTotal()
        {
            return $this->servicos->sum('valor');
        }

        public function dadosCastro()
    {
        return $this->belongsTo('App\Models\DadosCastro','dadosCastro_id');
    }
}
