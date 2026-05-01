# Análise de Código — SistemaCelic2

## Módulo: Auth
Responsável pela autenticação, autorização e controle de acesso granular.

### 1. Componentes Técnicos
- **Controllers:** `LoginController`, `RegisterController`, `ForgotPasswordController`, `ResetPasswordController`.
- **Middleware:** `Authenticate` (garante sessão), `RedirectIfAuthenticated` (redireciona logados).
- **Models:** `User`, `UserAccess`.

### 2. Lógica de Redirecionamento Pós-Login
O sistema utiliza o campo `privileges` na tabela `users` para determinar o dashboard de destino:
- Se `privileges == 'admin'`: Redireciona para `admin/home`.
- Caso contrário: Redireciona para `cliente/home`.

### 3. Matriz de Acesso Granular
Diferente de um RBAC tradicional, o sistema utiliza a tabela `user_accesses` para vincular usuários a instâncias específicas de:
- **Empresas:** Relacionamento N:N via `UserAccess`.
- **Unidades:** Relacionamento N:N via `UserAccess`.

Isso permite que um usuário do tipo `cliente` visualize apenas os dados das empresas e unidades que lhe foram explicitamente atribuídas.

### 4. Notificações
O modelo `User` está integrado ao sistema de notificações do Laravel (`Notifiable`), possuindo inclusive uma notificação customizada `UserMentioned` e suporte para reset de senha via e-mail.

### 5. Confiança
- **Estrutura de dados:** 🟢 CONFIRMADO (extraído de `User.php` e `UserAccess.php`)
- **Fluxo de Login:** 🟢 CONFIRMADO (extraído de `LoginController.php`)
- **Regras de Negócio:** 🟢 CONFIRMADO (extraído das rotas e controladores)
 
## Módulo: Dashboard
Centraliza indicadores de performance, geolocalização de clientes e auditoria de atividades.

### 1. Componentes Técnicos
- **Controllers:** `DashboardController`, `AdminController` (index).
- **Principais Indicadores:**
  - **Serviços a Vencer:** Filtro de validade de licença < 60 dias.
  - **Atividade de Usuários:** Ranking baseado na contagem de registros em `historicos`.
  - **Geolocalização:** Integração com `positionstack API` para geocodificação de endereços de unidades.

### 2. Lógica de Relatórios
O sistema possui uma engine robusta de geração de CSVs em `AdminController`:
- Usa `cursor()` (Lazy Loading) para evitar estouro de memória em relatórios grandes (ex: `completoCSV`).
- Geração via `StreamResponse` para download imediato.
- Formatação pesada de dados em tempo de execução (conversão de IDs para nomes, formatação de datas Carbon).

### 3. Timeline e Auditoria
A auditoria é feita via entidade `Historico`, que registra observações textuais vinculadas a um serviço e um usuário. O dashboard utiliza esses dados para montar uma timeline de interações.

### 4. Confiança
- **Indicadores:** 🟢 CONFIRMADO (extraído de `AdminController.php` e `DashboardController.php`)
- **Geolocalização:** 🟢 CONFIRMADO (API key e endpoint identificados)
- **Performance (CSVs):** 🟢 CONFIRMADO (uso de cursores identificado)

## Módulo: Faturamento
Gerencia o ciclo de cobrança de serviços, permitindo faturamentos parciais e agrupados por empresa.

### 1. Componentes Técnicos
- **Controllers:** `FaturamentoController`.
- **Wizard de Faturamento:** Processo em 4 etapas (Seleção -> Filtro -> Revisão -> Confirmação).
- **Entidades Core:** `Faturamento` (lote), `FaturamentoServico` (vínculo), `ServicoFinanceiro` (estado do serviço), `DadosCastro` (emissora).

### 2. Regras de Negócio de Faturamento
O sistema suporta faturamento granular:
- **Faturamento Parcial:** Um serviço pode ser faturado em múltiplas parcelas vinculadas a diferentes lotes de faturamento.
- **Gestão de Saldo:** A entidade `ServicoFinanceiro` mantém o rastreio de `valorTotal`, `valorFaturado` e `valorAberto` em tempo real.
- **Status Financeiro:** Transições automáticas de status (`aberto` -> `parcial` -> `faturado`) conforme os lotes são confirmados.
- **Controle de NF:** As Notas Fiscais podem ser informadas tanto no nível do lote (`Faturamento`) quanto no nível do serviço individual.

### 3. Integração com Serviços
O faturamento depende da conclusão (ou arquivamento) dos serviços. A lógica de filtragem no Step 2 prioriza serviços finalizados dentro do período selecionado, mas permite flexibilidade baseada em datas de criação.

### 4. Confiança
- **Fluxo Wizard:** 🟢 CONFIRMADO (extraído de `FaturamentoController.php`)
- **Cálculo Financeiro:** 🟢 CONFIRMADO (lógica de `atualizarFinanceiro` analisada)
- **Modelagem de Dados:** 🟢 CONFIRMADO (extraído dos modelos Eloquent)

## Módulo: Ordem de Serviço
Gerencia o ciclo de vida de ordens de serviço (OS) atribuídas a prestadores, incluindo controle de pagamentos parcelados e avaliação de qualidade.

### 1. Componentes Técnicos
- **Controllers:** `OrdemServicoController`.
- **Models:** `OrdemServico`, `OrdemServicoPagamento`, `OrdemServicoVinculo`.
- **Relacionamentos Principais:**
    - `OrdemServico` -> `Prestador` (Proprietário da execução).
    - `OrdemServico` -> `Servico` (Vínculo N:N via `OrdemServicoVinculo`).
    - `OrdemServico` -> `OrdemServicoPagamento` (Controle de parcelas e comprovantes).

### 2. Ciclo de Pagamento e Parcelamento
- **Geração de Parcelas:** Criação múltipla de registros em `ordem_servico_pagamentos` com datas de vencimento individuais.
- **Gestão de Comprovantes:** Upload de arquivos vinculados a cada parcela.
- **Status Automático:** Uma parcela transiciona para `pago` automaticamente quando um comprovante é anexado.

### 3. Sistema de Avaliação (Rating)
- **Cálculo de Mediana:** O sistema utiliza a mediana das avaliações para calcular o rating do prestador, evitando distorções por outliers.
- **Entidade de Comentário:** Armazenado em `PrestadorComentario`.

## Módulo: Prestadores
Gerencia o cadastro e a qualificação de prestadores de serviço.

### 1. Componentes Técnicos
- **Controllers:** `PrestadorController`.
- **Models:** `Prestador`, `PrestadorComentario`.
- **Destaques:**
    - **Geografia de Atuação:** Armazenamento de cidades em JSON no campo `cidadeAtuacao`.
    - **Dados Bancários:** Cadastro completo de PIX e contas bancárias.

## Módulo: Proposta
Automatiza a elaboração comercial e a conversão de propostas em serviços reais.

### 1. Componentes Técnicos
- **Controllers:** `PropostasController`.
- **Models:** `Proposta`, `PropostaServico`.
- **Lógica de Conversão:** A aprovação gera automaticamente serviços, financeiro, histórico e pendências iniciais.
- **Indicadores:** Taxa de conversão e dias em análise.

## Módulo: Reembolso
Processa despesas pagas pela Celic a serem reembolsadas pelos clientes.

### 1. Componentes Técnicos
- **Controllers:** `ReembolsoController`.
- **Models:** `Reembolso`, `ReembolsoTaxa`.
- **Empacotamento:** Geração de arquivo ZIP com relatório PDF e todos os comprovantes/boletos organizados.
- **Segurança de ID:** IDs de reembolso são mascarados (ID + 1000).

## Módulo: Portal do Cliente
Interface dedicada para usuários externos (tipo `cliente`).

### 1. Componentes Técnicos
- **Controllers:** `ClienteController`.
- **Segurança:** Acesso filtrado via `user_accesses`.
- **Interação:** Sistema de menções com resumos gerados por IA e notificações via Webhook.

## Confiança Final
- **Lógica de Conversão:** 🟢 CONFIRMADO
- **Acesso Cliente:** 🟢 CONFIRMADO
- **Gestão Financeira:** 🟢 CONFIRMADO
- **Algoritmos (Mediana/ZIP):** 🟢 CONFIRMADO
