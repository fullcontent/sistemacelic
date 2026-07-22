<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comprovante extends Model
{
    protected $table = 'comprovantes';

    protected $fillable = [
        'boleto_id',
        'valor_pago',
        'favorecido_pago',
        'data_pagamento',
        'arquivo_path',
        'status_auditoria',
        'divergencia',
        'motivo_divergencia',
        'reembolso_bloqueado',
    ];

    public function boleto()
    {
        return $this->belongsTo('App\Models\Boleto', 'boleto_id');
    }
}
