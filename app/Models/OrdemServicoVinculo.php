<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemServicoVinculo extends Model
{
    public function servico()
    {
        return $this->belongsTo('App\Models\Servico', 'servico_id');
    }
}
