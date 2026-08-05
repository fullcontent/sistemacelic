<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Protocolo extends Model
{
    protected $table = 'protocolos';

    protected $fillable = [
        'analise_protocolo_laudo_id',
        'numero',
        'data_protocolo',
        'anexo',
        'tipo',
    ];

    protected $dates = [
        'data_protocolo',
    ];

    public function analise()
    {
        return $this->belongsTo('App\Models\AnaliseProtocoloLaudo', 'analise_protocolo_laudo_id');
    }
}
