<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
        'analise_protocolo_laudo_id',
        'nome',
        'arquivo',
        'tipo',
    ];

    public function analise()
    {
        return $this->belongsTo('App\Models\AnaliseProtocoloLaudo', 'analise_protocolo_laudo_id');
    }
}
