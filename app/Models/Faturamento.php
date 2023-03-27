<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faturamento extends Model
{
    //
    protected $hidden = [
        'laravel_through_key'
    ];

    public function servicosFaturados()
    {
        return $this->hasMany('App\Models\FaturamentoServico','faturamento_id');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Models\Empresa','empresa_id');
    }

    public function servicos()
    {
        return $this->hasManyThrough('App\Models\Servico','App\Models\FaturamentoServico','faturamento_id','id');
    }

    public function dadosCastro()
    {
        return $this->belongsTo('App\Models\DadosCastro','dadosCastro_id');
    }

   

   
}
