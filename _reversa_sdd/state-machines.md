# Máquinas de Estado — SistemaCelic2

O sistema gerencia o ciclo de vida de diversas entidades através de campos de status e gatilhos lógicos.

## 1. Ciclo de Vida do Serviço
Entidade central que representa o fluxo de trabalho operacional.

```mermaid
stateDiagram-v2
    [*] --> andamento: Cadastro de Serviço
    andamento --> finalizado: Conclusão do Escopo
    finalizado --> andamento: Reabertura
    andamento --> arquivado: Cancelamento/Suspensão
    finalizado --> arquivado: Histórico
    arquivado --> [*]
```

- **andamento:** Estado inicial. Permite anexar arquivos, criar pendências e lançar taxas.
- **finalizado:** Bloqueia edições operacionais, mas permite faturamento e reembolso.
- **arquivado:** Oculto das listagens principais.

## 2. Fluxo da Proposta Comercial
Gerencia o processo de negociação.

```mermaid
stateDiagram-v2
    [*] --> Revisando: Criação (Vendedor)
    Revisando --> Em_Analise: Envio para Aprovação
    Em_Analise --> Revisando: Solicitação de Ajuste
    Em_Analise --> Aprovada: Aceite do Cliente
    Em_Analise --> Recusada: Perda da Oportunidade
    Aprovada --> Arquivada: Conclusão
    Recusada --> Arquivada: Perda Definitiva
    Arquivada --> [*]
```

- **Aprovada:** Gatilho para criação automática de Serviços (`PropostasController@aprovar`).

## 3. Estado Financeiro do Serviço
Controlado pela entidade `ServicoFinanceiro`.

```mermaid
stateDiagram-v2
    [*] --> aberto: Serviço Criado
    aberto --> parcial: Faturamento de Lote Parcial
    parcial --> faturado: Saldo Total Coberto
    aberto --> faturado: Faturamento Integral em Lote Único
    faturado --> [*]
```

- **Cálculo:** `valorAberto = valorTotal - valorFaturado`.
- **Transição:** Automática via `FaturamentoController@confirmar`.

## 4. Ordem de Serviço (OS) e Pagamentos
Fluxo de execução externa.

```mermaid
stateDiagram-v2
    [*] --> Aberto: Parcela Gerada
    Aberto --> Pago: Upload de Comprovante
    Pago --> [*]
```

- **Lógica:** A presença de um arquivo no campo `comprovante` força o status para `pago`.

## Escala de Confiança
- **Transições de Serviço:** 🟢 CONFIRMADO
- **Estados de Proposta:** 🟢 CONFIRMADO
- **Estados Financeiros:** 🟢 CONFIRMADO
