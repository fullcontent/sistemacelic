<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestador extends Model
{
    public function comentarios()
    {
        return $this->hasMany('App\Models\PrestadorComentario');
    }
}
