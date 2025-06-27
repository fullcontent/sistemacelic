<?php
namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Pendencia;
use App\Models\Servico;
use App\Models\ServicoFinanceiro;
use App\Models\Unidade;
use Auth;
use File;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //

    public function __construct()
    {

        $this->middleware('admin');

    }

    public function index()
    {

        // dd($this->pendencias()->id);

        // return $this->pendencias();

        return view('admin.dashboard')
            ->with([
                'vencer'                 => $this->servicosVencer(),
                'finalizados'            => $this->servicosFinalizados(),
                'andamento'              => $this->servicosAndamento(),
                'andamentoCoResponsavel' => $this->servicosAndamentoCoResponsavel(),
                'pendencias'             => $this->pendencias(),

            ]);
    }

    public function pendencias()
    {

        $servicos = Servico::select('id')->where('responsavel_id', Auth::id())->get();

        $pendencias = Pendencia::with('servico', 'unidade')
            ->where('responsavel_id', Auth::id())
        // ->orWhereIn('pendencias.servico_id',$servicos)
            ->where('status', 'pendente')
            ->whereDoesntHave('vinculos')
            ->get();

        return $pendencias;
    }

    public function servicosVencer()
    {
        $servicos = Servico::with('unidade', 'empresa', 'responsavel')
        // ->whereIn('unidade_id',$this->getUnidadesList())
            ->orWhere('responsavel_id', Auth::id())
            ->get();

        $servicos = $servicos->where('licenca_validade', '<', \Carbon\Carbon::today()->addDays(60))

            ->where('situacao', '=', 'finalizado')
            ->where('tipo', '=', 'licencaOperacao');

        return $servicos;
    }

    public function servicosFinalizados()
    {
        $servicos = Servico::with('unidade', 'empresa', 'responsavel')

        // ->whereIn('unidade_id',$this->getUnidadesList())
            ->orWhere('responsavel_id', Auth::id())
            ->get();

        $servicos = $servicos->where('situacao', '=', 'finalizado')

            ->where('situacao', '<>', 'arquivado');

        return $servicos;
    }

    public function servicosAndamento()
    {
        $servicos = Servico::with('unidade', 'empresa', 'responsavel')

        // ->whereIn('unidade_id',$this->getUnidadesList())
            ->orWhere('responsavel_id', Auth::id())
            ->get();

        $servicos = $servicos->where('situacao', '=', 'andamento')

            ->where('situacao', '<>', 'arquivado');

        return $servicos;
    }

    public function servicosAndamentoCoResponsavel()
    {
        $servicos = Servico::with('unidade', 'empresa', 'responsavel')

        // ->whereIn('unidade_id',$this->getUnidadesList())
            ->orWhere('coresponsavel_id', Auth::id())
            ->get();

        $servicos = $servicos->where('situacao', '=', 'andamento')

            ->where('situacao', '<>', 'arquivado');

        return $servicos;
    }

    public function relatorioCompleto()
    {

        $servicos = Servico::with('unidade', 'responsavel', 'financeiro', 'servicoFinalizado', 'historico')
            ->whereNotIn('responsavel_id', [1])
            ->take(4000)
            ->get();

        dump($servicos);

        return view('admin.relatorios.completo')->with(['servicos' => $servicos]);

    }

    public function completoCSV()
    {
        $fileName = 'Celic_RelatorioCompleto_Servicos' . date('d-m-Y') . '.csv';

        // Definindo os cabeçalhos da resposta
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        // Colunas do CSV
        $columns = [
            'ID', 'Razão Social', 'Código', 'Nome', 'Licenciamento', 'CNPJ', 'Status', 'Imóvel', 'Ins. Estadual',
            'Ins. Municipal', 'Ins. Imob.', 'RIP', 'Matrícula RI', 'Área da Loja', 'Área do Terreno', 'Endereço',
            'Número', 'Complemento', 'Bairro', 'Data Inauguração', 'Cidade', 'UF', 'CEP', 'Tipo', 'O.S.', 'Situação',
            'Responsável', 'Co-Responsável', 'Analista 1', 'Analista 2', 'Nome', 'Solicitante', 'Departamento',
            'N° Protocolo', 'Emissão Protocolo', 'Tipo Licença', 'Proposta', 'Emissão Licença', 'Validade Licença',
            'Valor Total', 'Valor em Aberto', 'Finalizado', 'Criação', 'Pendência(s)', 'Responsável(eis) pela(s) Pendência(s)',
        ];

        // Callback para gerar o CSV
        $callback = function () use ($columns) {
            // Abrindo o arquivo de saída
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Usando cursor() para fazer o lazy loading dos dados
            Servico::with([
                'unidade:id,razaoSocial,codigo,nomeFantasia,cnpj,status,tipoImovel,inscricaoEst,inscricaoMun,inscricaoImo,rip,matriculaRI,area,areaTerreno,endereco,numero,complemento,bairro,cidade,uf,cep,dataInauguracao',
                'responsavel:id,name',
                'coresponsavel:id,name',
                'financeiro:id,valorTotal,valorAberto',
                'servicoFinalizado:id,finalizado',
                'vinculos:id',
                'historico:id',
            ])->whereNotIn('responsavel_id', [1])
                ->cursor() // Lazy loading para evitar problemas de memória
                ->each(function ($s) use ($file) {
                    // Processando cada serviço para incluir no CSV
                    $solicitante = is_numeric($s->solicitante)
                    ? \App\Models\Solicitante::where('id', $s->solicitante)->value('nome')
                    : $s->solicitante;

                    $proposta          = $s->proposta_id ?: $s->proposta;
                    $finalizado        = $s->servicoFinalizado ? \Carbon\Carbon::parse($s->servicoFinalizado->finalizado)->format('d/m/Y') : '';
                    $protocolo_emissao = $s->protocolo_emissao ? \Carbon\Carbon::parse($s->protocolo_emissao)->format('d/m/Y') : null;
                    $licenca_emissao   = $s->licenca_emissao ? \Carbon\Carbon::parse($s->licenca_emissao)->format('d/m/Y') : null;
                    $licenca_validade  = $s->licenca_validade ? \Carbon\Carbon::parse($s->licenca_validade)->format('d/m/Y') : null;
                    $dataInauguracao   = $s->unidade->dataInauguracao ? \Carbon\Carbon::parse($s->unidade->dataInauguracao)->format('d/m/Y') : null;
                    $licenciamento     = $s->licenciamento ?: null;

                    // Processamento de pendências e responsáveis
                    $pendencias           = null;
                    $responsavelPendencia = null;

                    if ($s->pendencias) {
                        foreach ($s->pendencias as $p) {
                            $pendencias           = $p->pendencia;
                            $responsavelPendencia = $p->responsavel->name ?? '';
                        }
                    }

                    // Escrevendo cada linha no arquivo CSV
                    fputcsv($file, [
                        $s->id,
                        $s->unidade->razaoSocial,
                        $s->unidade->codigo,
                        $s->unidade->nomeFantasia,
                        $licenciamento,
                        $s->unidade->cnpj,
                        $s->unidade->status,
                        $s->unidade->tipoImovel,
                        $s->unidade->inscricaoEst,
                        $s->unidade->inscricaoMun,
                        $s->unidade->inscricaoImo,
                        $s->unidade->rip,
                        $s->unidade->matriculaRI,
                        $s->unidade->area,
                        $s->unidade->areaTerreno,
                        $s->unidade->endereco,
                        $s->unidade->numero,
                        $s->unidade->complemento,
                        $s->unidade->bairro,
                        $dataInauguracao,
                        $s->unidade->cidade,
                        $s->unidade->uf,
                        $s->unidade->cep,
                        $s->tipo,
                        $s->os,
                        $s->situacao,
                        $s->responsavel->name,
                        $s->coresponsavel->name ?? '',
                        $s->analista1->name ?? '',
                        $s->analista2->name ?? '',
                        $s->nome,
                        $solicitante,
                        $s->departamento,
                        $s->protocolo_numero,
                        $protocolo_emissao,
                        $s->tipoLicenca,
                        $proposta,
                        $licenca_emissao,
                        $licenca_validade,
                        $s->financeiro->valorTotal ?? '0',
                        $s->financeiro->valorAberto ?? '0',
                        $finalizado,
                        \Carbon\Carbon::parse($s->created_at)->format('d/m/Y'),
                        $pendencias,
                        $responsavelPendencia,
                    ]);
                });

            // Fechando o arquivo de saída
            fclose($file);
        };

        // Retornando a resposta de streaming do CSV
        return response()->stream($callback, 200, $headers);
    }

    public function gerarRelatorioCompletoCSV()
    {
        $servicos = Servico::with('unidade', 'responsavel', 'financeiro', 'servicoFinalizado', 'vinculos', 'historico')
            ->whereNotIn('responsavel_id', [1])
            ->get();

        // Nome do arquivo CSV
        // $fileName = 'servicos_' . date('Ymd_His') . '.csv';
        $fileName = 'Celic_RelatorioCompleto_Servicos ' . date('d-m-Y | H:m') . '.csv';
        $filePath = public_path('uploads/relatorios/' . $fileName); // Salvar na pasta uploads/relatorios

        // Verifica se a pasta existe, se não, cria a pasta
        if (! file_exists(public_path('uploads/relatorios'))) {
            mkdir(public_path('uploads/relatorios'), 0755, true);
        }

        // Abre o arquivo para escrita
        $file = fopen($filePath, 'w');

        // Cabeçalho do CSV
        $header = ['ServicoID', 'Razão Social', 'Código', 'Nome', 'CNPJ', 'Status', 'Imóvel', 'Ins. Estadual', 'Ins. Municipal', 'Ins. Imob.', 'RIP', 'Matrícula RI', 'Área da Loja', 'Área do Terreno', 'Endereço', 'Número', 'Bairro', 'Complemento', 'Data Inauguração', 'Cidade', 'UF', 'CEP', 'Tipo', 'O.S.', 'Situação', 'Responsável', 'Co-Responsável', 'Nome', 'Solicitante', 'Departamento', 'Licenciamento', 'N° Protocolo', 'Emissão Protocolo', 'Tipo Licença', 'Proposta', 'Emissão Licença', 'Validade Licença', 'Valor Total', 'Valor em Aberto', 'Finalizado', 'Criação'];

        // Escreve o cabeçalho no CSV
        fputcsv($file, $header);

        // Adiciona os dados ao CSV
        foreach ($servicos as $s) {
            if (is_numeric($s->solicitante)) {
                $s->solicitante = \App\Models\Solicitante::where('id', $s->solicitante)->value('nome');
            }

            $proposta          = $s->proposta_id ? $s->proposta_id : $s->proposta;
            $finalizado        = isset($s->servicoFinalizado) ? \Carbon\Carbon::parse($s->servicoFinalizado->finalizado)->format('d/m/Y') : '';
            $protocolo_emissao = isset($s->protocolo_emissao) ? \Carbon\Carbon::parse($s->protocolo_emissao)->format('d/m/Y') : null;
            $licenca_emissao   = isset($s->licenca_emissao) ? \Carbon\Carbon::parse($s->licenca_emissao)->format('d/m/Y') : null;
            $licenca_validade  = isset($s->licenca_validade) ? \Carbon\Carbon::parse($s->licenca_validade)->format('d/m/Y') : null;
            $dataInauguracao   = $s->unidade->dataInauguracao ? \Carbon\Carbon::parse($s->unidade->dataInauguracao)->format('d/m/Y') : null;

            // Escreve os dados do serviço no CSV
            fputcsv($file, [
                $s->id,
                $s->unidade->razaoSocial,
                $s->unidade->codigo,
                $s->unidade->nomeFantasia,
                $s->unidade->cnpj,
                $s->unidade->status,
                $s->unidade->tipoImovel,
                $s->unidade->inscricaoEst,
                $s->unidade->inscricaoMun,
                $s->unidade->inscricaoImo,
                $s->unidade->rip,
                $s->unidade->matriculaRI,
                $s->unidade->area,
                $s->unidade->areaTerreno,
                $s->unidade->endereco,
                $s->unidade->numero,
                $s->unidade->bairro,
                $s->unidade->complemento,
                $dataInauguracao,
                $s->unidade->cidade,
                $s->unidade->uf,
                $s->unidade->cep,
                $s->tipo,
                $s->os,
                $s->situacao,
                $s->responsavel->name,
                $s->coresponsavel->name ?? '',
                $s->nome,
                $s->solicitante,
                $s->departamento,
                $s->licenciamento ?? '',
                $s->protocolo_numero,
                $protocolo_emissao,
                $s->tipoLicenca,
                $proposta,
                $licenca_emissao,
                $licenca_validade,
                $s->financeiro->valorTotal ?? '0',
                $s->financeiro->valorAberto ?? '0',
                $finalizado,
                \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') ?? '',
            ]);
        }

        // Fecha o arquivo
        fclose($file);
        session()->flash('success', 'Relatório gerado com sucesso!');
        // Retorna o caminho do arquivo criado
        return redirect()->back();
    }

    public function completoJSON()
    {
        $servicos = Servico::with('unidade', 'responsavel', 'financeiro', 'servicoFinalizado', 'vinculos', 'historico')
            ->whereNotIn('responsavel_id', [1])
        // ->take(1000)
            ->get();

        $resultArray = [];

        foreach ($servicos as $s) {
            if (is_numeric($s->solicitante)) {
                $s->solicitante = \App\Models\Solicitante::where('id', $s->solicitante)->value('nome');
            }

            // Your existing code to process and format data goes here
            if ($s->proposta_id) {
                $proposta = $s->proposta_id;
            } else {
                $proposta = $s->proposta;
            }

            if (isset($s->servicoFinalizado)) {
                $finalizado = \Carbon\Carbon::parse($s->servicoFinalizado->finalizado)->format('d/m/Y');
            } else {
                $finalizado = '';
            }

            if (isset($s->protocolo_emissao)) {
                $protocolo_emissao = \Carbon\Carbon::parse($s->protocolo_emissao)->format('d/m/Y');
            } else {
                $protocolo_emissao = null;
            }

            if (isset($s->licenca_emissao)) {
                $licenca_emissao = \Carbon\Carbon::parse($s->licenca_emissao)->format('d/m/Y');
            } else {
                $licenca_emissao = null;
            }
            if (isset($s->licenca_validade)) {
                $licenca_validade = \Carbon\Carbon::parse($s->licenca_validade)->format('d/m/Y');
            } else {
                $licenca_validade = null;
            }

            if ($s->unidade->dataInaugurcao) {
                $dataInauguracao = \Carbon\Carbon::parse($s->unidade->dataInauguracao)->format('d/m/Y');
            } else {
                $dataInauguracao = null;
            }

            if ($s->licenciamento) {
                $licenciamento = $s->licenciamento;
            } else {
                $licenciamento = null;
            }

            if ($s->pendencias) {

                $pendencias           = null;
                $responsavelPendencia = null;

                foreach ($s->pendencias as $p) {
                    $pendencias = $p->pendencia;

                    if (isset($p->responsavel->name)) {
                        $responsavelPendencia = $p->responsavel->name;
                    }

                }

            }
            // ...

            $resultArray[] = [
                'ID'                                    => $s->id,
                'Razão Social'                          => $s->unidade->razaoSocial,
                // ... Other fields ...
                'Pendência(s)'                          => $pendencias,
                'Responsável(eis) pela(s) Pendência(s)' => $responsavelPendencia,
                // ... More fields ...
            ];
        }

        // Convert the result array to JSON
        $jsonResult = json_encode($resultArray, JSON_PRETTY_PRINT);

        // Set headers for JSON response
        $headers = [
            'Content-Type' => 'application/json',
        ];

        return response($jsonResult, 200, $headers);
    }

    public function taxasCSV()
    {

        $fileName = 'Celic_RelatorioCompleto_Taxas' . date('d-m-Y') . '.csv';

        $servicos = Servico::with('unidade', 'financeiro')->whereNotIn('responsavel_id', [1])->orderBy('id', 'DESC')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $columns = ['Empresa', 'Serviço', 'OS', 'ID', 'Código', 'Unidade', 'CNPJ', 'Cidade', 'UF', 'Proposta',
            'Valor Total', 'Taxa', 'Emissão', 'Vencimento', 'Pagamento', 'Resp. Pgto', 'Reembolso', 'Status', 'Valor'];

        $callback = function () use ($servicos, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($servicos as $s) {

                foreach ($s->taxas as $t) {

                    $cidadeUF = $s->unidade->cidade . "/" . $s->unidade->uf;

                    if ($s->proposta_id) {
                        $proposta = $s->proposta_id;
                    } else {
                        $proposta = $s->proposta;
                    }

                    if ($s->unidade->dataInauguraao) {
                        $dataInauguracao = \Carbon\Carbon::parse($s->unidade->dataInauguracao)->format('d/m/Y');
                    } else {
                        $dataInauguracao = null;
                    }

                    fputcsv($file, [
                        $s->unidade->empresa->nomeFantasia,
                        $s->nome,
                        $s->os,
                        $s->id,
                        $s->unidade->codigo,
                        $s->unidade->nomeFantasia,
                        $s->unidade->cnpj,
                        $s->unidade->cidade,
                        $s->unidade->uf,
                        $proposta,
                        $s->financeiro->valorTotal ?? '',
                        $t->nome,
                        \Carbon\Carbon::parse($t->emissao)->translatedFormat('d-M-Y'),
                        \Carbon\Carbon::parse($t->vencimento)->translatedFormat('d-M-Y'),
                        \Carbon\Carbon::parse($t->pagamento)->translatedFormat('d-M-Y'),
                        $t->responsavelPgto,
                        $t->reembolso,
                        $t->situacao,
                        number_format($t->valor, 2, ",", "."),

                    ]);
                }

            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    }

    public function pendenciasCSV()
    {

        $fileName = 'Celic_RelatorioCompleto_Pendencias' . date('d-m-Y') . '.csv';

        $servicos = Servico::with('pendencias')
            ->whereNotIn('responsavel_id', [1])
            ->orderBy('id', 'DESC')
            ->with('responsavel', 'coresponsavel', 'financeiro', 'historico')
            ->select('id', 'nome', 'os', 'unidade_id', 'tipo', 'protocolo_anexo', 'laudo_anexo', 'solicitante', 'responsavel_id', 'coresponsavel_id', 'licenciamento', 'departamento', 'situacao', 'created_at', 'dataFinal') // Add 'situacao' and 'created_at' to the select list
            // ->take(30)
            ->get();

        // $servicos = Pendencia::all();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $columns = [
            'id',
            'Etapa',
            'Empresa',
            'Serviço',
            'Licenciamento',
            'Código',
            'Unidade',
            'CNPJ',
            'Cidade',
            'UF',
            'Status da Unidade',
            'Data Inauguração',
            'OS',
            'Situação', // Add 'Situação' column
            'Tipo',
            'Valor Serviço',
            'Solicitante',
            'Departamento', // Add 'Departamento' column
            'Responsável',
            'Co-Responsável',
            'Analista 1',
            'Analista 2',
            'Etapa do Processo',
            'Pendência',
            'Responsável Pendência',
            'Responsabilidade',
            'Data Criação',
            'Data Inicio',
            'Data Limite',
            'Data Final Serviço',
            'Data Limite Ciclo',
            'Status',
            'Vinculo',
            'ServicoID',
            'Criação serviço', // Add 'Criado em' column
            'Historico',

        ];

        $callback = function () use ($servicos, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($servicos as $s) {

                foreach ($s->pendencias as $p) {

                    $cidadeUF = $s->unidade->cidade . "/" . $s->unidade->uf;

                    if ($s->proposta_id) {
                        $proposta = $s->proposta_id;
                    } else {
                        $proposta = $s->proposta;
                    }

                    if ($s->unidade->dataInauguraao) {
                        $dataInauguracao = \Carbon\Carbon::parse($s->unidade->dataInauguracao)->format('d/m/Y');
                    } else {
                        $dataInauguracao = null;
                    }

                    switch ($p->responsavel_tipo) {

                        case 'usuario':
                            $p->responsavel_tipo = "Castro";
                            break;

                        case 'op':
                            $p->responsavel_tipo = "Órgão";
                            break;

                        case 'cliente':
                            $p->responsavel_tipo = "Cliente";
                            break;

                    }

                    switch ($p->status) {
                        case 'pendente':
                            $p->status = "Pendente";
                            break;

                        case 'concluido':
                            $p->status = "Concluído";
                            break;

                    }

                    switch ($s->tipo) {

                        case 'licencaOperacao':
                            $s->tipo = "Licença de Operação";
                            break;

                        case 'nRenovaveis':
                            $s->tipo = "Não Renováveis";
                            break;

                        case 'controleCertidoes':
                            $s->tipo = "Controle de Certidões";
                            break;

                        case 'controleTaxas':
                            $s->tipo = "Controle de Taxas";
                            break;

                        case 'facilitiesRealEstate':
                            $s->tipo = "Facilities/Real Estate";
                            break;

                    }

                    $etapa = null;

                    if (! $s->protocolo_anexo) {
                        $etapa = "Em elaboração";
                    } else {
                        if (! $s->laudo_anexo) {
                            $etapa = "Em elaboração";
                        } else {
                            $etapa = "1° Análise";
                        }
                    }

                    if ($s->solicitanteServico) {
                        $solicitante = $s->solicitanteServico->nome;
                    } else {
                        $solicitante = $s->solicitante;
                    }

                    if (count($s->vinculos)) {
                        $vinculo = null;
                        foreach ($s->vinculos as $v) {
                            // dump($v->servico->os);
                            $vinculo = $v->servico->os;
                        }
                    } else {
                        $vinculo = null;
                    }

                    if ($s->licenciamento) {
                        $licenciamento = $s->licenciamento;
                    } else {
                        $licenciamento = null;
                    }

                    $historico = null;
                    foreach ($s->historico as $h) {
                        $historico = $h->observacoes . " ";
                    }

                    if (! $s->financeiro) {

                        $financeiro                = new ServicoFinanceiro();
                        $financeiro->servico_id    = $s->id;
                        $financeiro->valorTotal    = 0;
                        $financeiro->valorFaturado = 0;
                        $financeiro->valorFaturar  = 0;
                        $financeiro->valorAberto   = 0;
                        $financeiro->status        = 'aberto';

                        $financeiro->save();
                        $s->financeiro = $financeiro;

                    }

                    if ($s->dataFinal) {
                        $dataFinalServico = date('d/m/Y', strtotime($s->dataFinal));
                    } else {
                        $dataFinalServico = null;
                    }

                    if ($s->dataLimiteCiclo) {
                        $dataLimiteCiclo = date('d/m/Y', strtotime($s->dataLimiteCiclo));
                    } else {
                        $dataLimiteCiclo = null;
                    }

                    if ($p->created_at)
                    {
                        $dataCriacaoPendencia = \Carbon\Carbon::parse($p->created_at)->format('d/m/Y');
                    } else{
                        $dataCriacaoPendencia = null;
                    }

                    if($p->vencimento)
                    {
                        $dataInicio = \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y') ?? null;
                    } else{
                        $dataInicio = null;
                    }

                    if($p->dataLimite)
                    {
                        $dataLimite = \Carbon\Carbon::parse($p->dataLimite)->format('d/m/Y') ?? null;
                    } else{
                        $dataLimite = null;
                    }



                    fputcsv($file, [
                        $p->id,
                        $p->etapa,
                        $s->unidade->empresa->nomeFantasia,
                        $s->nome,
                        $s->licenciamento ?? '',
                        $s->unidade->codigo,
                        $s->unidade->nomeFantasia,
                        $s->unidade->cnpj,
                        $s->unidade->cidade,
                        $s->unidade->uf,
                        $s->unidade->status,
                        $dataInauguracao,
                        $s->os,
                        $s->situacao ?? '', // Add 'situacao' field
                        $s->tipo,
                        number_format($s->financeiro->valorTotal, 2, ",", "."),
                        $solicitante,
                        $s->departamento ?? '', // Add 'departamento' field
                        $s->responsavel->name ?? '',
                        $s->coResponsavel->name ?? '',
                        $s->analista1->name ?? '',
                        $s->analista2->name ?? '',
                        $etapa,
                        $p->pendencia,
                        $p->responsavel->name ?? '',
                        $p->responsavel_tipo,
                        $dataCriacaoPendencia,
                        $dataInicio,
                        $dataLimite,
                        $dataFinalServico,
                        $dataLimiteCiclo,
                        $p->status,
                        $vinculo,
                        $s->id,
                        $s->created_at->format('d/m/Y') ?? '', // Add 'created_at' field formatted as desired
                        $historico,

                    ]);

                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    }

    public function pendenciasFilter(Request $request)
    {

        $fileName = 'Celic_RelatorioFiltro_Pendencias' . date('d-m-Y') . '.csv';

        $servicos = Servico::with(['pendencias' => function ($q) use ($request) {
            $q->where('status', $request->status);
        }])
            ->whereNotIn('responsavel_id', [1])
            ->orderBy('id', 'DESC')
            ->with('responsavel', 'coresponsavel')
            ->select('id', 'nome', 'os', 'unidade_id', 'tipo', 'protocolo_anexo', 'laudo_anexo', 'solicitante', 'responsavel_id', 'coresponsavel_id', 'licenciamento', 'departamento', 'situacao', 'created_at') // Add 'situacao' and 'created_at' to the select list
                                                                                                                                                                                                             // ->take(200)   //Somente para testes
            ->whereIn('empresa_id', $request->empresa_id)
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $columns = [
            'id',
            'Etapa',
            'Empresa',
            'Serviço',
            'Licenciamento',
            'Código',
            'Unidade',
            'CNPJ',
            'Cidade',
            'UF',
            'Status da Unidade',
            'Data Inauguração',
            'OS',
            'Situação', // Add 'Situação' column
            'Tipo',
            'Valor Serviço',
            'Solicitante',
            'Departamento', // Add 'Departamento' column
            'Responsável',
            'Co-Responsável',
            'Etapa do Processo',
            'Pendência',
            'Responsável Pendência',
            'Responsabilidade',
            'Data Criação',
            'Data Limite',
            'Status',
            'Vinculo',
            'ServicoID',
            'Criação serviço', // Add 'Criado em' column
            'Historico',

        ];

        $callback = function () use ($servicos, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($servicos as $s) {

                foreach ($s->pendencias as $p) {

                    $cidadeUF = $s->unidade->cidade . "/" . $s->unidade->uf;

                    if ($s->proposta_id) {
                        $proposta = $s->proposta_id;
                    } else {
                        $proposta = $s->proposta;
                    }

                    if ($s->unidade->dataInauguraao) {
                        $dataInauguracao = \Carbon\Carbon::parse($s->unidade->dataInauguracao)->format('d/m/Y');
                    } else {
                        $dataInauguracao = null;
                    }

                    switch ($p->responsavel_tipo) {

                        case 'usuario':
                            $p->responsavel_tipo = "Castro";
                            break;

                        case 'op':
                            $p->responsavel_tipo = "Órgão";
                            break;

                        case 'cliente':
                            $p->responsavel_tipo = "Cliente";
                            break;

                    }

                    switch ($p->status) {
                        case 'pendente':
                            $p->status = "Pendente";
                            break;

                        case 'concluido':
                            $p->status = "Concluído";
                            break;

                    }

                    switch ($s->tipo) {

                        case 'licencaOperacao':
                            $s->tipo = "Licença de Operação";
                            break;

                        case 'nRenovaveis':
                            $s->tipo = "Não Renováveis";
                            break;

                        case 'controleCertidoes':
                            $s->tipo = "Controle de Certidões";
                            break;

                        case 'controleTaxas':
                            $s->tipo = "Controle de Taxas";
                            break;

                        case 'facilitiesRealEstate':
                            $s->tipo = "Facilities/Real Estate";
                            break;

                    }

                    $etapa = null;

                    if (! $s->protocolo_anexo) {
                        $etapa = "Em elaboração";
                    } else {
                        if (! $s->laudo_anexo) {
                            $etapa = "Em elaboração";
                        } else {
                            $etapa = "1° Análise";
                        }
                    }

                    if ($s->solicitanteServico) {
                        $solicitante = $s->solicitanteServico->nome;
                    } else {
                        $solicitante = $s->solicitante;
                    }

                    if (count($s->vinculos)) {
                        $vinculo = null;
                        foreach ($s->vinculos as $v) {
                            // dump($v->servico->os);
                            $vinculo = $v->servico->os;
                        }
                    } else {
                        $vinculo = null;
                    }

                    if ($s->licenciamento) {
                        $licenciamento = $s->licenciamento;
                    } else {
                        $licenciamento = null;
                    }

                    $historico = null;
                    foreach ($s->historico as $h) {
                        $historico = $h->observacoes . " ";
                    }

                    if (! $s->financeiro) {

                        $financeiro                = new ServicoFinanceiro();
                        $financeiro->servico_id    = $s->id;
                        $financeiro->valorTotal    = 0;
                        $financeiro->valorFaturado = 0;
                        $financeiro->valorFaturar  = 0;
                        $financeiro->valorAberto   = 0;
                        $financeiro->status        = 'aberto';

                        $financeiro->save();
                        $s->financeiro = $financeiro;

                    }

                    fputcsv($file, [
                        $p->id,
                        $p->etapa,
                        $s->unidade->empresa->nomeFantasia,
                        $s->nome,
                        $s->licenciamento ?? '',
                        $s->unidade->codigo,
                        $s->unidade->nomeFantasia,
                        $s->unidade->cnpj,
                        $s->unidade->cidade,
                        $s->unidade->uf,
                        $s->unidade->status,
                        $dataInauguracao,
                        $s->os,
                        $s->situacao ?? '', // Add 'situacao' field
                        $s->tipo,
                        number_format($s->financeiro->valorTotal, 2, ",", "."),
                        $solicitante,
                        $s->departamento ?? '', // Add 'departamento' field
                        $s->responsavel->name ?? '',
                        $s->coResponsavel->name ?? '',
                        $etapa,
                        $p->pendencia,
                        $p->responsavel->name ?? '',
                        $p->responsavel_tipo,
                        \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') ?? '',
                        \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y') ?? '',
                        $p->status,
                        $vinculo,
                        $s->id,
                        $s->created_at->format('d/m/Y') ?? '', // Add 'created_at' field formatted as desired
                        $historico,

                    ]);

                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    }

    public function servicosFilterCSV(Request $request)
    {
        $fileName = 'Celic_RelatorioServicos' . date('d-m-Y') . '.csv';

        $servicos = Servico::whereNotIn('responsavel_id', [1])
            ->orderBy('id', 'DESC')
            ->with('responsavel', 'coresponsavel')
        // ->take(200)   //Somente para testes
            ->whereIn('empresa_id', $request->empresa_id)
            ->get();

        // dd($servicos);

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $columns = [
            'Nome Unidade',
            'CNPJ',
            'Status',
            'Cidade',
            'UF',
            'Tipo',
            'Situação',
            'Responsável',
            'Co-Responsável',
            'Analista 1',
            'Analista 2',
            'Nome Serviço',
            'Licenciamento',
            'Solicitante',
            'Departamento',
            'Proposta',
            'Emissão Licença',
            'Validade Licença',
            'Finalizado',
            'Criação',
        ];

        $callback = function () use ($servicos, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($servicos as $s) {

                // $cidadeUF = $s->unidade->cidade."/".$s->unidade->uf;

                if (is_numeric($s->solicitante)) {
                    $s->solicitante = \App\Models\Solicitante::where('id', $s->solicitante)->value('nome');
                }

                if ($s->proposta_id) {
                    $proposta = $s->proposta_id;
                } else {
                    $proposta = $s->proposta;
                }

                if (isset($s->servicoFinalizado)) {
                    $finalizado = \Carbon\Carbon::parse($s->servicoFinalizado->finalizado)->format('d/m/Y');
                } else {
                    $finalizado = '';
                }

                if (isset($s->licenca_emissao)) {
                    $licenca_emissao = \Carbon\Carbon::parse($s->licenca_emissao)->format('d/m/Y');
                } else {
                    $licenca_emissao = null;
                }
                if (isset($s->licenca_validade)) {
                    $licenca_validade = \Carbon\Carbon::parse($s->licenca_validade)->format('d/m/Y');
                } else {
                    $licenca_validade = null;
                }

                if ($s->licenciamento) {
                    $licenciamento = $s->licenciamento;
                } else {
                    $licenciamento = null;
                }

                fputcsv($file, [
                    $s->unidade->nomeFantasia,
                    $s->unidade->cnpj,
                    $s->unidade->status,
                    $s->unidade->cidade,
                    $s->unidade->uf,
                    $s->tipo,
                    $s->situacao,
                    $s->responsavel->name,
                    $s->coresponsavel->name ?? '',
                    $s->analista1->name ?? '',
                    $s->analista2->name ?? '',
                    $s->nome,
                    $licenciamento,
                    $s->solicitante,
                    $s->departamento,
                    $proposta,
                    $licenca_emissao,
                    $licenca_validade,
                    $finalizado,
                    \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') ?? '',

                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
        // dd($callback);

    }

    // Função para listar os arquivos de relatórios gerados
    public function listarRelatorios()
    {
        $directory = public_path('uploads/relatorios');
        $files     = File::files($directory);

        $reports = [];

        foreach ($files as $file) {
            $reports[] = [
                'name'          => $file->getFilename(),
                'date'          => $file->getMTime(),                                        // Timestamp
                'download_link' => url('public/uploads/relatorios/' . $file->getFilename()), // Link para download
            ];
        }

        // Ordenar os relatórios pela data de criação (mais recente primeiro)
        usort($reports, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return response()->json($reports);
    }

    public function deleteRelatorio($filename)
    {
        $filePath = public_path("uploads/relatorios/{$filename}");

        if (file_exists($filePath)) {
            // Tenta excluir o arquivo
            if (unlink($filePath)) {
                return response()->json(['message' => 'Relatório excluído com sucesso!']);
            } else {
                return response()->json(['message' => 'Erro ao excluir o relatório22!'], 500);
            }
        } else {
            return response()->json(['message' => 'Arquivo não encontrado!'], 404);
        }
    }

}
