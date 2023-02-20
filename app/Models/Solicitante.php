<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitante extends Model
{
    public function servicos()
    {
    	return $this->hasMany('App\Models\Servico','solicitante','nome');
    }

    
    public function empresas()
    {
        return $this->hasManyThrough(
            'App\Models\Empresa',
            'App\Models\SolicitanteEmpresa',
            'solicitante_id', 
            'id', 
            'id', 
            'empresa_id' 
        );
    }

    
}
