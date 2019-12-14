<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    //
    protected $table = 'user_accesses';


    public function empresa()
    {
    	return $this->belongsTo("App\Models\Empresa");
    }
    
    public function unidade()
    {
    	return $this->belongsTo("App\Models\Unidade");
    }

    public function user()
    {
    	return $this->belongsTo("App\User");
    }
}
