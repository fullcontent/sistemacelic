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
        	return $this->hasMany('App\Models\ServicoLpu');
        }
    
}
