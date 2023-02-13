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

   public function scopeFilter($query, $filters)
    {
        if ($filters) {
            foreach ($filters as $key => $value) {
                if (is_array($value)) {
                    $query->where(function ($q) use ($key, $value) {
                        foreach ($value as $v) {
                            $q->orWhere($key, 'like', '%' . $v . '%');
                        }
                    });
                } else {
                    $query->where($key, 'like', '%' . $value . '%');
                }
            }
        }

        return $query;
    }
}
