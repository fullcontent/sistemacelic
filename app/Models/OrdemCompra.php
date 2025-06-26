<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemCompra extends Model
{
    public function servicoPrincipal()
    {
        return $this->belongsTo('App\Models\Servico','servico_id');
    }
    
    public function pagamentos()
    {    
        return $this->hasMany('App\Models\OrdemCompraPagamento','ordemCompra_id');
    }

    public function situacaoPagamento()
    {
       $s = $this->hasMany('App\Models\OrdemCompraPagamento','ordemCompra_id')->where('situacao','aberto');
       return $s;
    }

    public function prestador()
    {
        return $this->belongsTo('App\Models\Prestador');
    }
    
    public function rating()
    {
        return $this->hasMany('App\Models\PrestadorComentario','ordemCompra_id');
    }
    

}