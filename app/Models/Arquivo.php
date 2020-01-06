<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    //
    public function empresa()
    {
    	return $this->belongsTo('App\Models\Empresa');
    }
    public function unidade()
    {
    	return $this->belongsTo('App\Models\Unidade');

    }

    public function servico()
    {
    	return $this->belongsTo('App\Models\Servico');
    }
}
