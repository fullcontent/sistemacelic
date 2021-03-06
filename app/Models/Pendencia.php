<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendencia extends Model
{
    
    public function servico()
        {
        	return $this->belongsTo('App\Models\Servico','servico_id');
        }

    public function responsavel()
        {
        	return $this->belongsTo('App\User','responsavel_id');
        }
    
    public function unidade()

    {
    	return $this->hasOneThrough('App\Models\Unidade','App\Models\Servico','unidade_id','id','servico_id','unidade_id');
    }
    
        
}
