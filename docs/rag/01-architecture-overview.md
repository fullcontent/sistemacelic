# Projeto RAG Celic - Arquitetura e Passo-a-Passo

## 1. Visão Geral da Arquitetura (Agentic RAG)

Esta arquitetura resolve o problema matemático e analítico combinando as forças do **n8n como Agente Inteligente**, o **Gemini** para raciocínio em nuvem e o **Laravel** para consultas SQL pesadas, deixando o **Banco Vetorial** estritamente para busca semântica de textos.

### Stack Tecnológica Definida:
- **Orquestrador:** n8n (Gerencia o fluxo, os agentes e a memória).
- **Inference LLM (Cérebro do Chat):** API do Google Gemini (Garante performance, respostas rápidas e capacidade de tool-calling).
- **Ingestion & Embeddings LLM:** Ollama Local (`nomic-embed-text` para Embeddings e `llama3.1` ou `gemma3b` para sumarização batch de textos longos antes do insert). Custo zero de processamento.
- **Banco Vetorial:** Supabase (pgvector) ou Qdrant.
- **Escopo Inicial:** Textos do MySQL (Interações, Andamentos, Históricos descritivos) e uma robusta Base de Conhecimento Estruturada em JSON (Manuais Regionais, Regras de Prefeituras).
- **Memória:** Limitada ao dia. O n8n usará um `Window Buffer Memory` atrelado a um Session ID (ex: gerado no frontend pelo dia/usuário e descartado no dia seguinte).

---

## 2. Passo a Passo de Implementação

### Fase 1: Preparação de Infraestrutura e Banco Vetorial
1. **Ativar o Ollama Localmente:** 
   - Fazer pull e rodar o modelo de embeddings (ex: `ollama run nomic-embed-text`).
   - Fazer pull do modelo auxiliar se necessitar limpezas lexicais (`ollama run gemma:2b` ou `llama3.1`).
2. **Configuração do Vector DB (Supabase/Qdrant):**
   - Criar o cluster/projeto.
   - Criar 3 *collections* (Qdrant) ou *tables* (Supabase) separadas:
     1. `celic_servicos`
     2. `celic_historicos`
     3. `celic_knowledge_base` (Para indexar seus fantásticos arquivos JSON de regras e alertas regionais, como o de Campinas).
   *Nota: Passar a dimensão correta exigida pelo modelo de embedding local (geralmente 768 dimensões com nomic ou mxbai).*
3. **Credenciais no n8n:**
   - Adicionar a credencial do **Google Gemini Chat API**.
   - Adicionar a conexão do **Ollama** (apontando pro IP do localhost).
   - Adicionar a credencial do banco de dados (MySQL Celic) e do Vetorial (Supabase/Qdrant).

### Fase 2: O Pipeline de Ingestão (ETL Local Custo Zero)
*Objetivo: Alimentar o banco vetorial sem custos usando LLM local para conversões.*
1. **Criar Workflow de Sincronização no n8n:**
   - **Trigger:** Schedule Trigger (rodar a cada 1 hora) ou via Webhook (acionado diretamente pelo Evento do Laravel quando uma interação é salva).
   - **Node DB:** Buscar `interacoes` e `historicos` que ainda não foram vetorizadas.
   - **Node Ollama Embeddings:** Conecta ao texto cru e gera a matriz numérica (representação vetorial).
   - **Node Vector Store (Supabase/Qdrant):** Insere o vetor junto com *Metadata* forte em formato JSON. Exemplo:
     ```json
     {
       "processo_id": 123,
       "empresa": "Extrafarma",
       "estado": "PE",
       "tipo_registro": "andamento"
     }
     ```

### Fase 3: Smart APIs no Laravel (As Ferramentas de Contagem)
*Objetivo: Conceder o super-poder do Sistema Relacional para obter o número exato, sem o n8n "alucinar" lendo relatórios gigantes vetoriais.*
1. **Endpoint `GET /api/ai/tools/processos-count`**
   - Recebe filtros opcionais via query param: `?empresa=Extrafarma&estado=PE`.
   - Executa a query Eloquent para devolver um JSON apenas com a contagem exata e status numérico de processos ativos.
2. **Endpoint `GET /api/ai/tools/idas-prefeitura-count`**
   - Recebe filtros: `?fornecedor=Moabe&empresa=Pague Menos`.
   - Consulta a tabela de serviços/interações em grupo para retornar a taxa de visitas realizadas.
3. *Segurança:* Proteger essas e outras futuras API Tools com uma chave Bearer (ex: `API_KEY_N8N`), validada por um Middleware simples do Laravel, para acesso exclusivo do servidor do seu orquestrador.

### Fase 4: O Workflow Principal do Chatbot (AI Agent no n8n)
*Objetivo: Receber a pergunta do front-end e raciocinar as decisões dinamicamente usando o Gemini.*
1. **Trigger:** Webhook POST `/chat` (O Laravel ou o Front-end enviam a `pergunta` digitada e o hash de sessão `sessionId`).
2. **Node AI Agent:**
   - LLM: **Google Gemini Chat Model** (via API REST nativa ou LangChain Gemini Node).
   - Prompt Raiz (System Message): *"Você é o assistente inteligente do sistema Celic. Responda o usuário de forma concisa. IMPORTANTE: Para contagens ou perguntas matemáticas estruturadas de processos e serviços, ACIONE ESTRITAMENTE as rotas Tools do Laravel que lhe foram passadas. Para dúvidas contratuais, normais e textuais de contexto do sistema, use a Tool Vector Store."*
3. **Memory Node:**
   - `Window Buffer Memory` configurado para link exclusivo de variáveis usando a `sessionId`. Esta memória salva localmente no banco do n8n as ultimas transações por sessão de dia.
4. **Tools Node (As "Garras" do AI Agent):**
   - **Tool 1 (Busca Base de Conhecimento):** Vector Store plugado na collection `celic_knowledge_base`. O Agente usa proativamente para responder regras, alertas, fluxos presenciais ou "Como fazer" (Ex: *Como aprovar Alvará em Campinas? Quais as taxas?*).
   - **Tool 2 (Busca Históricos/Serviços):** Vector Store plugado nas bases do MySQL. (Acionado para recuperar memórias textuais: *o que aconteceu na observação do processo X?*).
   - **Tool 3 (Contar Processos Exato):** Nó Custom Tool com `HTTP Request` na porta `/api/ai/tools/processos-count` do seu servidor Celic.
   - **Tool 4 (Contar Visitas Frequentes):** Nó Custom Tool com `HTTP Request` direcionado à porta de análise de fornecedor.
5. **Final Output:** O agente constrói a frase falada (ex: *"Temos em andamento 15 processos em PE..."*) baseada na execução invisível do HTTP, e joga isso pro `Webhook Response`.

### Fase 5: Integração Frontend (Laravel / Blade ou React)
1. **UI do Chat:** Componente amarrado no canto da tela (similar a um help-desk).
2. **Geração do Session ID:** Cada dia ganha um número distinto por usuário. Ex: MD5 do `date() + user_id`. Para que hoje o chat saiba o que ele disse hoje cedo, mas amanhã ignore.
---

## 3. Passos para Execução (Próximas Ações)

Para consolidar esta arquitetura, siga estas etapas:

- [ ] **Validar Conectividade Ollama:** Garanta que o serviço está acessível pelo n8n (geralmente via IP local).
- [ ] **Configurar Credenciais no n8n:** Adicionar as chaves de API do Gemini e as URLs de conexão dos bancos Qdrant já criados (`historico` e `operacional`).
- [ ] **Definir Chave de API Celic-n8n:** Criar uma `API_KEY` no `.env` do Laravel para proteger os endpoints das ferramentas (Tools).
- [ ] **Mapeamento de Collections:** Confirmar se a collection `operacional` no Qdrant seguirá o esquema sugerido na Fase 2 (metadados estruturados).
