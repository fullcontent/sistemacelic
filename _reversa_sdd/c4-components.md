# C4 Model: Diagrama de Componentes (Nível 3)

Focando no container da **Aplicação Web Monolítica (Laravel)**, este diagrama detalha as peças internas (Módulos de Negócio/Controllers) e suas responsabilidades.

```mermaid
C4Component
    title Diagrama de Componentes: Aplicação Web Laravel

    Container_Boundary(app_boundary, "Aplicação Web Laravel") {
        Component(auth_module, "Módulo de Acesso", "Auth, UserAccess", "Autentica usuários e processa a matriz de permissões ACL para filtragem de dados.")
        Component(dash_module, "Módulo de Dashboard", "AdminController, DashboardController", "Gera KPIs, timeline de atividades e consolida relatórios CSV complexos via streaming.")
        Component(faturamento_module, "Módulo de Faturamento", "FaturamentoController", "Controla cobranças, atualiza saldos de serviços e integra-se ao PlugNotas para NFS-e.")
        Component(proposta_module, "Módulo de Propostas", "PropostasController", "Gera propostas comerciais, acompanha funil de vendas e converte leads em serviços automaticamente.")
        Component(os_module, "Módulo de Ordem de Serviço", "OrdemServicoController", "Atribui OS a prestadores, controla pagamentos parcelados e calcula reputação (mediana).")
        Component(reembolso_module, "Módulo de Reembolso", "ReembolsoController", "Empacota taxas operacionais da Celic em relatórios PDF e arquivos ZIP para o cliente.")
        Component(cliente_module, "Módulo Portal do Cliente", "ClienteController", "Restringe visibilidade de processos ao cliente final e orquestra menções sociais via Webhook.")
    }

    ContainerDb(db, "Banco de Dados", "MySQL", "Tabelas e esquemas")
    System_Ext(apis, "APIs Externas", "Integrações (n8n, PlugNotas)")

    Rel(auth_module, db, "Lê/Escreve roles e acessos")
    Rel(dash_module, db, "Faz queries massivas via cursor()")
    Rel(faturamento_module, db, "Atualiza status financeiro")
    Rel(faturamento_module, apis, "Chama PlugNotas")
    Rel(proposta_module, db, "Cria propostas, serviços e pendências")
    Rel(os_module, db, "Gerencia links OS-Serviço")
    Rel(reembolso_module, db, "Lê taxas e bloqueia reuso")
    Rel(cliente_module, auth_module, "Valida acesso")
    Rel(cliente_module, apis, "Dispara payload para n8n")
```
