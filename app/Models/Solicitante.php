<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitante extends Model
{
    public function servicos()
    {
    	return $this->hasMany('App\Models\Servico','solicitante','nome');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Models\Empresa');
    }
}
