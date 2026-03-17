<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposta extends Model
{
    public function servicos()
        {
        	return $this->hasMany('App\Models\PropostaServico');
        }

    public function empresa()
    {
        return $this->belongsTo('App\Models\Empresa');
    }

    public function unidade()
    {
        return $this->belongsTo('App\Models\Unidade');
    }

    public function servicosFaturados()
    {
        return $this->hasManyThrough('App\Models\Servico','App\Models\PropostaServico','proposta_id','propostaServico_id')->whereHas('faturamento');
    }

    public function servicosCriados()
    {
        return $this->hasManyThrough('App\Models\Servico','App\Models\PropostaServico','proposta_id','propostaServico_id');
    }


    public function vendedor()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function solicitanteObject()
    {
        return $this->belongsTo('App\Models\Solicitante', 'solicitante');
    }

    public function getDiasEmAnaliseAttribute()
    {
        $startDate = $this->sent_to_analysis_at ?: $this->created_at;
        
        // Se ainda está em análise, calcula até hoje
        if ($this->status == 'Em análise') {
            return \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::now());
        }

        // Para outros status, precisamos da data de finalização
        $endDate = null;
        if ($this->status == 'Aprovada') {
            $endDate = $this->approved_at;
        } elseif ($this->status == 'Recusada') {
            $endDate = $this->refused_at;
        }
        
        // Fallback para updated_at se não tiver data específica de aprovação/recusa/faturamento/fim
        if (!$endDate) {
            $endDate = $this->updated_at;
        }

        return \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate));
    }

    public function getIsDataAproximadaAttribute()
    {
        // É aproximada se não tiver o marco inicial (sent_to_analysis_at) 
        // ou se não for 'Em análise' e não tiver o marco final (approved_at/refused_at)
        if ($this->status == 'Em análise') {
            return !$this->sent_to_analysis_at;
        }

        if ($this->status == 'Aprovada') {
            return !$this->approved_at;
        }

        if ($this->status == 'Recusada') {
            return !$this->refused_at;
        }

        return true; // Para outros status como Revisando/Arquivada sem datas específicas
    }

    public function valorTotal()
    {
        return $this->servicos->sum('valor');
    }

    public function dadosCastro()
    {
        return $this->belongsTo('App\Models\DadosCastro', 'dadosCastro_id');
    }
}
