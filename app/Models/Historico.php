<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historico extends Model
{
    //
    
   public function servico()
   {
   	
   		return $this->belongsTo('App\Models\Servico');
   }


   public function user()
   {
   		return $this->belongsTo('App\User');
   }
}
