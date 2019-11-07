<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    

		public function unidades()
		{
			return $this->hasMany('App\Models\Unidade');
		}


		public function users()
		{
			return $this->hasManyThrough('App\User','App\UserAccess','empresa_id','id');
		}

}
