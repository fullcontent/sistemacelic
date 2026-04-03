# Integração NFS-e (Back-end) com TecnoSpeed PlugNotas

## Escopo implementado
Esta entrega implementa somente o **back-end** para integração de NFS-e com a API PlugNotas.

Não foi implementado front-end nesta etapa.

---

## O que foi criado

### 1) Configuração de emissão por empresa emitente
Foi criada estrutura para salvar configurações reutilizáveis da emissão NFS-e em `nfse_configurations`, contemplando os campos da Parte 02 (dados estáveis de emissão).

### 2) Área de emissão (back-end)
Foi criada API para os 3 modos:
- Emissão automática
- Anexação manual de NF
- Não emitir/anexar

Também foi criada persistência de histórico em:
- `nfse_emissions`
- `nfse_emission_items`

### 3) Emissão automática com 3 opções
Na rota de emissão automática (`/api/nfse/emitir-automatico`) existe suporte para:
- `individual_cnpj_padrao`
- `individual_cnpj_manual`
- `agrupado`

Regras aplicadas:
- bloqueio de duplicidade de serviço já vinculado a NF
- campos automáticos enviados com defaults solicitados:
  - data da competência = dia atual
  - indicador municipal, telefone e e-mail = em branco
- suporte aos campos adicionais opcionais

### 4) Webhook
Foi criado endpoint de webhook para retorno assíncrono da PlugNotas:
- `POST /api/nfse/webhooks/plugnotas`

### 5) Geração de ZIP
Foi criado endpoint para gerar ZIP com PDFs anexados/recebidos:
- `GET /api/nfse/emissoes/{emissionId}/zip`

---

## Rotas disponíveis (backend)

### Configuração
- `GET /api/nfse/configuracoes`
- `POST /api/nfse/configuracoes`

### Operação por faturamento
- `GET /api/nfse/faturamentos/{faturamentoId}/servicos`
- `GET /api/nfse/faturamentos/{faturamentoId}/status`

### Emissão
- `POST /api/nfse/emitir-automatico`
- `POST /api/nfse/anexar-manual`
- `POST /api/nfse/nao-emitir`

### Retorno e arquivos
- `POST /api/nfse/webhooks/plugnotas`
- `GET /api/nfse/emissoes/{emissionId}/zip`

---

## Configuração de ambiente (.env)
Adicione no ambiente da aplicação:

- `PLUGNOTAS_BASE_URL=https://api.plugnotas.com.br`
- `PLUGNOTAS_API_KEY=...`
- `PLUGNOTAS_TIMEOUT=30`
- `PLUGNOTAS_MOCK_MODE=true`
- `PLUGNOTAS_WEBHOOK_SECRET=...`

> Em desenvolvimento sem assinatura, mantenha `PLUGNOTAS_MOCK_MODE=true`.

---

## Como o cliente deve contratar e configurar a PlugNotas

### Passo 1 — Assinar o serviço
1. Acessar página oficial da TecnoSpeed PlugNotas.
2. Contratar plano com cobertura de NFS-e para os municípios usados pela operação.
3. Concluir validação cadastral da conta.

Links oficiais:
- Site: https://tecnospeed.com.br
- Produto PlugNotas: https://plugnotas.com.br
- Documentação da API: https://atendimento.tecnospeed.com.br/hc/pt-br/categories/360003705153-PlugNotas

### Passo 2 — Obter credenciais
Após contratação:
1. Gerar token/chave de API no painel da PlugNotas.
2. Configurar webhook de retorno apontando para:
   - `https://SEU_DOMINIO/api/nfse/webhooks/plugnotas`
3. Definir segredo do webhook (`PLUGNOTAS_WEBHOOK_SECRET`).

### Passo 3 — Configurar emitentes
No sistema:
1. Cadastrar configuração ativa em `/api/nfse/configuracoes` para cada emitente (Castro Empresarial / Castro Licenciamentos).
2. Validar campos fiscais obrigatórios por município.
3. Executar emissão teste com valor baixo em homologação.

### Passo 4 — Entrar em produção
1. Desativar mock: `PLUGNOTAS_MOCK_MODE=false`
2. Definir `PLUGNOTAS_API_KEY` real.
3. Validar recebimento do webhook e atualização de status.

---

## Exemplo de payloads

### Emissão automática (individual com CNPJ padrão)
```json
{
  "faturamento_id": 123,
  "opcao_automatica": "individual_cnpj_padrao",
  "servico_ids": [10, 11, 12],
  "campos_adicionais": {
    "documentoReferencia": "DOC-2026-001"
  }
}
```

### Emissão automática (agrupado)
```json
{
  "faturamento_id": 123,
  "opcao_automatica": "agrupado",
  "servico_ids": [10, 11, 12],
  "cnpj_manual_agrupado": "12.345.678/0001-99"
}
```

### Anexar manual
`multipart/form-data` com:
- `faturamento_id`
- `numero_nf`
- `servico_ids[]`
- `arquivo_pdf` (opcional)

---

## Prompt para IA construir o front-end (NÃO implementado nesta etapa)
Use o prompt abaixo em uma IA para gerar somente o front-end:

> Crie um front-end (Laravel Blade ou React) para a área “Emissão de NFS-e” consumindo as rotas REST já existentes em `/api/nfse`. A tela deve abrir após finalizar faturamento e conter 3 modos: (1) emitir automático, (2) anexar manual, (3) não emitir. No modo automático, exibir as 3 opções de emissão (`individual_cnpj_padrao`, `individual_cnpj_manual`, `agrupado`) com seleção de serviços por checkbox. Garantir bloqueio visual de serviços já emitidos, formulário para campos adicionais opcionais, botão de voltar/corrigir, e painel de status por serviço. Em anexação manual, permitir upload PDF e vínculo por serviço. Em não emitir, registrar observação opcional. Seguir paleta: #eaeaec, #7aa2c9, #6a84aa, #354256. Não implementar regra de negócio no front; tudo deve ser chamado via API.

---

## Testes sem assinatura da API
Foram criados testes unitários para validar:
- montagem de payload/descritivo agrupado
- sanitização de documento
- modo mock do cliente PlugNotas sem dependência de assinatura ativa

---

## Observações finais
- Esta etapa é exclusivamente de back-end.
- Fluxos de UX e tela foram documentados para próxima fase.
- O endpoint de webhook aceita validação por segredo para segurança.
