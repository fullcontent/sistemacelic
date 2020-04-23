<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicoLpu extends Model
{
    //

    public function servicos()
    {
    	return $this->hasMany('App\Models\Servico','servico_lpu');
    }
}
