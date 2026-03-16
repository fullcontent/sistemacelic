<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemCompraVinculo extends Model
{
    public function servico()
    {
        return $this->belongsTo('App\Models\Servico', 'servico_id');
    }
}
