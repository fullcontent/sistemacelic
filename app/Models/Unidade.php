<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    //


	public function empresa()
	{
		return $this->belongsTo('App\Models\Empresa');
	}	

	
	public function servicos()
		{
			return $this->hasMany('App\Models\Servico');
		}

		public function taxas()
		{
			return $this->hasManyThrough('App\Models\Taxa','App\Models\Servico','unidade_id','servico_id','id');
		}
}
