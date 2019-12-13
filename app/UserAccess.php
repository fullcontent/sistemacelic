<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    //
    public function empresa()
    {
    	return $this->belongsTo("App\Models\Empresa");
    }
    public function unidade()
    {
    	return $this->belongsTo("App\Models\Unidade");
    }
}
