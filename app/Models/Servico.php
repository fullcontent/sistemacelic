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
    	return $this->hasMany('App\Models\Historico')->orderBy('created_at','desc')->take(3);
    }

    public function taxas()
    {
        return $this->hasMany('App\Models\Taxa');
    }
    public function responsavel()
    {
        return $this->belongsTo('App\User','responsavel_id','id');
    }
}
