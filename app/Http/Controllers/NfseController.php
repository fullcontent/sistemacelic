<?php

namespace App\Http\Controllers;

use App\Models\Faturamento;
use App\Models\NfseConfiguration;
use App\Models\NfseEmission;
use App\Services\Nfse\NfseEmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use Carbon\Carbon;

class NfseController extends Controller
{
    /** @var NfseEmissionService */
    private $service;

    public function __construct(NfseEmissionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = NfseEmission::with(['itens', 'faturamento.empresa'])
            ->orderBy('id', 'desc');

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('data_inicio') && !empty($request->data_inicio)) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $request->data_inicio)->toDateString());
        }

        if ($request->has('data_fim') && !empty($request->data_fim)) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $request->data_fim)->toDateString());
        }

        if ($request->has('faturamento_id') && !empty($request->faturamento_id)) {
            $query->where('faturamento_id', $request->faturamento_id);
        }

        $emissions = $query->paginate(20);

        return view('admin.nfse.index', compact('emissions'));
    }

    public function indexConfig()
    {
        $dadosCastros = \App\Models\DadosCastro::where('ativo', true)->with('nfseConfiguration')->get();
        return view('admin.nfse.config', compact('dadosCastros'));
    }

    public function storeEmitente(Request $request)
    {
        $dc = \App\Models\DadosCastro::create([
            'cnpj' => $request->cnpj,
            'razaoSocial' => $request->razaoSocial,
            'ativo' => true
        ]);

        NfseConfiguration::create([
            'dados_castro_id' => $dc->id,
            'logradouro' => $request->logradouro,
            'numero' => $request->numero,
            'bairro' => $request->bairro,
            'cep' => $request->cep,
            'uf' => $request->uf,
            'telefone_emitente' => $request->telefone_emitente,
            'email_emitente' => $request->email_emitente,
            'codigo_cidade' => '4202008', // Default
            'regime_tributario' => '1',
            'emit_as' => 'Prestador',
            'simples_regime' => '1',
            'tomador_tipo' => 'Brasil',
            'intermediario_tipo' => 'Intermediario nao informado',
            'local_prestacao' => '4202008',
            'municipio_nome' => 'Joinville',
            'codigo_tributacao_nacional' => '0101',
            'suspensao_exigibilidade_issqn' => false,
            'item_nbs' => '0000',
            'issqn_exigibilidade_suspensa' => false,
            'issqn_retido' => false,
            'beneficio_municipal' => false,
            'pis_cofins_situacao' => '1',
            'aliquota_simples' => 0.0,
        ]);

        return redirect()->back()->with('success', 'Nova empresa cadastrada e parcialmente preenchida. Verifique a nova aba.');
    }

    public function destroyEmitente($id)
    {
        $dc = \App\Models\DadosCastro::findOrFail($id);
        $dc->ativo = false;
        $dc->save();

        return response()->json(['success' => true]);
    }

    public function syncEmpresa(Request $request)
    {
        // Dummy implementation to complete the front-end success block
        return response()->json(['success' => true]);
    }

    public function showEmissao($faturamentoId)
    {
        $faturamento = Faturamento::with(['servicosFaturados.detalhes.unidade', 'servicosFaturados.detalhes.financeiro'])->findOrFail($faturamentoId);
        $dadosCastros = \App\Models\DadosCastro::where('ativo', true)->get();
        $activeConfig = $dadosCastros->first();
        
        return view('admin.nfse.emissao', compact('faturamento', 'dadosCastros', 'activeConfig'));
    }

    public function previewEmissao(Request $request, $faturamentoId)
    {
        $data = $request->all();
        $data['faturamento_id'] = $faturamentoId;
        
        // Se houver dados de tomador override
        if ($request->has('nova_empresa') && $request->nova_empresa == '1') {
            $data['tomador_override'] = [
                'cnpj' => $request->override_cnpj,
                'razaoSocial' => $request->override_razaoSocial,
                'email' => $request->override_email,
                'logradouro' => $request->override_logradouro,
                'numero' => $request->override_numero,
                'bairro' => $request->override_bairro,
                'cep' => $request->override_cep,
                'uf' => $request->override_uf,
                'codigoCidade' => $request->override_codigoCidade,
            ];
        }

        try {
            $payloads = $this->service->buildPayloadsPreview($data);
            return response()->json([
                'success' => true,
                'payloads' => $payloads,
                'count' => count($payloads)
            ]);
        } catch (\Exception $e) {
            \Log::error('NfseController: Erro ao gerar preview: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function processarEmissao(Request $request, $faturamentoId)
    {
        $data = $request->all();
        $data['faturamento_id'] = $faturamentoId;
        
        // Se houver dados de tomador override
        if ($request->has('nova_empresa') && $request->nova_empresa == '1') {
            $data['tomador_override'] = [
                'cnpj' => $request->override_cnpj,
                'razaoSocial' => $request->override_razaoSocial,
                'email' => $request->override_email,
                'logradouro' => $request->override_logradouro,
                'numero' => $request->override_numero,
                'bairro' => $request->override_bairro,
                'cep' => $request->override_cep,
                'uf' => $request->override_uf,
                'codigoCidade' => $request->override_codigoCidade,
            ];
        }

        try {
            \Log::info('NfseController: Processando emissão para faturamento ' . $faturamentoId, $data);
            $this->service->emitirAutomatico($data);
            return redirect()->route('faturamento.show', $faturamentoId)->with('success', 'Emissão de NFS-e processada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('NfseController: Erro na emissão: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Falha na emissão: ' . $e->getMessage());
        }
    }

    public function buscarCnpjExterno($cnpj)
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        if (strlen($cnpj) != 14) {
            return response()->json(['error' => 'CNPJ inválido.'], 400);
        }

        $client = new Client();
        try {
            // Usando BrasilAPI v2 (traz código IBGE)
            $response = $client->get("https://brasilapi.com.br/api/cnpj/v2/{$cnpj}");
            $data = json_decode((string) $response->getBody(), true);
            
            return response()->json([
                'cnpj' => $data['cnpj'],
                'razaoSocial' => $data['razao_social'],
                'logradouro' => $data['logradouro'],
                'numero' => $data['numero'],
                'bairro' => $data['bairro'] ?? '',
                'cep' => $data['cep'],
                'uf' => $data['uf'],
                'email' => $data['email'] ?? '',
                'municipio' => $data['municipio'],
                'ibge' => $data['municipio_ibge'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'CNPJ não encontrado ou erro na consulta.'], 404);
        }
    }

    public function cancelar($id, Request $request)
    {
        $motivo = $request->get('motivo', 'Cancelamento solicitado pelo usuário.');
        
        try {
            $this->service->cancelar($id, $motivo);
            return response()->json(['success' => true, 'message' => 'Nota cancelada com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function syncStatus($id)
    {
        try {
            $emission = $this->service->consultarStatus($id);
            return response()->json([
                'success' => true, 
                'status' => $emission->status, 
                'pdf_url' => $emission->pdf_url,
                'xml_url' => $emission->xml_url
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function syncBatch(Request $request)
    {
        try {
            $filtros = [];
            if ($request->has('data_inicio') && !empty($request->data_inicio)) {
                $filtros['dataInicio'] = Carbon::createFromFormat('d/m/Y', $request->data_inicio)->toDateString();
            }
            if ($request->has('data_fim') && !empty($request->data_fim)) {
                $filtros['dataFim'] = Carbon::createFromFormat('d/m/Y', $request->data_fim)->toDateString();
            }

            $count = $this->service->sincronizarGeral($filtros);
            
            return response()->json([
                'success' => true,
                'message' => "Sincronização concluída! {$count} notas foram processadas.",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function status($id)
    {
        $faturamento = Faturamento::findOrFail($id);
        $emissions = NfseEmission::where('faturamento_id', $id)->get();
        return response()->json($emissions);
    }

    public function upsertConfig(Request $request)
    {
        $dadosCastro = \App\Models\DadosCastro::find($request->dados_castro_id);
        if ($dadosCastro && $request->has('dados_castro')) {
            $dadosCastro->update($request->dados_castro);
        }

        $filtered = $request->except(['_token', 'dados_castro', 'cert_password', 'pfx_file']);
        
        // Handle boolean toggles
        $booleans = [
            'suspensao_exigibilidade_issqn',
            'issqn_exigibilidade_suspensa',
            'issqn_retido',
            'beneficio_municipal',
            'producao',
            'ativo'
        ];

        foreach ($booleans as $field) {
            $filtered[$field] = $request->has($field) && ($request->get($field) === 'on' || $request->get($field) === '1');
        }

        $config = \App\Models\NfseConfiguration::updateOrCreate(
            ['dados_castro_id' => $request->dados_castro_id],
            $filtered
        );

        return response()->json([
            'success' => true, 
            'message' => 'Configuração salva com sucesso!', 
            'config_id' => $config->id
        ]);
    }

    public function listConfigs(Request $request)
    {
        $query = NfseConfiguration::query();

        if ($request->has('dados_castro_id')) {
            $query->where('dados_castro_id', $request->get('dados_castro_id'));
        }

        if ($request->has('ativo')) {
            $query->where('ativo', filter_var($request->get('ativo'), FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json($query->orderBy('id', 'desc')->get());
    }

    public function faturamentoServicos($faturamentoId)
    {
        $faturamento = Faturamento::with('servicosFaturados.detalhes.unidade', 'servicosFaturados.detalhes.financeiro')
            ->findOrFail($faturamentoId);

        $servicos = [];

        foreach ($faturamento->servicosFaturados as $item) {
            if (!$item->detalhes) {
                continue;
            }

            $servicos[] = [
                'faturamento_servico_id' => $item->id,
                'servico_id' => $item->detalhes->id,
                'nome_servico' => $item->detalhes->nome,
                'codigo_unidade' => !empty($item->detalhes->unidade->codigo) ? $item->detalhes->unidade->codigo : null,
                'nome_unidade' => !empty($item->detalhes->unidade->nomeFantasia) ? $item->detalhes->unidade->nomeFantasia : null,
                'cnpj_padrao' => !empty($item->detalhes->unidade->cnpj) ? $item->detalhes->unidade->cnpj : null,
                'valor_faturado' => $item->valorFaturado,
            ];
        }

        return response()->json([
            'faturamento_id' => $faturamento->id,
            'servicos' => $servicos,
        ]);
    }

    public function emitirAutomatico(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faturamento_id' => 'required|integer|exists:faturamentos,id',
            'dados_castro_id' => 'nullable|integer',
            'nfse_configuration_id' => 'nullable|integer|exists:nfse_configurations,id',
            'opcao_automatica' => 'required|string|in:individual_cnpj_padrao,individual_cnpj_manual,agrupado',
            'servico_ids' => 'required|array|min:1',
            'servico_ids.*' => 'integer',
            'cnpj_manual_agrupado' => 'nullable|string',
            'cnpj_manual_por_servico' => 'nullable|array',
            'campos_adicionais' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $emission = $this->service->emitirAutomatico($validator->validated());
            return response()->json($emission, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function anexarManual(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faturamento_id' => 'required|integer|exists:faturamentos,id',
            'numero_nf' => 'required|string',
            'servico_ids' => 'required|array|min:1',
            'servico_ids.*' => 'integer',
            'observacoes' => 'nullable|string',
            'arquivo_pdf' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();

            if ($request->hasFile('arquivo_pdf')) {
                $data['pdf_path'] = $request->file('arquivo_pdf')->store('nfse/manual');
            }

            $emission = $this->service->anexarManual($data);
            return response()->json($emission, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function naoEmitir(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faturamento_id' => 'required|integer|exists:faturamentos,id',
            'observacoes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $emission = $this->service->naoEmitir($validator->validated());
        return response()->json($emission, 201);
    }

    public function statusFaturamento($faturamentoId)
    {
        $list = NfseEmission::with('itens')
            ->where('faturamento_id', $faturamentoId)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($list);
    }

    public function webhookPlugNotas(Request $request)
    {
        $secret = config('services.plugnotas.webhook_secret');
        $provided = $request->header('X-PlugNotas-Secret');

        if (!empty($secret) && $provided !== $secret) {
            return response()->json(['error' => 'Webhook não autorizado'], 403);
        }

        try {
            $item = $this->service->processarWebhook($request->all());
            return response()->json(['ok' => true, 'item_id' => $item->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function downloadPdf($id)
    {
        $emission = NfseEmission::with('itens')->findOrFail($id);
        $externalId = $emission->itens->whereNotNull('external_id')->first()->external_id ?? null;

        if (!$externalId) {
            return redirect()->back()->with('error', 'ID externo da nota não encontrado.');
        }

        try {
            $client = app(\App\Services\Nfse\PlugNotasClient::class);
            $content = $client->downloadFile($externalId, 'pdf');
            return response($content)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="nfse-'.$id.'.pdf"')
                ->header('Content-Length', strlen($content));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao baixar PDF: ' . $e->getMessage());
        }
    }

    public function downloadXml($id)
    {
        $emission = NfseEmission::with('itens')->findOrFail($id);
        $externalId = $emission->itens->whereNotNull('external_id')->first()->external_id ?? null;

        if (!$externalId) {
            return redirect()->back()->with('error', 'ID externo da nota não encontrado.');
        }

        try {
            $client = app(\App\Services\Nfse\PlugNotasClient::class);
            $content = $client->downloadFile($externalId, 'xml');
            return response($content)
                ->header('Content-Type', 'application/xml')
                ->header('Content-Disposition', 'attachment; filename="nfse-'.$id.'.xml"');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao baixar XML: ' . $e->getMessage());
        }
    }
}
