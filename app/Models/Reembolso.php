<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reembolso extends Model
{
    //
    public function empresa()
    {
        return $this->belongsTo('App\Models\Empresa','empresa_id');
    }

    public function taxas()
    {
        return $this->hasMany('App\Models\ReembolsoTaxa','reembolso_id');
    }
}
