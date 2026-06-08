# Instrução de engenharia de software (time técnico)

## 1) Objetivo técnico

Documentar arquitetura, contratos, validação, troubleshooting e plano de estabilização da integração NFS-e (PlugNotas), para desenvolvedores responsáveis por testar, validar e corrigir bugs.

---

## 2) Escopo implementado

### Componentes criados

- **Controller**:
  - `app/Http/Controllers/NfseController.php`

- **Services**:
  - `app/Services/Nfse/NfseEmissionService.php`
  - `app/Services/Nfse/NfsePayloadFactory.php`
  - `app/Services/Nfse/PlugNotasClient.php`

- **Models**:
  - `app/Models/NfseConfiguration.php`
  - `app/Models/NfseEmission.php`
  - `app/Models/NfseEmissionItem.php`
  - relation em `app/Models/Faturamento.php`

- **Migrations**:
  - `database/migrations/2026_04_03_100000_create_nfse_configurations_table.php`
  - `database/migrations/2026_04_03_100100_create_nfse_emissions_table.php`
  - `database/migrations/2026_04_03_100200_create_nfse_emission_items_table.php`

- **Config**:
  - `config/services.php` (`services.plugnotas`)

- **Rotas**:
  - `routes/api.php` (`/api/nfse/*`)

- **Testes unitários**:
  - `tests/Unit/Nfse/NfsePayloadFactoryTest.php`
  - `tests/Unit/Nfse/PlugNotasClientTest.php`

---

## 3) Modelo de dados e responsabilidade

### `nfse_configurations`
Responsável por persistir parâmetros fiscais estáveis por emitente.

Campos críticos:
- `dados_castro_id`
- `emit_as`
- `simples_regime`
- `tomador_tipo`
- `intermediario_tipo`
- `codigo_tributacao_nacional`
- `item_nbs`
- `pis_cofins_situacao`
- `aliquota_simples`
- flags de ISS/PIS/benefício

### `nfse_emissions`
Representa um lote/ação de emissão (automática/manual/não emitir).

### `nfse_emission_items`
Representa item de emissão por serviço (ou agrupado) com rastreabilidade de status, `external_id`, `numero_nf`, arquivos e erro.

---

## 4) Contratos de API

### Configuração
- `GET /api/nfse/configuracoes`
- `POST /api/nfse/configuracoes`

### Emissão
- `POST /api/nfse/emitir-automatico`
- `POST /api/nfse/anexar-manual`
- `POST /api/nfse/nao-emitir`

### Consulta operacional
- `GET /api/nfse/faturamentos/{faturamentoId}/servicos`
- `GET /api/nfse/faturamentos/{faturamentoId}/status`

### Integração assíncrona
- `POST /api/nfse/webhooks/plugnotas`

### Artefatos
- `GET /api/nfse/emissoes/{emissionId}/zip`

---

## 5) Fluxos de negócio implementados

## 5.1 Emissão automática

Entradas:
- `faturamento_id`
- `opcao_automatica`:
  - `individual_cnpj_padrao`
  - `individual_cnpj_manual`
  - `agrupado`
- `servico_ids`
- opcionais de CNPJ/campos adicionais

Regras:
- resolve configuração ativa (`nfse_configuration_id` direto ou fallback por `dados_castro_id`);
- valida serviços elegíveis dentro do faturamento;
- bloqueia duplicidade (`ensureServicosNotDuplicated`);
- gera payload com defaults solicitados;
- envia via `PlugNotasClient`;
- persiste emissão e itens.

## 5.2 Anexação manual

- cria emissão com `modo=manual`;
- cria itens vinculados por serviço;
- persiste número de NF e PDF (quando enviado).

## 5.3 Não emitir

- registra emissão com `modo=nao_emitir` e `status=nao_emitir`.

## 5.4 Webhook

- valida header `X-PlugNotas-Secret` quando secret configurado;
- atualiza item por `external_id`;
- atualiza status de emissão por agregação.

---

## 6) Variáveis de ambiente

- `PLUGNOTAS_BASE_URL`
- `PLUGNOTAS_API_KEY`
- `PLUGNOTAS_TIMEOUT`
- `PLUGNOTAS_MOCK_MODE`
- `PLUGNOTAS_WEBHOOK_SECRET`

### Estratégia recomendada

Homologação inicial:
- `PLUGNOTAS_MOCK_MODE=true`

Pré-produção/produção:
- `PLUGNOTAS_MOCK_MODE=false`
- `PLUGNOTAS_API_KEY` válida
- webhook ativo e validado

---

## 7) Plano de validação técnica (sem subir front)

## Nível 1 — Estático

- Lint/sintaxe de arquivos alterados.
- Inspeção de rotas e validações.
- Revisão de casts/model fillable.

## Nível 2 — Unitário

- `NfsePayloadFactoryTest`:
  - descrição agrupada;
  - sanitização CNPJ;
  - defaults automáticos.

- `PlugNotasClientTest`:
  - sucesso em mock;
  - falha esperada sem API key em modo real.

## Nível 3 — Integração controlada (recomendado)

Executar em ambiente com DB e credenciais:
1. cadastrar configuração;
2. emitir em cada modo;
3. simular webhook;
4. validar persistência de status;
5. validar ZIP.

---

## 8) Riscos técnicos conhecidos e mitigação

## 8.1 Contrato PlugNotas pode variar por município
Mitigação:
- manter payload factory isolada;
- mapear campos por cidade/regime caso necessário.

## 8.2 Dependências locais ausentes para teste completo
Mitigação:
- fallback robusto em mock no cliente;
- pipeline CI com ambiente provisionado para testes completos.

## 8.3 Conflito de duplicidade em operação concorrente
Mitigação sugerida:
- reforçar unicidade em banco (constraint em `servico_id` por status ativo, se regra confirmar);
- transação já implementada no serviço.

## 8.4 Segurança de webhook
Mitigação:
- `PLUGNOTAS_WEBHOOK_SECRET` obrigatório em produção;
- eventual allowlist de IP/proxy reverso.

---

## 9) Runbook de troubleshooting

### Sintoma: “Configuração ativa não encontrada”
Causa provável:
- falta de cadastro em `nfse_configurations`.

Ação:
- criar configuração via `POST /api/nfse/configuracoes`.

### Sintoma: “Serviço já vinculado a nota”
Causa provável:
- bloqueio de duplicidade funcionando.

Ação:
- consultar `GET /api/nfse/faturamentos/{id}/status`;
- avaliar fluxo de correção/substituição.

### Sintoma: webhook 403
Causa provável:
- secret divergente.

Ação:
- alinhar `X-PlugNotas-Secret` com `PLUGNOTAS_WEBHOOK_SECRET`.

### Sintoma: ZIP vazio
Causa provável:
- itens sem `pdf_path` válido.

Ação:
- validar anexação/caminho no disco local.

---

## 10) Backlog técnico recomendado

- testes de integração HTTP com mock server da PlugNotas;
- testes de concorrência para emissão simultânea;
- observabilidade (logs estruturados com correlation id);
- política de retry/backoff explícita em falhas transitórias;
- endpoint de substituição/cancelamento (se requerido pelo negócio).

---

## 11) Critérios de aceite técnico para homologação

- [ ] Todas as migrations executam sem erro.
- [ ] Configuração ativa pode ser cadastrada/consultada.
- [ ] Emissão automática funciona nos 3 modos.
- [ ] Duplicidade é bloqueada corretamente.
- [ ] Anexação manual persiste número/PDF.
- [ ] Não emitir persiste status correto.
- [ ] Webhook atualiza status/item por `external_id`.
- [ ] Geração de ZIP funciona com PDFs existentes.
- [ ] Logs de erro são rastreáveis.

---

## 12) Responsabilidade de manutenção

- Regras fiscais: evoluir em `NfsePayloadFactory`.
- Fluxo transacional: evoluir em `NfseEmissionService`.
- Contrato externo HTTP: evoluir em `PlugNotasClient`.
- Camada de entrada/validação API: evoluir em `NfseController`.
