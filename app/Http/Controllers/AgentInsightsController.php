<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servico;
use App\Models\Pendencia;
use App\Models\Taxa;
use App\Models\Empresa;
use Carbon\Carbon;

class AgentInsightsController extends Controller
{
    /**
     * Endpoint para gerar o resumo matinal (Daily Briefing) do Agente.
     * Retorna Serviços vencendo/vencidos, Pendências atrasadas e Taxas vencendo.
     */
    public function getDailyInsights(Request $request)
    {
        // Token super simples de segurança apenas para o agente (em prod seria melhor JWT ou similar)
        $token = $request->query('token');
        if ($token !== 'celic-agent-super-secret-2026') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $hoje = Carbon::now()->format('Y-m-d');
        $proximaSemana = Carbon::now()->addDays(7)->format('Y-m-d');

        // 1. Serviços Críticos (Vencendo ou Vencido)
        $servicosAlertas = Servico::with('empresa', 'unidade', 'responsavel')
            ->whereIn('situacao', ['vencendo', 'vencido'])
            ->get()
            ->map(function ($servico) {
                return [
                    'id' => $servico->id,
                    'nome' => $servico->nome,
                    'situacao' => $servico->situacao,
                    'empresa' => $servico->empresa ? $servico->empresa->nomeFantasia ?? $servico->empresa->razaoSocial : 'N/A',
                    'unidade' => $servico->unidade ? $servico->unidade->nomefantasia : 'N/A',
                    'responsavel' => $servico->responsavel ? $servico->responsavel->name : 'N/A',
                    'validade' => $servico->licenca_validade
                ];
            });

        // 2. Pendências Atrasadas (status = pendente e vencimento < hoje)
        $pendenciasAtrasadas = Pendencia::with('servico.empresa', 'responsavel')
            ->where('status', 'pendente')
            ->where('vencimento', '<', $hoje)
            ->get()
            ->map(function ($pendencia) {
                return [
                    'id' => $pendencia->id,
                    'pendencia' => $pendencia->pendencia,
                    'vencimento' => $pendencia->vencimento,
                    'responsavel' => $pendencia->responsavel ? $pendencia->responsavel->name : 'N/A',
                    'empresa' => ($pendencia->servico && $pendencia->servico->empresa) ? $pendencia->servico->empresa->razaoSocial : 'N/A',
                ];
            });

        // 3. Taxas Vencendo (status = aberto e vencimento <= 7 dias)
        $taxasAlertas = Taxa::with('servico.empresa')
            ->where('situacao', 'aberto')
            ->where('vencimento', '<=', $proximaSemana)
            ->get()
            ->map(function ($taxa) {
                return [
                    'id' => $taxa->id,
                    'nome' => $taxa->nome,
                    'valor' => $taxa->valor,
                    'vencimento' => $taxa->vencimento,
                    'empresa' => ($taxa->servico && $taxa->servico->empresa) ? $taxa->servico->empresa->razaoSocial : 'N/A',
                ];
            });

        return response()->json([
            'gerado_em' => Carbon::now()->toDateTimeString(),
            'servicos_criticos' => $servicosAlertas,
            'pendencias_atrasadas' => $pendenciasAtrasadas,
            'taxas_vencendo' => $taxasAlertas
        ]);
    }

    /**
     * Endpoint interativo: Resumo de um cliente específico.
     */
    public function getClienteResumo(Request $request, $empresa_id)
    {
        $token = $request->query('token');
        if ($token !== 'celic-agent-super-secret-2026') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $empresa = Empresa::with('unidades')->find($empresa_id);
        
        if (!$empresa) {
            return response()->json(['error' => 'Empresa não encontrada'], 404);
        }

        $servicos = Servico::where('empresa_id', $empresa_id)->get();
        
        $vigentes = $servicos->where('situacao', 'vigente')->count();
        $vencidos = $servicos->where('situacao', 'vencido')->count();
        $emAndamento = $servicos->where('situacao', 'andamento')->count();

        return response()->json([
            'cliente' => $empresa->razaoSocial,
            'unidades_count' => $empresa->unidades->count(),
            'resumo_servicos' => [
                'vigentes' => $vigentes,
                'vencidos' => $vencidos,
                'em_andamento' => $emAndamento,
                'total' => $servicos->count()
            ],
            // Poderíamos adicionar mais detalhes se necessário
        ]);
    }
}
