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
        return $this->belongsTo('App\Models\OrdemCompra');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
