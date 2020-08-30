<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faturamento extends Model
{
    //
    public function servicosFaturados()
    {
        return $this->hasMany('App\Models\FaturamentoServico','faturamento_id');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Models\Empresa','empresa_id');
    }
}
