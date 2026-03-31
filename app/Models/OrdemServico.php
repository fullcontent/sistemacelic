<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemServico extends Model
{
    public function servicoPrincipal()
    {
        return $this->belongsTo('App\Models\Servico','servico_id');
    }
    
    public function pagamentos()
    {    
        return $this->hasMany('App\Models\OrdemServicoPagamento','ordemServico_id');
    }

    public function situacaoPagamento()
    {
       $s = $this->hasMany('App\Models\OrdemServicoPagamento','ordemServico_id')->where('situacao','aberto');
       return $s;
    }

    public function prestador()
    {
        return $this->belongsTo('App\Models\Prestador');
    }
    
    public function rating()
    {
        return $this->hasMany('App\Models\PrestadorComentario','ordemServico_id');
    }
    public function vinculos()
    {
        return $this->hasMany('App\Models\OrdemServicoVinculo', 'ordemServico_id');
    }

}