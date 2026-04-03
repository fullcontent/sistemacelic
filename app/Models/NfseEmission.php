<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfseEmission extends Model
{
    protected $fillable = [
        'faturamento_id',
        'nfse_configuration_id',
        'modo',
        'opcao_automatica',
        'status',
        'payload',
        'retorno',
        'zip_path',
        'observacoes',
    ];

    public function faturamento()
    {
        return $this->belongsTo('App\\Models\\Faturamento', 'faturamento_id');
    }

    public function configuracao()
    {
        return $this->belongsTo('App\\Models\\NfseConfiguration', 'nfse_configuration_id');
    }

    public function itens()
    {
        return $this->hasMany('App\\Models\\NfseEmissionItem', 'nfse_emission_id');
    }
}
