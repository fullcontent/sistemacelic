<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Taxa;
use App\Models\Pendencia;
use App\Models\Servico;
use Carbon\Carbon;

class WebhookCheckController extends Controller
{
    public function taxas()
    {
        $timezone = 'America/Sao_Paulo';
        $today = Carbon::now($timezone)->toDateString();
        $tomorrow = Carbon::now($timezone)->addDay()->toDateString();

        $taxasHoje = Taxa::with(['servico.unidade', 'servico.empresa', 'servico.responsavel'])
            ->whereDate('vencimento', $today)
            ->where('situacao', 'aberto')
            ->whereHas('servico', function ($query) {
                $query->where('situacao', 'andamento');
            })
            ->get()
            ->map(function ($taxa) {
                return [
                    'id' => $taxa->id,
                    'servico_id' => $taxa->servico_id,
                    'servico_nome' => $taxa->servico->nome ?? 'N/A',
                    'responsavel_id' => $taxa->servico->responsavel->id ?? null,
                    'responsavel_nome' => $taxa->servico->responsavel->name ?? 'N/A',
                    'responsavel_email' => $taxa->servico->responsavel->email ?? 'N/A',
                    'unidade' => $taxa->servico->unidade->nome ?? 'N/A',
                    'empresa' => $taxa->servico->empresa->nome ?? 'N/A',
                    'vencimento' => $taxa->vencimento,
                    'valor' => $taxa->valor,
                    'nome' => $taxa->nome,
                    'boleto' => $taxa->boleto
                ];
            });

        $taxasAmanha = Taxa::with(['servico.unidade', 'servico.empresa', 'servico.responsavel'])
            ->whereDate('vencimento', $tomorrow)
            ->where('situacao', 'aberto')
            ->whereHas('servico', function ($query) {
                $query->where('situacao', 'andamento');
            })
            ->get()
            ->map(function ($taxa) {
                return [
                    'id' => $taxa->id,
                    'servico_id' => $taxa->servico_id,
                    'servico_nome' => $taxa->servico->nome ?? 'N/A',
                    'responsavel_id' => $taxa->servico->responsavel->id ?? null,
                    'responsavel_nome' => $taxa->servico->responsavel->name ?? 'N/A',
                    'responsavel_email' => $taxa->servico->responsavel->email ?? 'N/A',
                    'unidade' => $taxa->servico->unidade->nome ?? 'N/A',
                    'empresa' => $taxa->servico->empresa->nome ?? 'N/A',
                    'vencimento' => $taxa->vencimento,
                    'valor' => $taxa->valor,
                    'nome' => $taxa->nome,
                    'boleto' => $taxa->boleto
                ];
            });

        $taxasAtrasadas = Taxa::with(['servico.unidade', 'servico.empresa', 'servico.responsavel'])
            ->whereDate('vencimento', '<', $today)
            ->where('situacao', 'aberto')
            ->whereHas('servico', function ($query) {
                $query->where('situacao', 'andamento');
            })
            ->get()
            ->map(function ($taxa) {
                return [
                    'id' => $taxa->id,
                    'servico_id' => $taxa->servico_id,
                    'servico_nome' => $taxa->servico->nome ?? 'N/A',
                    'responsavel_id' => $taxa->servico->responsavel->id ?? null,
                    'responsavel_nome' => $taxa->servico->responsavel->name ?? 'N/A',
                    'responsavel_email' => $taxa->servico->responsavel->email ?? 'N/A',
                    'unidade' => $taxa->servico->unidade->nome ?? 'N/A',
                    'empresa' => $taxa->servico->empresa->nome ?? 'N/A',
                    'vencimento' => $taxa->vencimento,
                    'valor' => $taxa->valor,
                    'nome' => $taxa->nome,
                    'boleto' => $taxa->boleto
                ];
            });

        return response()->json([
            'hoje' => $taxasHoje,
            'amanha' => $taxasAmanha,
            'atrasadas' => $taxasAtrasadas
        ]);
    }

    public function pendencias()
    {
        $timezone = 'America/Sao_Paulo';
        $today = Carbon::now($timezone)->toDateString();
        $tomorrow = Carbon::now($timezone)->addDay()->toDateString();

        $pendenciasHoje = Pendencia::with(['servico.unidade', 'servico.empresa', 'responsavel'])
            ->where('status', 'pendente')
            ->whereHas('servico', function ($query) {
                $query->where('situacao', 'andamento');
            })
            ->where(function ($query) use ($today) {
                $query->whereDate('dataLimite', $today)
                    ->orWhereDate('vencimento', $today);
            })
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'servico_id' => $p->servico_id,
                    'servico_nome' => $p->servico->nome ?? 'N/A',
                    'responsavel' => $p->responsavel->name ?? 'N/A',
                    'unidade' => $p->servico->unidade->nome ?? 'N/A',
                    'empresa' => $p->servico->empresa->nome ?? 'N/A',
                    'dataLimite' => $p->dataLimite ?? $p->vencimento,
                    'nome' => $p->pendencia ?? $p->pendencia
                ];
            });

        $pendenciasAmanha = Pendencia::with(['servico.unidade', 'servico.empresa', 'responsavel'])
            ->where('status', 'pendente')
            ->whereHas('servico', function ($query) {
                $query->where('situacao', 'andamento');
            })
            ->where(function ($query) use ($tomorrow) {
                $query->whereDate('dataLimite', $tomorrow)
                    ->orWhereDate('vencimento', $tomorrow);
            })
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'servico_id' => $p->servico_id,
                    'servico_nome' => $p->servico->nome ?? 'N/A',
                    'responsavel' => $p->responsavel->name ?? 'N/A',
                    'unidade' => $p->servico->unidade->nome ?? 'N/A',
                    'empresa' => $p->servico->empresa->nome ?? 'N/A',
                    'dataLimite' => $p->dataLimite ?? $p->vencimento,
                    'nome' => $p->pendencia ?? $p->pendencia
                ];
            });

        return response()->json([
            'hoje' => $pendenciasHoje,
            'amanha' => $pendenciasAmanha
        ]);
    }

    public function licencas()
    {
        return response()->json([
            '30_dias' => $this->getLicencasInDays(30),
            '60_dias' => $this->getLicencasInDays(60),
            '90_dias' => $this->getLicencasInDays(90),
            '120_dias' => $this->getLicencasInDays(120)
        ]);
    }

    // Mantendo index para compatibilidade caso necessário, ou removendo se preferir.
    // Vamos manter por enquanto retornando tudo.
    public function index()
    {
        return response()->json([
            'taxas' => json_decode($this->taxas()->content()),
            'pendencias' => json_decode($this->pendencias()->content()),
            'licencas' => json_decode($this->licencas()->content())
        ]);
    }

    private function getLicencasInDays($days)
    {
        $timezone = 'America/Sao_Paulo';
        $targetDate = Carbon::now($timezone)->addDays($days)->toDateString();

        return Servico::with(['unidade', 'empresa', 'responsavel'])
            ->where('situacao', 'finalizado')
            ->whereDate('licenca_validade', $targetDate)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'nome' => $s->nome,
                    'responsavel' => $s->responsavel->name ?? 'N/A',
                    'unidade' => $s->unidade->nome ?? 'N/A',
                    'empresa' => $s->empresa->nome ?? 'N/A',
                    'licenca_validade' => $s->licenca_validade
                ];
            });
    }
}
