<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReembolsoTaxa extends Model
{
    //
    public function taxa()
    {
        return $this->belongsTo('App\Models\Taxa','taxa_id');
    }

    public function remmbolso()
    {
        return $this->belongsTo('App\Models\Reembolso','reembolso_id');
    }
}
