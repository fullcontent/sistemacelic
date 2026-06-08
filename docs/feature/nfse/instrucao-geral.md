# Instrução geral (cliente e time não técnico)

## 1) O que é esta funcionalidade

Esta funcionalidade permite emitir Nota Fiscal de Serviço Eletrônica (NFS-e) pelo sistema, usando a API da **TecnoSpeed PlugNotas**.

Ela foi pensada para reduzir trabalho manual e evitar preencher os mesmos dados repetidamente.

---

## 2) O que foi entregue nesta fase

✅ Back-end pronto para:
- Salvar configurações de emissão por empresa emitente.
- Emitir NFS-e automaticamente em 3 modos.
- Anexar NF manualmente (PDF e número).
- Registrar “não emitir/anexar”.
- Receber retorno por webhook.
- Gerar ZIP com documentos de NF.

⚠️ Importante:
- **Não foi feito front-end nesta fase.**
- O uso será por API até a tela ficar pronta.

---

## 3) Antes de usar: contratar e configurar a PlugNotas

### Passo 1 — Contratar a PlugNotas

1. Acesse o site da TecnoSpeed.
2. Contrate o produto PlugNotas com cobertura para seus municípios.
3. Conclua cadastro e validações da conta.

Links úteis:
- Site TecnoSpeed: https://tecnospeed.com.br
- Site PlugNotas: https://plugnotas.com.br
- Central de ajuda PlugNotas: https://atendimento.tecnospeed.com.br/hc/pt-br/categories/360003705153-PlugNotas

### Passo 2 — Obter credenciais

Após contratar, você precisará de:
- Chave/token da API.
- Configuração de webhook.
- Segredo do webhook (recomendado para segurança).

### Passo 3 — Informar credenciais no sistema

A equipe técnica deve configurar estas variáveis no ambiente:

- `PLUGNOTAS_BASE_URL=https://api.plugnotas.com.br`
- `PLUGNOTAS_API_KEY=...`
- `PLUGNOTAS_TIMEOUT=180`
- `PLUGNOTAS_MOCK_MODE=true|false`
- `PLUGNOTAS_WEBHOOK_SECRET=...`

Recomendação:
- Em homologação/validação inicial: `PLUGNOTAS_MOCK_MODE=true`.
- Em produção real: `PLUGNOTAS_MOCK_MODE=false` com chave válida.

---

## 4) Como a funcionalidade funciona (visão simples)

Após um faturamento, haverá 3 escolhas:

1. **Emitir NF automaticamente**
2. **Anexar NF manualmente**
3. **Não emitir/anexar NF**

### 4.1 Emissão automática

Você pode escolher:

#### Opção A — Emitir NF individual com CNPJ padrão
- Cada serviço gera sua própria nota.
- CNPJ vem automaticamente do cadastro (unidade/serviço).

#### Opção B — Emitir NF individual com CNPJ manual
- Cada serviço gera sua própria nota.
- O usuário informa manualmente o CNPJ para cada serviço.

#### Opção C — Emitir NF agrupada
- Vários serviços entram em uma única nota.
- A descrição é montada automaticamente listando serviços.
- O valor total da nota é a soma dos serviços selecionados.
- CNPJ informado manualmente.

### 4.2 Anexar NF manualmente

Quando a nota é emitida fora do sistema:
- informar número da NF;
- anexar PDF;
- vincular aos serviços.

### 4.3 Não emitir/anexar

Quando cliente final não exige nota:
- registrar sem NF naquele momento;
- permitir emissão/anexação posterior.

---

## 5) Regras importantes para evitar erro

- O sistema evita duplicidade de vínculo (mesmo serviço em mais de uma nota).
- Campos automáticos da emissão seguem o solicitado:
  - data competência = dia atual;
  - indicador municipal, telefone e e-mail = em branco.
- Campos opcionais adicionais podem ser enviados quando necessário.

---

## 6) Endereços de API (uso técnico-operacional)

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

### Retornos e arquivos
- `POST /api/nfse/webhooks/plugnotas`
- `GET /api/nfse/emissoes/{emissionId}/zip`

---

## 7) Exemplo de uso (simples)

### Emitir automático (individual, CNPJ padrão)

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

### Emitir automático (agrupado)

```json
{
  "faturamento_id": 123,
  "opcao_automatica": "agrupado",
  "servico_ids": [10, 11, 12],
  "cnpj_manual_agrupado": "12.345.678/0001-99"
}
```

### Anexar manual

`multipart/form-data`:
- `faturamento_id`
- `numero_nf`
- `servico_ids[]`
- `arquivo_pdf` (opcional)

---

## 8) Prompt para IA construir o front-end (etapa futura)

> Crie o front-end da área “Emissão de NFS-e” consumindo as APIs em `/api/nfse`. A tela deve aparecer após finalizar faturamento e oferecer 3 modos: (1) emitir automático, (2) anexar manual, (3) não emitir. No modo automático, oferecer: `individual_cnpj_padrao`, `individual_cnpj_manual`, `agrupado`. Exibir lista de serviços com seleção por checkbox; bloquear visualmente serviços já emitidos; permitir campos adicionais opcionais; incluir ação voltar/corrigir; mostrar status da emissão por serviço e por lote. No modo manual, permitir informar número da NF e anexar PDF com vínculo dos serviços. No modo não emitir, registrar observação opcional. Seguir paleta: cinza #eaeaec, azul claro #7aa2c9, azul intermediário #6a84aa, azul escuro #354256. Não reimplementar regra fiscal no front-end: toda regra vem da API.

---

## 9) Perguntas frequentes (FAQ)

### “Sem assinatura da PlugNotas já funciona?”
Sim, para validação técnica em modo mock (`PLUGNOTAS_MOCK_MODE=true`).

### “Quando a nota vira oficial?”
Quando estiver em produção, com credencial válida e retorno real da API.

### “Podemos corrigir uma emissão?”
Sim, o fluxo foi preparado para suportar ajustes/substituições no processo operacional.

### “Essa documentação serve para usuários sem perfil técnico?”
Sim. O objetivo deste documento é justamente orientar público administrativo/operacional.

---

## 10) Checklist rápido de entrada em produção

- [ ] Conta PlugNotas ativa.
- [ ] Token de API gerado.
- [ ] Webhook apontando para o endpoint correto.
- [ ] Segredo de webhook configurado.
- [ ] `PLUGNOTAS_MOCK_MODE=false`.
- [ ] Emissão de teste aprovada.
- [ ] Time operacional orientado.
