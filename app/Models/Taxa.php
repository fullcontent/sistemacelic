<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxa extends Model
{
    //

    public function servico()
    {
    	return $this->belongsTo('App\Models\Servico');
    }
}
