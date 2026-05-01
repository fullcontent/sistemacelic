# Módulo: Faturamento

## Visão Geral
Gerencia o ciclo de cobrança dos serviços prestados, permitindo faturamentos parciais e emissão de Notas Fiscais (NFS-e) através da integração com o PlugNotas.

## Responsabilidades
- Conduzir o usuário pelo Wizard de Faturamento (4 etapas).
- Consolidar serviços de uma mesma empresa em um único lote (`Faturamento`).
- Gerenciar o saldo financeiro individual de cada serviço (`ServicoFinanceiro`).
- Enviar payload de emissão de NF para o sistema PlugNotas.

## Interface
- **Modelos Principais:** `Faturamento`, `FaturamentoServico`, `ServicoFinanceiro`, `DadosCastro`
- **Controladores:** `FaturamentoController`
- **APIs Externas:** PlugNotas API

## Regras de Negócio
- O sistema permite **faturamento parcial**; um serviço pode ter seu valor dividido em múltiplos lotes de faturamento. 🟢
- O status de `ServicoFinanceiro` transita de `aberto` -> `parcial` -> `faturado` de acordo com a soma dos valores faturados em relação ao `valorTotal`. 🟢
- Os serviços listados para faturamento devem estar finalizados (com flexibilidade no Step 2). 🟢

## Fluxo Principal (Wizard de Faturamento)
1. **Step 1:** Seleciona a Empresa.
2. **Step 2:** Filtra serviços por período e situação.
3. **Step 3:** Seleciona quais serviços entrarão no lote e o valor a faturar de cada um.
4. **Step 4:** Confirma o lote, atualiza os saldos (`valorAberto`, `valorFaturado`) e gera o registro de `Faturamento`.

## Fluxos Alternativos
- **Reemissão de NF:** Se houver erro na integração com a PlugNotas, o sistema permite reenviar o payload a partir da listagem de faturamentos.

## Dependências
- `Empresa` e `Servico` — Base do faturamento.
- `PlugNotas` — Para efetivação fiscal.

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Disponibilidade | Timeout customizado em chamadas HTTP | `FaturamentoController` (via Guzzle/CURL) | 🟢 |
| Resiliência | Possibilidade de reemissão manual de NF | View de detalhe do faturamento | 🟢 |

## Cenários de Borda
1. **Faturamento excedendo o valor total:** O sistema DEVE bloquear no backend e interface faturamentos maiores que o `valorAberto`. Cobranças extras por juros/multa não são aceitas na mesma nota fiscal do serviço original. 🟢
2. **Falha na API PlugNotas:** O lote de faturamento é criado internamente, mas a NF fica pendente de reemissão. 🟢

## Critérios de Aceitação

```gherkin
Dado que o serviço X possui Valor Total de R$ 1000 e Valor Faturado de R$ 0
Quando o usuário inclui este serviço em um lote de faturamento com valor de R$ 400
Então o status financeiro do serviço deve mudar para `parcial`
E o Valor Aberto deve ser R$ 600

Dado que a API do PlugNotas está fora do ar
Quando o lote de faturamento é confirmado
Então o sistema deve registrar o faturamento internamente e exibir um alerta de falha na emissão da NF
```

## Prioridade

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Controle de Saldo Financeiro | Must | Evita cobrança duplicada ou perda de receita |
| Integração NFS-e | Must | Automação fiscal vital |
| Faturamento Parcial | Should | Importante para contratos longos |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `app/Http/Controllers/FaturamentoController.php` | `FaturamentoController` | 🟢 |
| `app/Models/ServicoFinanceiro.php` | `ServicoFinanceiro` | 🟢 |
