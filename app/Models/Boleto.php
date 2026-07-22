<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boleto extends Model
{
    protected $table = 'boletos';

    protected $fillable = [
        'faturamento_id',
        'valor',
        'favorecido',
        'linha_digitavel',
        'vencimento',
        'arquivo_path',
        'status_auditoria',
    ];

    public function faturamento()
    {
        return $this->belongsTo('App\Models\Faturamento', 'faturamento_id');
    }

    public function comprovante()
    {
        return $this->hasOne('App\Models\Comprovante', 'boleto_id');
    }
}
