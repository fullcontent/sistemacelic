# RAG Architecture: 4 Specialized Agents (Local Llama 3 9B)

Este documento detalha a estrutura de 4 agentes especializados para o RAG do Sistema Celic, focando em precisão, economia de tokens e organização de dados em multi-collections.

---

## 🏛️ Estrutura de Dados (Multi-Collections)

Sim, a estratégia de **Multi-Collections** é a melhor opção. Misturar 12 mil serviços com 700 mil históricos criaria um "mar de dados" onde a busca semântica ficaria imprecisa. 

### Collections Recomendadas (Vector DB):
1. `operacional` (Qdrant): Metadados técnicos (ID, Código Loja, Cidade, Status).
2. `celic_normativo`: PDFs de manuais, leis e exigências de prefeituras.
3. `celic_bi`: Padronização de prazos médios, riscos e métricas de performance.
4. `historico` (Qdrant): Relatos narrativos de visitas e interações de prestadores.

---

## 🗄️ Estratégia de Bancos de Dados (Routing)

Para garantir precisão (zero alucinações) e velocidade, nem tudo deve ir para o banco vetorial. A melhor arquitetura usa o banco certo para a tarefa certa:

### 1. MySQL Existente (Obrigatório para Matemática/Exatidão)
Bancos vetoriais são péssimos para contagens exatas (`COUNT`) ou buscas por IDs exatos.
- **Uso ideal:** Agente **Operacional** e Agente de **BI**.
- **Como funciona:** O n8n não busca no vetor. Ele usa a ferramenta `HTTP Request` para chamar uma API do seu Laravel (ex: `/api/ai/servico/12676` ou `/api/ai/metricas/prazos`). O Laravel roda uma query MySQL tradicional, tira a média/contagem exata e devolve o JSON pro Llama 3 apenas ler e responder.
- **Resultado:** 100% de exatidão ao perguntar "Quantas visitas..." ou "Qual o status do ID X".

### 2. Qdrant (O Campeão Semântico)
Qdrant é construído do zero com Rust *apenas* para vetores. É extremamente leve e rápido.
- **Uso ideal:** Agente **Normativo**.
- **Como funciona:** Você sobe os manuais, PDFs e regras de prefeituras nele. Quando o usuário pergunta "Qual a regra em Campinas?", o Qdrant acha o parágrafo exato por similaridade de sentido (mesmo que a pessoa use palavras diferentes).

### 3. Supabase / pgvector (O Melhor dos Dois Mundos)
Supabase é PostgreSQL. O `pgvector` permite busca vetorial, mas com o poder de filtros relacionais.
- **Uso ideal:** Agente de **Campo** (Os 700 mil históricos).
- **Como funciona:** Você precisa de Busca Híbrida. Quando o usuário pergunta "O que o Moabe fez na unidade 1457?", o Supabase permite que a I.A. faça um filtro exato (`WHERE loja_id = 1457`) e, *dentro desse filtro*, faça a busca semântica pelo texto do histórico. Fazer isso no Qdrant exige metadados complexos; no Supabase é natural.

**RESUMO DA ARQUITETURA DE BANCOS:**
- **Operacional e BI** ➔ MySQL (Via APIs Laravel estruturadas).
- **Normativo** ➔ Qdrant (Para leitura rápida de Manuais e Leis).
- **Campo (Histórico)** ➔ Supabase (Para lidar com os 700k registros filtrando por IDs antes de ler o texto).

---

## 🔄 Estratégia de Atualização Contínua (Sync Laravel -> Vector DB)

Fazer a carga inicial (Batch Insert) é apenas o passo 1. Como garantir que as I.As não fiquem com dados velhos quando um serviço mudar de status amanhã?

A arquitetura resolve isso com **Sincronização Orientada a Eventos (Event-Driven UPSERT)**:

1. **Os "Espiões" no Laravel (Observers)**:
   Já criamos o `ServicoObserver` e o `HistoricoObserver`. Sempre que um colaborador clicar em "Salvar" ou mudar o status de um serviço no sistema, o Observer detecta a mudança na mesma hora.
2. **O Gatilho (Webhook)**:
   O Observer do Laravel empacota esse único serviço atualizado (em JSON) e dispara um `POST` para o Webhook de Ingestão do n8n (em background, para não travar a tela do usuário).
3. **A Mágica no n8n (UPSERT)**:
   No nó do Vector Store (Supabase) no n8n, a operação configurada **não é** `Insert`, mas sim `Upsert` (Update or Insert). O n8n vai olhar para o `id_original` do serviço alterado:
   - *Se o ID já existir no Supabase:* Ele sobrescreve o texto antigo e atualiza o vetor com os novos dados.
   - *Se for um serviço recém-criado:* Ele insere como um novo registro.

**Dica de Ouro no Supabase**: Para que o UPSERT funcione perfeitamente, certifique-se de que a coluna `id` na tabela `celic_servicos` do Supabase receba o exato mesmo número do ID do MySQL, e não um UUID aleatório gerado pelo Postgres.

---

## 🤖 Modelos de Agentes e Prompts

### 1. Agente OPERACIONAL (O "Onde")
**Objetivo**: Localizar serviços por ID, Código, Endereço ou Cidade e retornar o status.
**Prompt (Llama 3 9B)**:
> "Você é o Agente Operacional do Celic. Sua função é extrair dados técnicos exatos. 
> Se o usuário perguntar por um ID, ignore o código da loja. Se perguntar por código de loja, ignore o ID.
> **SAÍDA OBRIGATÓRIA EM JSON**: 
> {
>   \"tipo_agente\": \"operacional\",
>   \"servico_id\": number,
>   \"codigo_loja\": \"string\",
>   \"status\": \"string\",
>   \"localizacao\": { \"endereco\": \"string\", \"cidade\": \"string\", \"uf\": \"string\" },
>   \"processo\": \"string\"
> }"

### 2. Agente NORMATIVO (O "Como")
**Objetivo**: Explicar regras de prefeituras e documentação necessária.
**Prompt (Llama 3 9B)**:
> "Você é o Agente Normativo. Sua base são manuais e leis municipais. 
> Foque em documentação e fluxos teóricos.
> **SAÍDA OBRIGATÓRIA EM JSON**:
> {
>   \"tipo_agente\": \"normativo\",
>   \"municipio\": \"string\",
>   \"orgao\": \"string\",
>   \"exigencias\": [\"string\"],
>   \"fluxo_teorico\": \"string\"
> }"

### 3. Agente BI / INTELIGÊNCIA (O "Aprendizado")
**Objetivo**: Analisar médias de prazos e identificar riscos em regiões.
**Prompt (Llama 3 9B)**:
> "Você é o Agente de Inteligência. Analise os padrões e médias nos dados fornecidos.
> **SAÍDA OBRIGATÓRIA EM JSON**:
> {
>   \"tipo_agente\": \"bi_inteligencia\",
>   \"assunto\": \"string\",
>   \"prazo_medio\": \"string\",
>   \"riscos\": [\"string\"],
>   \"recomendacoes\": [\"string\"]
> }"

#### 🧠 Pipeline de Ingestão de Conhecimento (Engenheiro de Dados AI)
Antes de salvar dados complexos na collection `celic_bi`, um nó de LLM no n8n (atuando como pré-processador) deve ler o JSON bruto do serviço e destilá-lo usando este excelente prompt:

> **Role**: Engenheiro de Conhecimento Sênior.
> **Tarefa**: Extrair fatos de conhecimento estruturados e reutilizáveis do JSON para a Vector Store.
> **Diretrizes**:
> 1. **Generalize**: Transforme dados pontuais em regras de negócio gerais (ex: "Em [Cidade], o prazo é X").
> 2. **Categorize**: Separe fatos em Operacional, Financeiro e Escopo.
> 3. **Rastreabilidade**: Mantenha o `id_original` do serviço para busca e auditoria posterior.
> 4. **Riscos**: Identifique gargalos ou erros recorrentes no histórico e crie uma checklist preventiva.
> 5. **Restrição Absoluta**: Responda APENAS com o JSON válido. Sem formatação markdown, sem texto antes ou depois. Tom direto, técnico e sem verbosidade (evite "sugere-se", "pode ser").
> 
> **Saída Esperada (JSON)**:
> {
>   "id_original": number,
>   "contexto_regional": { "municipio": "string", "orgao": "string", "regras": ["string"] },
>   "benchmarks_de_performance": { "prazo_dias": number, "gargalos": ["string"] },
>   "parametros_financeiros": { "taxas_comuns": ["string"] },
>   "regras_de_escopo_extraidas": ["string"],
>   "alertas_estrategicos": ["string"]
> }

### 4. Agente CAMPO (A "Memória")
**Objetivo**: Contar interações, visitas e narrativas do histórico.
**Prompt (Llama 3 9B)**:
> "Você é o Agente de Campo. Sua função é ler o histórico narrativo e contar ocorrências.
> Ex: Se Moabe foi 3 vezes na prefeitura, reporte isso.
> **SAÍDA OBRIGATÓRIA EM JSON**:
> {
>   \"tipo_agente\": \"campo\",
>   \"prestador\": \"string\",
>   \"contagem_visitas\": number,
>   \"historico_relevante\": \"string\",
>   \"conclusao_narrativa\": \"string\"
> }"

#### 🔄 Pipeline de Tratamento (n8n Code Node) para Agente Campo
Para que o Llama 3 entenda o histórico sem "alucinar", o JSON bruto do Laravel **não deve** ir direto para o banco vetorial. Ele passa por este script Javascript no n8n para gerar um contexto limpo e cronológico (`search_text`) e extrair metadados para filtros (`meta`):

```javascript
const cleanHtml = (html) => {
  if (!html) return "";
  return html
    .replace(/<img[^>]*>/g, '[IMAGEM]')
    .replace(/<[^>]*>?/gm, '')
    .replace(/&nbsp;/g, ' ')
    .replace(/\s+/g, ' ').trim();
};

const items = $input.all();
const output = [];

for (const item of items) {
  const raw = item.json;

  // 1. Narrativa do Histórico (Cronológico)
  const linhaDoTempo = (raw.historico || [])
    .reverse() 
    .map(h => `[${h.created_at}] - ${cleanHtml(h.observacoes)}`)
    .join("\n");

  // 2. Detalhes das Pendências
  const analisePendencias = (raw.pendencias || [])
    .map(p => `Pendência: ${p.pendencia} | Status: ${p.status} | Obs: ${cleanHtml(p.observacoes)}`)
    .join("\n");

  // 3. Resumo Executivo
  const contextoIA = `
    CLIENTE: ${raw.empresa?.nomeFantasia} | UNIDADE: ${raw.unidade?.nomeFantasia} (${raw.unidade?.cidade}/${raw.unidade?.uf})
    SERVIÇO: ${raw.nome} (OS: ${raw.os}) | SITUAÇÃO: ${raw.situacao}
    ESCOPO TÉCNICO: ${cleanHtml(raw.escopo)}
    
    LINHA DO TEMPO E INTERAÇÕES:
    ${linhaDoTempo}

    DETALHAMENTO DE PENDÊNCIAS:
    ${analisePendencias}
    
    FINANCEIRO: Valor R$ ${raw.financeiro?.valorTotal} | Status: ${raw.financeiro?.status}
  `.trim();

  output.push({
    json: {
      id_original: raw.id,
      os: raw.os,
      search_text: contextoIA, // O texto vetorizado para o Supabase
      meta: { // Filtros para Busca Híbrida
        unidade: raw.unidade?.nomeFantasia,
        cidade: raw.unidade?.cidade,
        uf: raw.unidade?.uf,
        data_finalizado: raw.licenca_emissao
      }
    }
  });
}
return output;
```
*Esse formato garante que a I.A encontre exatamente quem fez o quê, resolvendo perguntas como: "Quantas vezes o Moabe foi na unidade de Parnamirim?".*

---

## ⚙️ Configuração Adicional
- **LLM Local**: Ollama rodando Llama 3.1 8B ou 9B.
- **n8n Orchestration**: Um "Router" inicial (Node If ou Switch) que classifica a pergunta do usuário antes de enviar para o agente específico. Isso poupa tokens de sistema e evita confusão.

---

## 🛠️ Passos para Execução (Próximas Ações)

Para testar a lógica dos agentes:

1. **Testar Prompt Operacional:** No n8n, insira um exemplo de JSON de serviço e veja se o Gemini consegue extrair os campos corretamente para o `operacional`.
2. **Refinar Prompt de Campo:** Use os dados da collection `historico` do Qdrant para validar se a I.A. consegue contar visitas sem alucinar.
3. **Simular Router:** Criar o nó de classificação inicial no n8n (ex: "Contagem" -> Operacional, "Regra" -> Normativo).
