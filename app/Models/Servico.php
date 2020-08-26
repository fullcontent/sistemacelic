<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    //
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

    public function taxas()
    {
        return $this->hasMany('App\Models\Taxa');
    }

    public function pendencias()
    {
        return $this->hasMany('App\Models\Pendencia');
    }
    
    public function responsavel()
    {
        return $this->belongsTo('App\User','responsavel_id','id');
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

    

}
