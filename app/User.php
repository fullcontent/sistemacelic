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
        'name', 'email', 'password', 'privileges', 'permitir_interacoes', 'permitir_acesso_servicos'
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
        'permitir_interacoes' => 'boolean',
        'permitir_acesso_servicos' => 'boolean',
    ];


    public function routeNotificationForMail($notification)
    {
    
    return $this->email;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new Notifications\ResetPasswordNotification($token));
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
    
    public function servicos()
    {
        return $this->hasMany('App\Models\Servico', 'responsavel_id')
            ->orWhere('coresponsavel_id', $this->id)
            ->orWhere('analista1_id', $this->id)
            ->orWhere('analista2_id', $this->id);
    }

    // In-memory cache for user departments to avoid multiple file reads
    protected static $cachedUserDepartments = null;

    protected static function loadUserDepartments()
    {
        if (self::$cachedUserDepartments === null) {
            $path = storage_path('app/user_departments.json');
            if (file_exists($path)) {
                $content = file_get_contents($path);
                self::$cachedUserDepartments = json_decode($content, true) ?: [];
            } else {
                self::$cachedUserDepartments = [];
            }
        }
        return self::$cachedUserDepartments;
    }

    public function getDepartamentosAttribute()
    {
        $depts = self::loadUserDepartments();
        return $depts[$this->id] ?? null;
    }

    public function hasDepartmentAccess($departamento)
    {
        $allowed = $this->departamentos;
        if ($allowed === null || !is_array($allowed) || count($allowed) === 0) {
            return true;
        }
        return in_array($departamento, $allowed);
    }
}
