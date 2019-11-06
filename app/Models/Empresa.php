<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    

		public function unidades()
		{
			
			return $this->hasMany(Unidade::class);
		}


		public function user()
		{
			return $this->belongsTo(User::class);
		}

}
