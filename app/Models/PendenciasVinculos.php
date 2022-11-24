<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendenciasVinculos extends Model
{
    //
    public function pendencia()
    {
        return $this->belongsTo('App\Models\Pendencia');
    }

    public function Servicos()
    {
        return $this->hasMany('App\Models\Servico');
    }

    public function servico()
    {
        return $this->belongsTo('App\Models\Servico')->select('os');
    }
}
