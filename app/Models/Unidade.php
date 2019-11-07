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

	public function user()
		{
			return $this->belongsTo('App\User');
		}
}
