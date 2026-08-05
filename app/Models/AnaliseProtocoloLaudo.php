<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnaliseProtocoloLaudo extends Model
{
    protected $table = 'analise_protocolo_laudos';

    protected $fillable = [
        'servico_id',
        'status',
        'descricao',
    ];

    public function servico()
    {
        return $this->belongsTo('App\Models\Servico', 'servico_id');
    }

    public function protocolo()
    {
        return $this->hasOne('App\Models\Protocolo', 'analise_protocolo_laudo_id');
    }

    public function laudo()
    {
        return $this->hasOne('App\Models\Laudo', 'analise_protocolo_laudo_id');
    }

    public function documentos()
    {
        return $this->hasMany('App\Models\Documento', 'analise_protocolo_laudo_id');
    }
}
