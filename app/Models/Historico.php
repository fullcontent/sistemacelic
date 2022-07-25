<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historico extends Model
{
    //
    
   public function servico()
   {
   	
   		return $this->hasOne('App\Models\Servico','id','servico_id');
   }


   public function user()
   {
   		return $this->belongsTo('App\User');
   }

   
}
