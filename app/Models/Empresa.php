<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    

		public function unidades()
		{
			
			return $this->hasMany('App\Models\Unidade');
		}


		public function user()
		{
			return $this->belongsTo('App\User');
		}

}
