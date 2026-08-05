<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicoEquipe extends Model
{
    protected $table = 'servico_equipe';

    protected $fillable = [
        'servico_id',
        'user_id',
        'papel',
        'data_vinculo',
        'data_desvinculo',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_vinculo' => 'datetime',
        'data_desvinculo' => 'datetime',
    ];

    public function servico()
    {
        return $this->belongsTo('App\Models\Servico', 'servico_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
