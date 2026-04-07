<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendencia;
use Auth;

class RelatoriosController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function pendenciasAtivas(Request $request)
    {
        $status = $request->get('status', 'pendente');

        $pendencias = Pendencia::with(['servico.unidade.empresa', 'responsavel'])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('vencimento', 'asc')
            ->paginate(50);

        return view('relatorios.pendencias_ativas', [
            'pendencias' => $pendencias,
            'status' => $status,
            'title' => 'Relatório de Pendências Ativas'
        ]);
    }
}
