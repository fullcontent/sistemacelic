<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendencia extends Model
{
    
    public function servico()
        {
        	return $this->belongsTo('App\Models\Servico');
        }

    public function responsavel()
        {
        	return $this->belongsTo('App\User','responsavel_id');
        }

        
}
