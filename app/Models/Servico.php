<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    //
    public function unidade()
    {
    	return $this->belongsTo('App\Models\Unidade');
    }

    public function empresa()
    {
    	return $this->belongsTo('App\Models\Empresa','empresa_id');
    }
    
    public function historico()
    {
    	return $this->hasMany('App\Models\Historico');
    }

    public function taxas()
    {
        return $this->hasMany('App\Models\Taxa');
    }
}
