<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjetoDossie;
use App\Models\Pendencia;
use App\Models\Servico;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DossieIAController extends Controller
{
    public function gerarDossie(Request $request, $servico_id)
    {
        Log::info("DossieIAController: Gerando Dossiê para o Serviço/Projeto ID: $servico_id");

        $servico = Servico::find($servico_id);
        if (!$servico) {
            return response()->json(['error' => 'Projeto não encontrado.'], 404);
        }

        $historicoExigencias = Pendencia::where('empresa_id', $servico->empresa_id)
            ->whereNotNull('resolucao')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->pluck('resolucao')
            ->implode(' | ');

        $contextoNormas = "Art 5º: É obrigatório apresentar planta baixa assinada.\nArt 8º: Extintores a cada 15 metros quadrados.";

        $prompt = "Você é um Especialista em Legalização.
        Baseado nas NORMAS OFICIAIS: '$contextoNormas'
        E no HISTÓRICO DA CASTRO: '$historicoExigencias'
        
        Gere um Checklist Preventivo com 3 tópicos críticos para evitar retrabalho neste novo projeto. 
        Formato de saída: JSON com { \"checklist\": [ {\"item\": \"...\", \"fonte\": \"VISA\"} ] }";

        $provider = env('AI_PROVIDER', 'openai');

        /*
        switch ($provider) {
            case 'gemini':
                // Requisição para Google Gemini Pro
                $respostaIA = Http::post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=' . env('GEMINI_API_KEY'), [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ])->json();
                break;
                
            case 'opencode':
                // Requisição para a API Opencode
                $respostaIA = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENCODE_API_KEY')
                ])->post('https://api.opencode.com/v1/chat/completions', [
                    'model' => 'opencode-model',
                    'messages' => [['role' => 'user', 'content' => $prompt]]
                ])->json();
                break;
                
            case 'openai':
            default:
                // Requisição para OpenAI
                $respostaIA = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o',
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [['role' => 'user', 'content' => $prompt]]
                ])->json();
                break;
        }
        */

        $checklistSimulado = json_encode([
            "checklist" => [
                ["item" => "Validar planta baixa (Usando Provedor: $provider)", "fonte" => "VISA (Art 5º)"],
                ["item" => "Checar posicionamento dos extintores", "fonte" => "Bombeiros (Art 8º)"],
                ["item" => "Garantir documentação do responsável técnico", "fonte" => "Experiência Castro (Histórico)"]
            ]
        ]);

        $dossie = ProjetoDossie::updateOrCreate(
            ['projeto_id' => $servico_id],
            [
                'checklist_gerado' => $checklistSimulado,
                'historico_utilizado' => $historicoExigencias,
                'status' => 'concluido'
            ]
        );

        return response()->json([
            'mensagem' => 'Dossiê Inteligente gerado com sucesso.',
            'provedor' => $provider,
            'dossie_id' => $dossie->id,
            'checklist' => json_decode($dossie->checklist_gerado, true)
        ]);
    }
}
