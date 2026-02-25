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

        // Real Metrics from Database
        $totalEmpresas = Empresa::whereYear('created_at', '>=', $startYear)->count();
        $totalServicos = Servico::count();
        $totalLicencas = Servico::whereNotNull('licenca_emissao')
            ->orWhereNotNull('protocolo_anexo')
            ->count();
        $totalUnidades = Unidade::count();

        // Advanced Operational Metrics
        $avgDays = Servico::where('situacao', 'finalizado')
            ->whereNotNull('dataFinal')
            ->selectRaw('AVG(DATEDIFF(dataFinal, created_at)) as avg_days')
            ->first()->avg_days ?? 86;

        $historicoCount = Historico::count();
        $activeProcesses = Servico::where('situacao', 'andamento')->count();
        $pendenciaCount = Pendencia::count();

        // BI: Duration per Service Type (Start to Finish) - EXCLUDING projetosLaudos
        $avgTimePerType = Servico::where('situacao', 'finalizado')
            ->whereNotNull('dataFinal')
            ->where('tipo', '!=', 'projetosLaudos')
            ->selectRaw('tipo, count(*) as total, AVG(DATEDIFF(dataFinal, created_at)) as avg_days')
            ->groupBy('tipo')
            ->get();

        // BI: License Distribution (Variety of services handled)
        $licenseDistribution = Servico::where('tipo', '!=', 'projetosLaudos')
            ->whereNotNull('nome')
            ->selectRaw('nome, count(*) as total')
            ->groupBy('nome')
            ->orderBy('total', 'desc')
            ->take(6)
            ->get();

        // BI: Yearly Duration Evolution (Efficiency Trend) - EXCLUDING 2026
        $yearlyDuration = Servico::where('situacao', 'finalizado')
            ->whereNotNull('dataFinal')
            ->whereYear('created_at', '>=', $startYear)
            ->whereYear('created_at', '<', 2026)
            ->selectRaw('YEAR(created_at) as year, AVG(DATEDIFF(dataFinal, created_at)) as avg_days')
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();

        // Team Productivity Ranking
        $teamRanking = Historico::selectRaw('user_id, count(*) as total')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->with('user')
            ->take(6)
            ->get();

        // National Coverage & Digital Maturity
        $cityCount = Unidade::distinct('cidade')->count();
        $stateCount = Unidade::distinct('uf')->count();
        $withDocsCount = Servico::whereNotNull('licenca_anexo')
            ->orWhereNotNull('protocolo_anexo')
            ->count();
        $maturityRate = ($totalServicos > 0) ? ($withDocsCount / $totalServicos) * 100 : 0;

        // Units Growth Evolution (Running Total 2019-2026)
        $unitsPerYear = Unidade::selectRaw('YEAR(created_at) as year, count(*) as count')
            ->whereYear('created_at', '>=', $startYear)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $runningTotalUnits = 0;
        $unitsEvolution = [];
        $years = range(2019, 2025);
        foreach ($years as $year) {
            $count = $unitsPerYear->firstWhere('year', $year)->count ?? 0;
            $runningTotalUnits += $count;
            $unitsEvolution[] = [
                'year' => $year,
                'total' => $runningTotalUnits
            ];
        }

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

        // Regional Concentration (by UF)
        $concentracaoRegional = Unidade::selectRaw('uf, count(*) as total')
            ->groupBy('uf')
            ->orderBy('total', 'desc')
            ->get();

        // Managed Area Impact
        $areaTotal = Unidade::sum('area');

        // Top Cities
        $topCidades = Unidade::selectRaw('cidade, count(*) as total')
            ->groupBy('cidade')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // Top Clients Ranking (Joined via Units for accurate attribution)
        $topClientes = DB::table('servicos')
            ->join('unidades', 'servicos.unidade_id', '=', 'unidades.id')
            ->whereYear('servicos.created_at', '>=', $startYear)
            ->whereNotNull('unidades.empresa_id')
            ->selectRaw('unidades.empresa_id, count(*) as total')
            ->groupBy('unidades.empresa_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // Map empresa_id to Logo
        foreach ($topClientes as $cliente) {
            $cliente->empresa = Empresa::find($cliente->empresa_id);
        }

        // Logo Mapping
        $logosClientes = [
            18 => 'https://upload.wikimedia.org/wikipedia/commons/5/5a/Logo_Farm%C3%A1cias_Pague_Menos.png',
            1 => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cc/Burger_King_2020.svg/800px-Burger_King_2020.svg.png',
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
            'topClientes',
            'concentracaoRegional',
            'areaTotal',
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
            'pendenciaCount',
            'unitsEvolution',
            'licenseDistribution',
            'yearlyDuration'
        ));
    }
}
