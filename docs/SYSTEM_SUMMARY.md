# Sistema Celic: Resumo do Sistema

## 📋 Visão Geral
O **Sistema Celic** é uma plataforma de gestão corporativa focada em **Licenciamentos**, **Serviços** e **Burocracia Técnica**. Ele permite que empresas com diversas unidades físicas (lojas, centros de distribuição, etc.) gerenciem alvarás, licenças de bombeiros, vigilância sanitária e outros documentos regulatórios em uma interface unificada.

## 🏗️ Arquitetura Técnica
- **Backend**: Laravel (PHP) robustamente configurado para gestão de ativos.
- **Frontend**: Combinação de Laravel Blade para o painel administrativo e React para componentes interativos de alta performance (ex: Timeline).
- **Banco de Dados**: MySQL (Relacional) com integração futura em Banco Vetorial (Qdrant/Supabase).
- **Automação**:
    - **n8n**: Orquestração de workflows asíncronos (Embeddings, Changelogs, Sincronização).
    - **GitHub Actions**: CI/CD e integração com Todoist para backlog de tarefas.

## 🏢 Entidades de Domínio
1. **Empresa**: O cliente "pai" (ex: Extrafarma, RaiaDrogasil).
2. **Unidade**: O local físico onde o serviço é prestado.
3. **Serviços**: A tarefa principal (ex: "Licença de Operação").
4. **Pendências**: Atividades granulares para conclusão do serviço (ex: "Solicitar IPTU").
5. **Taxas**: Gestão de pagamentos associados ao licenciamento.

## 🧠 Integração com I.A. (RAG)
O sistema está sendo expandido para incluir um assistente inteligente baseado em RAG (Retrieval-Augmented Generation), permitindo:
- Consultas de status em linguagem natural.
- Análise de históricos de interações passadas.
- Base de conhecimento sobre regras regionais de prefeituras e órgãos públicos.
