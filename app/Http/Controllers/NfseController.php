<?php

namespace App\Http\Controllers;

use App\Models\Faturamento;
use App\Models\NfseConfiguration;
use App\Models\NfseEmission;
use App\Services\Nfse\NfseEmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NfseController extends Controller
{
    /** @var NfseEmissionService */
    private $service;

    public function __construct(NfseEmissionService $service)
    {
        $this->service = $service;
    }

    public function upsertConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer|exists:nfse_configurations,id',
            'dados_castro_id' => 'nullable|integer',
            'emit_as' => 'required|string|in:Prestador,Tomador,Intermediario',
            'simples_regime' => 'required|string',
            'tomador_tipo' => 'required|string|in:Tomador nao informado,Brasil,Exterior',
            'intermediario_tipo' => 'required|string|in:Intermediario nao informado,Brasil,Exterior',
            'local_prestacao' => 'required|string',
            'municipio_nome' => 'required|string',
            'municipio_ibge' => 'nullable|string',
            'codigo_tributacao_nacional' => 'required|string',
            'suspensao_exigibilidade_issqn' => 'required|boolean',
            'item_nbs' => 'required|string',
            'issqn_exigibilidade_suspensa' => 'required|boolean',
            'issqn_retido' => 'required|boolean',
            'beneficio_municipal' => 'required|boolean',
            'pis_cofins_situacao' => 'required|string',
            'aliquota_simples' => 'required|numeric|min:0',
            'valor_aproximado_tributos' => 'nullable|numeric|min:0',
            'ativo' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['provider'] = 'plugnotas';

        if (isset($data['ativo']) && $data['ativo']) {
            NfseConfiguration::where('dados_castro_id', isset($data['dados_castro_id']) ? $data['dados_castro_id'] : null)
                ->update(['ativo' => false]);
        }

        $config = null;
        if (!empty($data['id'])) {
            $config = NfseConfiguration::findOrFail($data['id']);
            $config->fill($data);
            $config->save();
        } else {
            $config = NfseConfiguration::create($data);
        }

        return response()->json($config);
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

    public function gerarZip($emissionId)
    {
        $emission = $this->service->gerarZipNotas($emissionId);

        if (empty($emission->zip_path) || !Storage::disk('local')->exists($emission->zip_path)) {
            return response()->json(['error' => 'ZIP não encontrado.'], 404);
        }

        return response()->download(storage_path('app/' . $emission->zip_path));
    }
}
