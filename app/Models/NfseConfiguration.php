<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfseConfiguration extends Model
{
    protected $fillable = [
        'dados_castro_id',
        'provider',
        'emit_as',
        'simples_regime',
        'tomador_tipo',
        'intermediario_tipo',
        'local_prestacao',
        'municipio_nome',
        'municipio_ibge',
        'codigo_tributacao_nacional',
        'suspensao_exigibilidade_issqn',
        'item_nbs',
        'issqn_exigibilidade_suspensa',
        'issqn_retido',
        'beneficio_municipal',
        'pis_cofins_situacao',
        'aliquota_simples',
        'valor_aproximado_tributos',
        'ativo',
    ];

    protected $casts = [
        'suspensao_exigibilidade_issqn' => 'boolean',
        'issqn_exigibilidade_suspensa' => 'boolean',
        'issqn_retido' => 'boolean',
        'beneficio_municipal' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function scopeAtiva($query)
    {
        return $query->where('ativo', true);
    }
}
