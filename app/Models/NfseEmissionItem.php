<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfseEmissionItem extends Model
{
    protected $fillable = [
        'nfse_emission_id',
        'servico_id',
        'faturamento_servico_id',
        'cnpj_tomador',
        'descricao_servico',
        'valor_servico',
        'numero_nf',
        'external_id',
        'status',
        'pdf_path',
        'xml_path',
        'mensagem_erro',
        'additional_data',
    ];

    public function emissao()
    {
        return $this->belongsTo('App\\Models\\NfseEmission', 'nfse_emission_id');
    }

    public function servico()
    {
        return $this->belongsTo('App\\Models\\Servico', 'servico_id');
    }
}
