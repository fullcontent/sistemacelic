<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faturamento;
use App\Models\Servico;
use App\Models\Empresa;
use App\Models\Unidade;
use App\Models\Historico;
use App\Models\Pendencia;
use DB;

class LandingPageController extends Controller
{
    public function index()
    {
        $startYear = 2019;

        // Real Metrics from Database (Filtered >= 2019)
        $totalEmpresas = Empresa::whereYear('created_at', '>=', $startYear)->count();
        $totalServicos = Servico::count();
        $totalLicencas = Servico::whereNotNull('licenca_emissao')->count();
        $totalUnidades = Unidade::count();

        // Advanced Operational Metrics
        $avgDays = Servico::where('situacao', 'finalizado')
            ->whereNotNull('dataFinal')
            ->selectRaw('AVG(DATEDIFF(dataFinal, created_at)) as avg_days')
            ->first()->avg_days ?? 86;

        $historicoCount = Historico::count();
        $activeProcesses = Servico::where('situacao', 'andamento')->count();
        $pendenciaCount = Pendencia::count();

        // National Coverage & Digital Maturity
        $cityCount = Unidade::distinct('cidade')->count();
        $stateCount = Unidade::distinct('uf')->count();
        $withDocsCount = Servico::whereNotNull('licenca_anexo')
            ->orWhereNotNull('protocolo_anexo')
            ->count();
        $maturityRate = ($totalServicos > 0) ? ($withDocsCount / $totalServicos) * 100 : 0;

        // BI: Duration per Service Type
        $avgTimePerType = Servico::where('situacao', 'finalizado')
            ->whereNotNull('dataFinal')
            ->selectRaw('tipo, count(*) as total, AVG(DATEDIFF(dataFinal, created_at)) as avg_days')
            ->groupBy('tipo')
            ->get();

        // Team Productivity Ranking
        $teamRanking = Historico::selectRaw('user_id, count(*) as total')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->with('user')
            ->take(6)
            ->get();

        // Unit Locations for the Map
        $unidadesMapa = Unidade::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('nomeFantasia', 'latitude', 'longitude', 'cidade', 'uf')
            ->get();

        // Yearly Process Evolution ( >= 2019 )
        $processosPorAno = Servico::selectRaw("YEAR(created_at) as year, count(*) as total")
            ->whereYear('created_at', '>=', $startYear)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $servicosDesde2019 = Servico::whereYear('created_at', '>=', $startYear)->count();
        $mediaMensal = (count($processosPorAno) > 0) ? $servicosDesde2019 / (count($processosPorAno) * 12) : 0;

        // Regional Concentration (by UF)
        $concentracaoRegional = Unidade::selectRaw('uf, count(*) as total')
            ->groupBy('uf')
            ->orderBy('total', 'desc')
            ->get();

        // Managed Area Impact
        $areaTotal = Unidade::sum('area');

        // Service Types Distribution
        $tiposServico = Servico::whereYear('created_at', '>=', $startYear)
            ->selectRaw('tipo, count(*) as total')
            ->groupBy('tipo')
            ->get();

        // Top Cities
        $topCidades = Unidade::selectRaw('cidade, count(*) as total')
            ->groupBy('cidade')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // Top Clients Ranking
        $topClientes = Servico::whereYear('created_at', '>=', $startYear)
            ->whereNotNull('empresa_id')
            ->selectRaw('empresa_id, count(*) as total')
            ->groupBy('empresa_id')
            ->orderBy('total', 'desc')
            ->with('empresa')
            ->take(5)
            ->get();

        // Logo Mapping
        $logosClientes = [
            18 => 'https://upload.wikimedia.org/wikipedia/commons/5/5a/Logo_Farm%C3%A1cias_Pague_Menos.png',
            59 => 'https://upload.wikimedia.org/wikipedia/commons/8/8f/Extrafarma_Logo.png',
            36 => 'https://equalweb.com.br/wp-content/uploads/2025/07/2024-07-15-BLOG-Cliente-1.png',
            55 => 'https://pwa.grupomadero.com.br/assets/logo.svg',
            17 => 'https://carreiras.grupofleury.com.br/wp-content/uploads/2023/06/GF-Logo-ver.png'
        ];

        return view('landing', compact(
            'totalEmpresas',
            'totalServicos',
            'totalLicencas',
            'totalUnidades',
            'unidadesMapa',
            'processosPorAno',
            'mediaMensal',
            'topClientes',
            'concentracaoRegional',
            'areaTotal',
            'tiposServico',
            'topCidades',
            'logosClientes',
            'avgDays',
            'historicoCount',
            'activeProcesses',
            'teamRanking',
            'avgTimePerType',
            'cityCount',
            'stateCount',
            'maturityRate',
            'pendenciaCount'
        ));
    }
}
