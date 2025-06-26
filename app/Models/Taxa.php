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

    public function unidade()
		{
			return $this->hasOneThrough('App\Models\Unidade','App\Models\Servico','id','id','servico_id','unidade_id');
        }
    
}
