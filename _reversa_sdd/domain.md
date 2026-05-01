# Domínio e Regras de Negócio — SistemaCelic2

Este documento descreve os conceitos fundamentais e as regras de negócio implícitas identificadas através da análise do código e histórico do projeto.

## Glossário de Domínio

| Termo | Definição |
| :--- | :--- |
| **Empresa** | Entidade jurídica principal (cliente da Celic). |
| **Unidade** | Filial ou ponto de serviço específico de uma Empresa. |
| **Serviço** | Unidade básica de trabalho operacional (ex: Licenciamento, Consultoria). |
| **LPU** | Lista de Preços Unitários. Define os tipos de serviço e valores base. |
| **Proposta** | Documento comercial que precede a execução dos serviços. |
| **OS (Ordem de Serviço)** | Atribuição de um Serviço (ou parte dele) a um Prestador externo. |
| **Taxa** | Despesa governamental ou operacional paga pela Celic. |
| **Reembolso** | Processo de cobrança das Taxas pagas pela Celic ao cliente final. |
| **Faturamento** | Processo de cobrança dos Serviços prestados pela Celic. |
| **Dados de Castro** | Entidade do grupo Celic que emite a cobrança/nota fiscal. |

## Regras de Negócio Implícitas

### 1. Governança de Serviços
- **Múltiplos Responsáveis:** Um serviço não possui apenas um dono. Ele pode ter um `Responsável`, um `Coresponsável` e até dois `Analistas`. Qualquer um destes quatro tem visibilidade e autoridade sobre o item. 🟢
- **Histórico como Estado:** O sistema utiliza o registro de log em `historicos` para marcar marcos temporais (como a data de finalização), buscando por strings específicas como `"Alterou situacao para \"finalizado\""`. 🟢
- **Hierarquia de Serviços:** Existe suporte para `subServicos` vinculados a um `servicoPrincipal`, permitindo que um projeto complexo seja decomposto. 🟢

### 2. Ciclo Financeiro
- **Soma de Vínculos (OS):** Uma Ordem de Serviço não pode ter vínculos de serviço que somem mais do que o seu valor total bruto (tolerância de R$ 0,01). 🟢
- **Faturamento Parcial:** Serviços podem ser faturados em lotes diferentes, e o sistema mantém o saldo (`valorAberto`) atualizado em `servico_financeiros`. 🟢
- **Garantia de Reembolso:** Taxas marcadas com `reembolso="sim"` são bloqueadas para novos lotes de reembolso assim que são incluídas em um processo (vínculo via `reembolso_taxas`). 🟢

### 3. Conversão Comercial
- **Automação de Checkout:** A aprovação de uma Proposta é o gatilho principal para a criação de toda a infraestrutura operacional de um projeto (Serviço, Financeiro e Pendência de Início). 🟢
- **Meta de Vendas:** A meta global de vendas (propostas aprovadas) é fixada em **R$ 175.000,00** por mês (ref: Commit #386). 🟢

### 4. Gestão de Qualidade
- **Reputação via Mediana:** A nota de um prestador não é uma média aritmética, mas sim a **mediana** de suas avaliações. Isso protege bons prestadores de avaliações punitivas isoladas (outliers). 🟢

### 5. Lógicas de Relatório e Autocorreção
- **Autocorreção Financeira (Self-Healing):** O motor de geração de CSVs (`completoCSV`) identifica serviços sem registro financeiro e os cria automaticamente com valores zerados para garantir a integridade do relatório. 🟢
- **Inferência de Etapa:** O estágio do processo ("Etapa") é inferido dinamicamente com base na presença de anexos:
    - Sem protocolo ou laudo: `"Em elaboração"`.
    - Com ambos: `"1° Análise"`. 🟢
- **Mapeamento de Terminologia:** O sistema traduz chaves técnicas para termos de negócio em relatórios (ex: `usuario` vira `Castro`, `op` vira `Órgão`). 🟢

## Escala de Confiança
- **Terminologia:** 🟢 CONFIRMADO
- **Cálculos Financeiros:** 🟢 CONFIRMADO
- **Processos de Conversão:** 🟢 CONFIRMADO
- **Lógicas de Filtro Geográfico:** 🟡 INFERIDO (baseado em campos de UF/Cidade e API Google)
