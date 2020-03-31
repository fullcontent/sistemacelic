<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



class User extends Authenticatable
{
    use Notifiable;
   

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','privileges'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function routeNotificationForMail($notification)
    {
    
    return $this->email;
    }

   
    public function empresas()
    {
      return $this->hasManyThrough(
            'App\Models\Empresa',
            'App\UserAccess',
            'user_id', // Foreign key on users table...
            'id', // Foreign key on history table...
            'id', // Local key on suppliers table...
            'empresa_id' // Local key on users table...
        );
                 
    }
    public function unidades()
    {
      return $this->hasManyThrough(
            'App\Models\Unidade',
            'App\UserAccess',
            'user_id', // Foreign key on users table...
            'id', // Foreign key on history table...
            'id', // Local key on suppliers table...
            'unidade_id' // Local key on users table...
        );
                 
    }

    public function acesso_empresa()
    {
        return $this->belongsToMany('App\UserAccess','user_accesses','user_id','empresa_id');
    }
    public function acesso_unidade()
    {
        return $this->belongsToMany('App\UserAccess','user_accesses','user_id','unidade_id');
    }
    

    
    
    
    
}
