<?php

namespace App\Http\Controllers;

use App\Models\Faturamento;
use App\Models\NfseConfiguration;
use App\Models\NfseEmission;
use App\Services\NfseService;
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
        $validator = Validator::make($request->all(), [
            'dados_castro_id' => 'required|integer|exists:dados_castros,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa emitente inválida para sincronização.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dadosCastroId = (int) $request->get('dados_castro_id');
        $dadosCastro = \App\Models\DadosCastro::with('nfseConfiguration')->find($dadosCastroId);

        if (!$dadosCastro) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa emitente não encontrada.',
            ], 404);
        }

        $config = $dadosCastro->nfseConfiguration;
        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Configuração NFS-e não encontrada. Salve a configuração antes de sincronizar.',
            ], 422);
        }

        $service = app(NfseService::class);
        $payload = $this->buildEmpresaPayloadForPlugNotas($dadosCastro, $config);
        $cnpj = isset($payload['cpfCnpj']) ? $payload['cpfCnpj'] : null;

        if (empty($cnpj)) {
            \Log::warning('NFSe syncEmpresa: CNPJ ausente no payload.', [
                'dados_castro_id' => $dadosCastroId,
                'dados_castro_cnpj' => $dadosCastro->cnpj,
                'payload' => $payload,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'CNPJ do emitente não informado.',
            ], 422);
        }

        $consultaEmpresa = $service->consultarEmpresaPorCnpj($cnpj);
        if (!empty($consultaEmpresa['success']) && !empty($consultaEmpresa['exists'])) {
            $config->plugnotas_empresa_sincronizada = true;
            $config->plugnotas_empresa_sync_at = now();
            $config->plugnotas_empresa_sync_error = null;
            $config->save();

            return response()->json([
                'success' => true,
                'synced' => true,
                'message' => 'Empresa já cadastrada na PlugNotas e marcada como sincronizada.',
                'sync_at' => optional($config->plugnotas_empresa_sync_at)->format('d/m/Y H:i:s'),
            ]);
        }

        if (empty($consultaEmpresa['success'])) {
            \Log::warning('NFSe syncEmpresa: falha ao consultar empresa na PlugNotas.', [
                'dados_castro_id' => $dadosCastroId,
                'cnpj' => $cnpj,
                'consulta_empresa' => $consultaEmpresa,
                'payload' => $payload,
            ]);

            return response()->json([
                'success' => false,
                'message' => $consultaEmpresa['message'] ?? 'Não foi possível consultar empresa na PlugNotas.',
            ], 422);
        }

        $requiredErrors = [];
        if (empty($payload['razaoSocial'])) {
            $requiredErrors[] = 'Razão social do emitente não informada.';
        }

        // Quando a empresa ainda nao existe na PlugNotas, estes campos sao exigidos no cadastro.
        $requiredForCadastro = $this->validateRequiredFieldsForPlugNotasCadastro($payload);
        if (!empty($requiredForCadastro)) {
            $requiredErrors = array_merge($requiredErrors, $requiredForCadastro);
        }

        if (!empty($requiredErrors)) {
            \Log::warning('NFSe syncEmpresa: validacao bloqueou cadastro de empresa na PlugNotas.', [
                'dados_castro_id' => $dadosCastroId,
                'cnpj' => $cnpj,
                'required_errors' => $requiredErrors,
                'consulta_empresa' => $consultaEmpresa,
                'payload' => $payload,
                'nfse_config_id' => $config->id,
                'certificado' => $config->certificado,
                'inscricao_municipal' => $config->inscricao_municipal,
            ]);

            return response()->json([
                'success' => false,
                'message' => implode(' ', $requiredErrors),
            ], 422);
        }

        try {
            $result = $service->cadastrarEmpresa($payload);

            if (!empty($result['success'])) {
                $cnpjRetornado = preg_replace('/\D/', '', (string) ($result['data']['data']['cnpj'] ?? $result['data']['cnpj'] ?? ''));
                $confirmadoPorRetorno = !empty($cnpjRetornado) && $cnpjRetornado === $cnpj;

                $confirmadoPorConsulta = false;
                if (!$confirmadoPorRetorno) {
                    $confirmacao = $service->consultarEmpresaPorCnpj($cnpj);
                    $confirmadoPorConsulta = !empty($confirmacao['success']) && !empty($confirmacao['exists']);
                }

                if ($confirmadoPorRetorno || $confirmadoPorConsulta) {
                    $config->plugnotas_empresa_sincronizada = true;
                    $config->plugnotas_empresa_sync_at = now();
                    $config->plugnotas_empresa_sync_error = null;
                    $config->save();

                    return response()->json([
                        'success' => true,
                        'synced' => true,
                        'message' => $result['message'] ?? 'Empresa sincronizada com sucesso na PlugNotas.',
                        'sync_at' => optional($config->plugnotas_empresa_sync_at)->format('d/m/Y H:i:s'),
                    ]);
                }

                \Log::warning('NFSe syncEmpresa: API retornou sucesso, mas sem confirmacao de existencia da empresa na PlugNotas.', [
                    'dados_castro_id' => $dadosCastroId,
                    'cnpj' => $cnpj,
                    'cnpj_retorno_cadastro' => $cnpjRetornado,
                    'result' => $result,
                ]);

                $config->plugnotas_empresa_sincronizada = false;
                $config->plugnotas_empresa_sync_error = 'PlugNotas retornou sucesso, mas não confirmou a empresa no cadastro.';
                $config->save();

                return response()->json([
                    'success' => false,
                    'synced' => false,
                    'message' => 'A PlugNotas respondeu sucesso, mas a empresa não foi confirmada no cadastro. Verifique no portal e tente novamente.',
                ], 422);
            }

            $errorMessage = !empty($result['message']) ? $result['message'] : 'Falha ao sincronizar empresa com a PlugNotas.';
            \Log::warning('NFSe syncEmpresa: cadastro/atualizacao na PlugNotas retornou falha.', [
                'dados_castro_id' => $dadosCastroId,
                'cnpj' => $cnpj,
                'result' => $result,
                'payload' => $payload,
            ]);

            $config->plugnotas_empresa_sync_error = $errorMessage;
            $config->save();

            return response()->json([
                'success' => false,
                'synced' => (bool) $config->plugnotas_empresa_sincronizada,
                'message' => $errorMessage,
            ], 422);
        } catch (\Exception $e) {
            \Log::error('NFSe syncEmpresa: excecao durante sincronizacao.', [
                'dados_castro_id' => $dadosCastroId,
                'cnpj' => $cnpj,
                'payload' => $payload,
                'exception_message' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);

            $config->plugnotas_empresa_sync_error = $e->getMessage();
            $config->save();

            return response()->json([
                'success' => false,
                'synced' => (bool) $config->plugnotas_empresa_sincronizada,
                'message' => 'Erro ao sincronizar com a PlugNotas: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function buildEmpresaPayloadForPlugNotas($dadosCastro, NfseConfiguration $config)
    {
        $cnpj = preg_replace('/\D/', '', (string) $dadosCastro->cnpj);
        $cep = preg_replace('/\D/', '', (string) $config->cep);
        $telefoneSomenteDigitos = preg_replace('/\D/', '', (string) $config->telefone_emitente);

        $ddd = null;
        $numeroTelefone = null;
        if (!empty($telefoneSomenteDigitos)) {
            if (strlen($telefoneSomenteDigitos) >= 10) {
                $ddd = substr($telefoneSomenteDigitos, 0, 2);
                $numeroTelefone = substr($telefoneSomenteDigitos, 2);
            } else {
                $numeroTelefone = $telefoneSomenteDigitos;
            }
        }

        $simplesNacional = in_array((int) $config->regime_tributario, [1, 2], true);
        $emailEmitente = !empty($config->email_emitente)
            ? $config->email_emitente
            : env('MAIL_FROM_ADDRESS');

        $payload = [
            'cpfCnpj' => $cnpj,
            'razaoSocial' => $dadosCastro->razaoSocial,
            'nomeFantasia' => $dadosCastro->razaoSocial,
            'email' => $emailEmitente,
            'inscricaoMunicipal' => $config->inscricao_municipal,
            'regimeTributario' => (int) ($config->regime_tributario ?: 1),
            'simplesNacional' => $simplesNacional,
            'certificado' => $config->certificado,
            'endereco' => [
                'bairro' => $config->bairro,
                'cep' => $cep,
                'codigoCidade' => (string) $config->codigo_cidade,
                'estado' => strtoupper((string) $config->uf),
                'tipoLogradouro' => 'Rua',
                'logradouro' => $config->logradouro,
                'numero' => (string) $config->numero,
                'descricaoCidade' => $config->municipio_nome,
                'codigoPais' => '1058',
                'descricaoPais' => 'Brasil',
            ],
            'nfse' => [
                'ativo' => (bool) $config->ativo,
                'tipoContrato' => 0,
                'config' => [
                    'producao' => (bool) $config->producao,
                    'prefeitura' => [
                        'login' => $config->login_prefeitura,
                        'senha' => $config->senha_prefeitura,
                    ],
                ],
            ],
        ];

        if (!empty($ddd) || !empty($numeroTelefone)) {
            $payload['telefone'] = [
                'ddd' => $ddd,
                'numero' => $numeroTelefone,
            ];
        }

        return $this->removeNullAndEmpty($payload);
    }

    private function removeNullAndEmpty(array $data)
    {
        $clean = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->removeNullAndEmpty($value);
                if (empty($value)) {
                    continue;
                }

                $clean[$key] = $value;
                continue;
            }

            if ($value === null) {
                continue;
            }

            if (is_string($value) && trim($value) === '') {
                continue;
            }

            $clean[$key] = $value;
        }

        return $clean;
    }

    private function validateRequiredFieldsForPlugNotasCadastro(array $payload)
    {
        $missing = [];

        if (empty($payload['cpfCnpj'])) {
            $missing[] = 'CNPJ do emitente (cpfCnpj)';
        }

        if (empty($payload['razaoSocial'])) {
            $missing[] = 'Razão social do emitente (razaoSocial)';
        }

        if (empty($payload['inscricaoMunicipal'])) {
            $missing[] = 'Inscrição municipal (inscricaoMunicipal)';
        }

        if (empty($payload['email'])) {
            $missing[] = 'Email do emitente (email)';
        }

        if (empty($payload['certificado'])) {
            $missing[] = 'ID do certificado PlugNotas (certificado)';
        }

        if (empty($payload['endereco']) || !is_array($payload['endereco'])) {
            $missing[] = 'Endereço do emitente (endereco)';
        } else {
            if (empty($payload['endereco']['codigoCidade'])) {
                $missing[] = 'Código IBGE da cidade (endereco.codigoCidade)';
            }

            if (empty($payload['endereco']['estado'])) {
                $missing[] = 'UF do emitente (endereco.estado)';
            }

            if (empty($payload['endereco']['logradouro'])) {
                $missing[] = 'Logradouro do emitente (endereco.logradouro)';
            }

            if (empty($payload['endereco']['tipoLogradouro'])) {
                $missing[] = 'Tipo de logradouro (endereco.tipoLogradouro)';
            }

            if (empty($payload['endereco']['numero'])) {
                $missing[] = 'Número do endereço (endereco.numero)';
            }

            if (empty($payload['endereco']['bairro'])) {
                $missing[] = 'Bairro do emitente (endereco.bairro)';
            }
        }

        if (empty($missing)) {
            return [];
        }

        return ['Preencha os campos obrigatórios para cadastro na PlugNotas: ' . implode(', ', $missing) . '.'];
    }

    public function showEmissao($faturamentoId)
    {
        $faturamento = Faturamento::with(['servicosFaturados.detalhes.unidade', 'servicosFaturados.detalhes.financeiro'])->findOrFail($faturamentoId);
        $dadosCastros = \App\Models\DadosCastro::where('ativo', true)->get();
        $activeConfig = $dadosCastros->first();
        
        return view('admin.nfse.emissao', compact('faturamento', 'dadosCastros', 'activeConfig'));
    }

    public function processarEmissao(Request $request, $faturamentoId)
    {
        $data = $request->all();
        $data['faturamento_id'] = $faturamentoId;
        
        // Se houver dados de tomador override
        if ($request->has('nova_empresa') && $request->nova_empresa == '1') {
            $data['tomador_override'] = $this->buildTomadorOverrideFromRequest($request);
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
            // Usando BrasilAPI (v2 é mais detalhada)
            $response = $client->get("https://brasilapi.com.br/api/cnpj/v1/{$cnpj}");
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
                'ibge' => $data['codigo_municipio_ibge'] ?? \App\Helpers\IbgeHelper::getIbgeCode($data['municipio'] ?? null, $data['uf'] ?? null),
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
            'opcao_automatica' => 'required|string|in:1,2,3,4,individual_cnpj_padrao,individual_cnpj_manual,agrupado,agrupado_manual',
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
            $data = $validator->validated();

            if (!isset($data['tomador_override']) && !empty($data['cnpj_manual_agrupado'])) {
                $override = $this->buildTomadorOverrideFromRequest($request);
                $override['cnpj'] = $data['cnpj_manual_agrupado'];
                $data['tomador_override'] = $override;
            }

            $emission = $this->service->emitirAutomatico($data);
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

    private function buildTomadorOverrideFromRequest(Request $request)
    {
        return [
            'cnpj' => $request->get('override_cnpj', $request->get('cnpj_manual', $request->get('tomador_cnpj'))),
            'razaoSocial' => $request->get('override_razaoSocial', $request->get('razao_social_manual', $request->get('tomador_razaoSocial'))),
            'email' => $request->get('override_email', $request->get('email_manual', $request->get('tomador_email'))),
            'logradouro' => $request->get('override_logradouro', $request->get('logradouro_manual', $request->get('tomador_logradouro'))),
            'numero' => $request->get('override_numero', $request->get('numero_manual', $request->get('tomador_numero'))),
            'bairro' => $request->get('override_bairro', $request->get('bairro_manual', $request->get('tomador_bairro'))),
            'cep' => $request->get('override_cep', $request->get('cep_manual', $request->get('tomador_cep'))),
            'municipio' => $request->get('override_municipio', $request->get('municipio_manual', $request->get('tomador_municipio'))),
            'uf' => $request->get('override_uf', $request->get('uf_manual', $request->get('tomador_uf', $request->get('tomador_estado')))),
            'codigoCidade' => $request->get('override_codigoCidade', $request->get('codigo_cidade_manual', $request->get('tomador_codigoCidade'))),
        ];
    }
}
