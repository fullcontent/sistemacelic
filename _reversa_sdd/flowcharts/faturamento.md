# Fluxograma do Módulo Faturamento — SistemaCelic2

## Processo de Faturamento (Wizard 4 Passos)

```mermaid
graph TD
    A[Início: Step 1] --> B[Selecionar Empresa e Propostas]
    B --> C[Step 2: Filtrar Serviços]
    C --> D{Serviços Encontrados?}
    D -- Não --> C
    D -- Sim --> E[Selecionar Serviços para Faturar]
    E --> F[Step 3: Revisão e Dados de Faturamento]
    F --> G[Selecionar Dados da Emissora - DadosCastro]
    G --> H[Definir Descrição e Observações]
    H --> I[Step 4: Processamento Final]
    I --> J[Criar Registro Faturamento]
    J --> K[Criar Vínculos FaturamentoServico]
    K --> L[Atualizar ServicoFinanceiro - valorAberto/Faturado]
    L --> M[Fim: Exibir Resumo]
```

## Lógica de Atualização Financeira

```mermaid
graph TD
    A[Recebe Valor a Faturar] --> B{Já possui faturamento?}
    
    B -- Não --> C{Valor == Total?}
    C -- Sim --> D[Status: Faturado | Aberto: 0]
    C -- Não --> E[Status: Parcial | Aberto: Total - Faturar]
    
    B -- Sim --> F{Novo Total == Total?}
    F -- Sim --> G[Status: Faturado | Aberto: 0]
    F -- Não --> H[Status: Parcial | Aberto: Aberto Anterior - Faturar]
```
