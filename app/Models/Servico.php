<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    //
    protected $hidden = [
        'laravel_through_key'
    ];

    protected $casts = [
        'ativar_notificacao_renovacao' => 'boolean',
        'dias_para_notificacao_renovacao' => 'integer',
        'notificacao_renovacao_enviada_at' => 'datetime',
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
    	$query = $this->hasMany('App\Models\Historico')->orderBy('created_at','desc')->where('observacoes','not like',"%alterado %")->where('observacoes','not like',"Alterou %")->where('observacoes','not like',"%cadastrada.%")->where('observacoes','not like',"%cadastrado.%")->where('observacoes','not like','@%');
        if (\Auth::check() && \Auth::user()->privileges === 'cliente') {
            $query->where('visibilidade', '!=', 'interno');
        }
        return $query;
    }
    public function ultimasInteracoes()
    {
    	$query = $this->hasMany('App\Models\Historico')->orderBy('created_at','desc')->where('observacoes','not like',"%alterado %")->where('observacoes','not like',"%Concluiu%")->where('observacoes','not like',"Alterou %")->where('observacoes','not like',"%cadastrado.%")->where('observacoes','not like','@%');
        if (\Auth::check() && \Auth::user()->privileges === 'cliente') {
            $query->where('visibilidade', '!=', 'interno');
        }
        return $query->take(5);
    }

    public function interacoes()
    {
    	$query = $this->hasMany('App\Models\Historico')->orderBy('created_at','desc')->where('observacoes','like',"@%")->where('observacoes','not like',"%Concluiu%")->where('observacoes','not like',"Alterou%");
        if (\Auth::check() && \Auth::user()->privileges === 'cliente') {
            $query->where('visibilidade', '!=', 'interno');
        }
        return $query;
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
    public function analista1()
    {
        return $this->belongsTo('App\User','analista1_id','id');
    }
    
    public function analista2()
    {
        return $this->belongsTo('App\User','analista2_id','id');
    }

    public function membrosEquipe()
    {
        return $this->hasMany('App\Models\ServicoEquipe', 'servico_id');
    }

    public function membrosEquipeAtivos()
    {
        return $this->membrosEquipe()->where('ativo', true);
    }

    public function coordenadores()
    {
        return $this->belongsToMany('App\User', 'servico_equipe', 'servico_id', 'user_id')
            ->wherePivot('papel', 'coordenador')
            ->wherePivot('ativo', true);
    }

    public function responsaveisTecnicos()
    {
        return $this->belongsToMany('App\User', 'servico_equipe', 'servico_id', 'user_id')
            ->wherePivot('papel', 'responsavel_tecnico')
            ->wherePivot('ativo', true);
    }

    public function analistasEquipe()
    {
        return $this->belongsToMany('App\User', 'servico_equipe', 'servico_id', 'user_id')
            ->wherePivot('papel', 'analista')
            ->wherePivot('ativo', true);
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

    public function ordensServico()
    {
        return $this->hasMany('App\Models\OrdemServico','servico_id');
    }

    public function scopePorUsuario($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('responsavel_id', $userId)
              ->orWhere('coresponsavel_id', $userId)
              ->orWhere('analista1_id', $userId)
              ->orWhere('analista2_id', $userId)
              ->orWhereHas('membrosEquipeAtivos', function($sq) use ($userId) {
                  $sq->where('user_id', $userId);
              });
        });
    }

    public function analiseProtocoloLaudos()
    {
        return $this->hasMany('App\Models\AnaliseProtocoloLaudo', 'servico_id')->orderBy('created_at', 'desc');
    }

    public function cicloAtivo()
    {
        return $this->analiseProtocoloLaudos()->where('status', 'em_andamento')->first();
    }

}

