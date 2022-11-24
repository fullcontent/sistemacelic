<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    //
    protected $hidden = [
        'laravel_through_key'
    ];


    public function unidade()
    {
    	return $this->belongsTo('App\Models\Unidade','unidade_id');
    }

    public function empresa()
    {
    	return $this->belongsTo('App\Models\Empresa','empresa_id');
    }
    
    public function historico()
    {
    	return $this->hasMany('App\Models\Historico')->orderBy('created_at','desc')->where('observacoes','not like',"%Alterou%")->take(5);
    }
    public function ultimasInteracoes()
    {
    	return $this->hasMany('App\Models\Historico')->orderBy('created_at','desc')->where('observacoes','not like',"%alterado%")->where('observacoes','not like',"%Concluiu%")->where('observacoes','not like',"%cadastrado%")->where('observacoes','not like','%@%')->take(5);
    }

    public function interacoes()
    {
    	return $this->hasMany('App\Models\Historico')->orderBy('created_at','desc')->where('observacoes','like',"%@%")->where('observacoes','not like',"%alterado%")->where('observacoes','not like',"%Concluiu%")->where('observacoes','not like',"%cadastrado%");
    }

    public function taxas()
    {
        return $this->hasMany('App\Models\Taxa');
    }

    public function reembolsos()
    {
        return $this->hasMany('App\Models\Taxa')->where('reembolso','sim');
    }
    

    public function pendencias()
    {
        return $this->hasMany('App\Models\Pendencia');
    }
    
    public function responsavel()
    {
        return $this->belongsTo('App\User','responsavel_id','id');
    }

    public function coresponsavel()
    {
        return $this->belongsTo('App\User','coresponsavel_id','id');
    }

    public function arquivos()
        {
            return $this->hasMany('App\Models\Arquivo');
        }

    public function servicoLpu()
    {
        return $this->belongsTo('App\Models\ServicoLpu','servico_lpu');
    }

    public function financeiro()
    {
        return $this->hasOne('App\Models\ServicoFinanceiro','servico_id');
    }
    
    public function finalizado()
    {
    	return $this->hasOne('App\Models\Historico')->where('observacoes','like','Alterou situacao para "finalizado"');
    }
    


    public function servicoFinalizado()
    {
    	return $this->hasOne('App\Models\ServicoFinalizado');
    }


    public function subServicos()
    {
        return $this->hasMany('App\Models\Servico','servicoPrincipal');
    }

    
    public function servicoPrincipal()
    {
        return $this->belongsTo('App\Models\Servico', 'servicoPrincipal');
    }

    public function vinculo()
    {
        return $this->hasMany('App\Models\Pendencia', 'vinculo');
    }

    public function vinculos()
    {
        return $this->hasMany('App\Models\PendenciasVinculos');
    }

    public function faturamento()
    {
        return $this->hasOneThrough(
            Faturamento::class,
            FaturamentoServico::class,
            'servico_id', // Foreign key on the faturamentoServico table...
            'id', // Foreign key on the faturamento table...
            'id', // Local key on the Servico table...
            'faturamento_id' // Local key on the FaturamentoServico table...
        );
    }

    public function faturado()
    {
        return $this->hasOne('App\Models\FaturamentoServico');
    }

    public function proposta()
    {
        return $this->belongsTo('App\Models\Proposta', 'proposta_id');
    }

    public function solicitanteServico()
    {
        return $this->hasOne('App\Models\Solicitante','id','solicitante');
    }

    

}
