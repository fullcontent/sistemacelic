<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    //


	public function empresa()
	{
		return $this->belongsTo(Models\Empresa::class);
	}	

	public function user()
		{
			return $this->belongsTo(User::class);
		}
}
