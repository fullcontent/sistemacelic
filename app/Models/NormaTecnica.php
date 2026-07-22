<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NormaTecnica extends Model
{
    protected $table = 'normas_tecnicas';

    protected $fillable = [
        'orgao',
        'estado',
        'municipio',
        'titulo',
        'link_original',
        'arquivo_path',
        'data_publicacao',
        'indexado_rag',
    ];
}
