<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laudo extends Model
{
    protected $table = 'laudos';

    protected $fillable = [
        'analise_protocolo_laudo_id',
        'numero',
        'data_emissao',
        'anexo',
    ];

    protected $dates = [
        'data_emissao',
    ];

    public function analise()
    {
        return $this->belongsTo('App\Models\AnaliseProtocoloLaudo', 'analise_protocolo_laudo_id');
    }
}
