<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DadosCastro extends Model
{
    protected $table = 'dados_castros';
    public $timestamps = false;
    
    protected $fillable = [
        'cnpj', 
        'razaoSocial', 
        'chavePix', 
        'banco', 
        'agencia', 
        'conta',
        'ativo'
    ];

    public function empresas()
    {
        return $this->hasMany('App\Models\Empresa', 'dados_castro_id');
    }

    public function nfseConfiguration()
    {
        return $this->hasOne('App\Models\NfseConfiguration', 'dados_castro_id');
    }

    public function nfseEmissions()
    {
        return $this->hasMany('App\Models\NfseEmission', 'dados_castro_id');
    }
}
