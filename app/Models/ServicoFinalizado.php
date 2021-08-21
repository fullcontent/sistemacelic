<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicoFinalizado extends Model
{
    //

    protected $dates = ['finalizado'];


    public function servico()
    {
    	return $this->belongsTo('App\Models\Servico');
    }
}
