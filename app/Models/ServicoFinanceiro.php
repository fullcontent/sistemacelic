<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicoFinanceiro extends Model
{   

    protected $fillable = ['valorFaturado'];
    public function servico()
    {
    	return $this->belongsTo('App\Models\Servico');
    }
}
