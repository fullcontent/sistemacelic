# Fluxogramas: Proposta Comercial

## 1. Ciclo de Vida da Proposta
Fluxo de estados e transições de uma proposta desde a criação até o arquivamento.

```mermaid
graph TD
    Start((Início)) --> Create[Criar Proposta - Status 'Revisando']
    Create --> Edit[Editar Escopo e Valores]
    Edit --> Analysis[Enviar para Análise - Status 'Em análise']
    
    Analysis --> Approved{Aprovada?}
    Analysis --> Recusada[Recusada - Status 'Recusada']
    Analysis --> Revisar[Solicitar Revisão - Status 'Revisando']
    
    Revisar --> Edit
    
    Approved -- Sim --> AutoCreate[Geração Automática de Serviços]
    AutoCreate --> StatusApp[Status 'Aprovada']
    
    StatusApp --> Archive[Arquivar]
    Recusada --> Archive
    Archive --> End((Fim - Status 'Arquivada'))
```

## 2. Lógica de Conversão (Aprovação)
O que acontece internamente quando o botão "Aprovar" é acionado.

```mermaid
graph TD
    Approve[Ação: Aprovar Proposta] --> UpdateStatus[Setar Proposta como 'Aprovada']
    UpdateStatus --> SetDate[Registrar 'approved_at']
    
    SetDate --> Loop[Para cada PropostaServico...]
    Loop --> CreateServ[Criar Instância de Servico]
    CreateServ --> MapFields[Mapear: Empresa, Unidade, Escopo, Responsável]
    MapFields --> GenOS[Gerar Número de OS sequencial]
    GenOS --> SaveServ[Salvar Serviço]
    
    SaveServ --> Finance[Criar ServicoFinanceiro com ValorTotal]
    Finance --> History[Registrar Histórico de Cadastro]
    History --> Pendencia[Criar Pendência 'Criar pendências!']
    
    Pendencia --> Loop
    Loop -- Fim --> Success((Sucesso))
```

## 3. Geração de Número de OS
Algoritmo de `getLastOs`.

```mermaid
graph TD
    Start((Início)) --> GetUnit[Obter Unidade e Empresa]
    GetUnit --> Initials[Pegar iniciais da Razão Social - ex: 'CA']
    Initials --> Query[Buscar maior OS que começa com 'CA']
    
    Query --> Found{Encontrou?}
    Found -- Não --> Default[Retornar 'CA0001']
    Found -- Sim --> Increment[Extrair número, somar +1 e formatar com 4 zeros]
    Increment --> Result[Retornar 'CA0002', etc]
```
