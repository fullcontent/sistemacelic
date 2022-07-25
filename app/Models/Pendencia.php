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

    

    public function vinculos()
    {
        return $this->hasManyThrough(
            'App\Models\Servico',
            'App\Models\PendenciasVinculos',
            'pendencia_id',
            'id',
            'id',
            'servico_id'       
        );
    }
    
    public function vinculo()
    {
        return $this->belongsTo('App\Models\Servico','vinculo_os');
    }
        
}
