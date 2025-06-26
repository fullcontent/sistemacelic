<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestador extends Model
{
    public function rating()
    {
        return $this->hasMany('App\Models\PrestadorComentario');
    }
}
