<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitanteEmpresa extends Model
{
    //
    public function solicitante()
    {
        return $this->belongsTo('App\Models\Solicitante');
    }
}
