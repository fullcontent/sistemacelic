<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrestadorComentario extends Model
{
    public function prestador()
    {
        return $this->belongsTo('App\Models\Prestador');
    }

    public function ordemCompra()
    {
        return $this->belongsTo('App\Models\OrdemCompra','ordemCompra_id');
    }

    public function servico()
    {
        return $this->hasOneThrough(
            'App\Models\Servico',
            'App\Models\OrdemCompra',
            'servico_id', // Foreign key on the cars table...
            'id', // Foreign key on the owners table...
            'ordemCompra_id', // Local key on the mechanics table...
            'id' // Local key on the cars table...
        );

    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
