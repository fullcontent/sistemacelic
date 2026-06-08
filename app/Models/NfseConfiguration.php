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
        'tipo_tributacao_iss',
        'exigibilidade_iss',
        'valor_aproximado_tributos',
        'ativo',
        'inscricao_municipal',
        'email_emitente',
        'telefone_emitente',
        'logradouro',
        'numero',
        'bairro',
        'codigo_cidade',
        'cep',
        'uf',
        'regime_tributario',
        'login_prefeitura',
        'senha_prefeitura',
        'certificado',
        'producao',
    ];

    protected $casts = [
        'suspensao_exigibilidade_issqn' => 'boolean',
        'issqn_exigibilidade_suspensa' => 'boolean',
        'issqn_retido' => 'boolean',
        'beneficio_municipal' => 'boolean',
        'ativo' => 'boolean',
        'producao' => 'boolean',
    ];

    public function scopeAtiva($query)
    {
        return $query->where('ativo', true);
    }

    public function dadosCastro()
    {
        return $this->belongsTo('App\Models\DadosCastro', 'dados_castro_id');
    }
}
