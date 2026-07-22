<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjetoDossie extends Model
{
    protected $table = 'projeto_dossies';

    protected $fillable = [
        'projeto_id',
        'checklist_gerado',
        'historico_utilizado',
        'status'
    ];

    // Aqui poderia ter o relacionamento com o Servico/Projeto principal
    /*
    public function projeto()
    {
        return $this->belongsTo(Servico::class, 'projeto_id');
    }
    */
}
