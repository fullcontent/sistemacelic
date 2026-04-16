# PLAN: RAG Agentic Architecture (V2 - 4 Specialist Agents)
**Status:** Planejamento Detalhado
**Descrição:** RAG otimizado com 4 agentes especialistas rodando Llama 3 9B Local (Ollama) e multi-collections no banco vetorial.

---

## 🏗️ Atribuições de Especialistas (Agents)

| Especialista | Foco de Atuação |
| :--- | :--- |
| **Agente Operacional** | Localização, Status e IDs técnicos. |
| **Agente Normativo** | Regras de prefeitura e fluxos teóricos (O "Como"). |
| **Agente BI** | Médias de prazos e riscos regionais (O "Aprendizado"). |
| **Agente Campo** | Histórico narrativo e visitas de prestadores (A "Memória"). |

---

## 📋 Task Breakdown (Passo-a-Passo)

### Fase 1: Setup Infraestrutura e Vector DB
- [ ] Subir/configurar o serviço **Ollama** no servidor do Celic e fazer o pull estrutural (`nomic-embed-text`).
- [x] Provisionar o **Banco Vetorial** (Qdrant Local). (Iniciado: `historico` e `operacional`).
- [x] Criar estruturalmente as Collections na engine vetorial usando dimensão 768:
  - [x] `operacional` (Qdrant)
  - [x] `historico` (Qdrant)
  - [ ] `celic_knowledge_base`
- [ ] Cadastrar as Credenciais Base no n8n (Google Gemini API, Qdrant Localhost, Webhook e URL do Celic MySQL).

### Fase 2: Pipeline de Ingestão de Dados (Sync Laravel -> Vector)
- [ ] **Laravel:** Criar um Observer ou Action disparado no evento de `created`/`updated` dos Models de `Servico` e `HistoricoServico`.
- [ ] **Laravel:** Programar Webhook dispatcher para enviar o payload assíncrono pro n8n no exato momento da criação (`Event-Driven`).
- [ ] **n8n:** Criar Workflow Webhook de Indexador que recebe o payload, converte no nó Ollama, cria o metadado Json embutido e dispara UPSERT no vetor.
- [ ] **Laravel (Carga Inicial Múltipla):** Desenvolver script de `Chunk` otimizado para não travar a memória do servidor, lendo os 712k registros e enfilando em Jobs Background para submetê-los gentilmente à indexação local.

### Fase 3: Smart APIs (O Cérebro Computacional)
- [ ] Criar Middleware `VerifyN8nAuth` usando um Bearer Token gerado via variavéis de ambiente (Segurança máxima para evitar scraping da api rotineira).
- [ ] Criar Controller `RAGToolsController`.
- [ ] Desenvolver Rota da Tool 1: `GET /api/ai/tools/processos-count` (Aplicação de filtros `empresa_id`, `estado`, `status_id`, `fornecedor_id`).
- [ ] Desenvolver Rota da Tool 2: `GET /api/ai/tools/metricas-fornecedor` (Para responder: "quantas vezes X foi em Y").
- [ ] Ajustar o output de retorno JSON em linguagem clara (O Gemini entende o JSON, mas se chaves forem amigáveis, a latência de token reduz).

### Fase 4: O Coração RAG (n8n Agentic Workflow)
- [ ] Criar novo Workflow no n8n com Trigger do tipo "Webhook POST".
- [ ] Adicionar Node "AI Agent" equipado com o LLM da Gemini (ex: `gemini-1.5-pro` ou `gemini-1.5-flash` para custo/benefício e alta velocidade).
- [ ] Acoplar bloco de **Window Buffer Memory**. O valor do buffer (janela) deve considerar de 5 a 10 memórias, extraindo a SessionKey direto do JSON do front-end.
- [ ] Configurar **Ferramenta 1 (Vector Tool)** apontando para a *Knowledge Base* de regras regionais e Manuais (ex: fluxos em JSON de Campinas).
- [ ] Configurar **Ferramenta 2 (Vector Tool)** apontando para histórico do passado.
- [ ] Configurar **Ferramenta 3 (HTTP Request Tool)** interligada com as rotas criadas na Fase 3 do Laravel, documentando minuciosamente o Prompt de ferramenta ("Use isso para contar ocorrências de processo blabla").

### Fase 5: Integração do Frontend Celic
- [ ] **Blade/React:** Construção Visual de botão flutuante e container de Chatbot.
- [ ] Estruturar a geração do token Hash do `sessionId` com expiração à meia-noite (para reset diário de conversa).
- [ ] Disparo Assíncrono (`fetch` / `axios`) ao Endpoit n8n guardando estado Loading ("Digitando...").
- [ ] Instalar renderizador visual de Markdown para que a resposta formatada da AI se apresente quebrando linhas e renderizando listras numeradas na Interface de Bate-Papo.

---

## 🧠 Configurações de I.A. (Prompts e Ferramentas)

Para o Agente n8n funcionar com excelência, utilize as seguintes definições de Prompt:

### Prompt de Sistema (Base do Agente)
> "Você é o **Assistente Inteligente Celic**, especialista em gestão de licenciamentos e processos. Seu objetivo é ajudar usuários a analisar dados de serviços e históricos.
> 
> **Regras Cruciais:**
> 1. Se a pergunta for sobre contagens técnicas (Ex: 'Quantos processos...'), use obrigatoriamente a ferramenta `Celic_API_Counter`.
> 2. Se a pergunta for sobre métricas de performance (Ex: 'Quantas vezes Moabe foi...'), use `Celic_API_Metrics`.
> 3. Se a pergunta for sobre normas de prefeituras ou manuais, use a `Celic_Knowledge_Vector`.
> 4. Nunca invente números. Se a ferramenta retornar 0, diga que não há registros.
> 5. Responda sempre em Português (Brasil)."

### Configuração das Ferramentas (Tools no n8n)

#### 1. `Celic_API_Counter` (HTTP Request)
- **Descrição**: "Use para contar processos e serviços. Aceita filtros de empresa, estado, status e fornecedor."
- **Endpoint**: `GET /api/ai/tools/processos-count`
- **Parâmetros**: `empresa_id`, `estado` (sigla), `status_id`.

#### 2. `Celic_API_Metrics` (HTTP Request)
- **Descrição**: "Use para contar interações específicas de um prestador ou palavras-chave no histórico."
- **Endpoint**: `GET /api/ai/tools/metricas-fornecedor`

---

## 🗄️ Setup do Banco Vetorial (Collections)

### Opção A: Qdrant (CURL/API)
Execute este comando para criar cada uma das collections (`celic_servicos`, `celic_historicos`, `celic_knowledge_base`):

```bash
curl -X PUT "http://localhost:6333/collections/NOME_DA_COLLECTION" \
     -H "Content-Type: application/json" \
     -d '{
       "vectors": {
         "size": 768,
         "distance": "Cosine"
       }
     }'
```

### Opção B: Supabase (SQL via Editor)
Execute este script no SQL Editor do Supabase para habilitar o `pgvector` e criar as tabelas:

```sql
-- Ativar a extensão de vetores
create extension if not exists vector;

-- Criar tabela para uma collection (Repetir para as 3 alterando o nome)
create table celic_servicos (
  id uuid primary key default uuid_generate_v4(),
  content text, -- O texto original
  metadata jsonb, -- Dados como empresa_id, estado, etc
  embedding vector(768) -- Dimensão do nomic-embed-text
);

-- Criar índice para busca rápida
create index on celic_servicos using ivfflat (embedding vector_cosine_ops)
with (lists = 100);
```

---

## 🛡️ Plano de Verificação Detalhado

Este plano garante que cada camada da "ponte" RAG (Laravel -> n8n -> Vector DB -> Gemini) esteja funcionando corretamente.

### 1. Testes de Modelos e Observers (Laravel)
- [ ] **Captura de Evento**: Criar um novo `Servico` no banco e validar se o `Log::info` ou uma ferramenta de inspeção de rede mostra uma tentativa de POST para o `RAG_N8N_INDEX_WEBHOOK`.
- [ ] **Integridade de Payload**: Verificar se o JSON enviado pelo `HistoricoObserver` contém o campo `observacoes` completo e o `id` correto do serviço relacionado.
- [ ] **Performance de Carga**: Executar `php artisan rag:sync --type=servicos` para um lote de 1.000 registros e monitorar o uso de CPU/RAM do servidor (Ollama processando embeddings).

### 2. Testes de Vector Store (Supabase/Qdrant)
- [ ] **Conectividade**: Realizar uma busca manual (via n8n ou script) na collection `celic_knowledge_base` e garantir que o banco retorna resultados.
- [ ] **Similaridade Semântica**: Inserir um texto sobre "Alvará em Campinas" e buscar por "Como aprovar projeto em SP interior". O banco deve retornar o registro de Campinas por proximidade semântica.
- [ ] **Filtro de Metadados**: Realizar uma query vetorial filtrando por `metadata.estado == 'PE'`. Garantir que nenhum registro de outros estados retorne na lista.

### 3. Testes de API e Segurança (Laravel Smart APIs)
- [ ] **Acesso Negado**: Tentar acessar `/api/ai/tools/processos-count` sem o header `Authorization: Bearer ...`. Deve retornar **401 Unauthorized**.
- [ ] **Precisão Matemática**: Fazer a pergunta "Quantos processos Extrafarma em PE". Comparar o resultado do Chat com um `SELECT COUNT(*)` manual no banco MySQL. Os números devem ser idênticos.
- [ ] **Sanitização de Input**: Enviar caracteres especiais ou SQL Injection nos parâmetros da ferramenta de contagem e garantir que o Eloquent os trate com segurança.

### 4. Testes de Lógica RAG e Agentic (n8n + Gemini)
- [ ] **Tool Switching**: No Chat, perguntar primeiro algo de contagem (API Tool) e depois algo sobre regras (Vector Tool). Validar no n8n se o Agente soube alternar entre os nós corretamente.
- [ ] **Tratamento de Alucinação**: Perguntar sobre uma empresa que não existe. O Agente deve responder que não encontrou dados, em vez de inventar um número.
- [ ] **Memória de Curto Prazo**:
    1. Usuário: "Quem é o prestador Moabe?"
    2. Agente: "Moabe é um prestador de serviços..."
    3. Usuário: "Quantas vezes **ele** foi na prefeitura?" (O Agente deve entender que 'ele' refere-se ao Moabe do contexto anterior).

### 5. Testes de Frontend e UX
- [ ] **Persistência de Sessão**: Iniciar uma conversa, atualizar a página F5. As mensagens anteriores devem (ou não, dependendo da config) sumir, mas o `sessionId` deve ser o mesmo para o backend manter o contexto do dia.
- [ ] **Estado de Loading**: Validar se o ícone de "Digitando..." aparece imediatamente após o envio e desaparece somente após a resposta completa.
- [ ] **Markdown Rendering**: Enviar uma resposta da IA contendo tabelas ou listas e verificar se o widget renderiza o HTML corretamente (e não o texto cru com asteriscos).

---

## 🚀 Passos para Execução (Próximas Ações)

Para movimentar o plano hoje:

1. **Webhook Ingestor:** No n8n, configure o nó que recebe o POST do Laravel e o conecta ao Qdrant para os primeiros testes de Upsert.
2. **Carga de Teste:** Escolha um lote de 100 serviços e dispare a sincronização manual via script para validar a densidade dos embeddings no Qdrant.
3. **Segurança (Middleware):** Implementar o `VerifyN8nAuth` no Laravel para garantir que as ferramentas de BI/Contagem não fiquem expostas.
