<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaturamentoServico extends Model
{
    //
    public function detalhes()
    {
        return $this->belongsTo('App\Models\Servico', 'servico_id');
    }
}
