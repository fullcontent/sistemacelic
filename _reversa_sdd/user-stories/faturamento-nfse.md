# User Story: Faturamento e Emissão de NFS-e

**Como** analista financeiro (Admin)
**Quero** faturar serviços de forma total ou parcial e emitir a NFS-e automaticamente
**Para** garantir o fluxo de caixa, manter o saldo dos serviços em dia e evitar retrabalho com o portal da prefeitura.

## Regras de Negócio e Contexto
- A API do PlugNotas é utilizada para a emissão das notas ficais.
- É permitido o Faturamento Parcial.
- O saldo do serviço atualiza o status de "aberto" para "parcial" ou "faturado".

## Cenários de Aceitação

### Cenário 1: Faturamento Parcial de Serviço Único
**Dado** que existe um Serviço de Licenciamento com Valor Total de R$ 5.000,00 e Status Financeiro "aberto"
**Quando** o usuário avança as etapas do Wizard de Faturamento
**E** define no Step 3 que o valor a faturar agora é de R$ 2.000,00
**E** clica em "Confirmar Faturamento" no Step 4
**Então** o sistema deve criar um lote de Faturamento no valor de R$ 2.000,00
**E** o sistema deve criar o vínculo `FaturamentoServico` com o valor proporcional
**E** o `ServicoFinanceiro` original deve mudar para `valorAberto = 3000`, `valorFaturado = 2000` e status `parcial`
**E** o payload deve ser enviado para a PlugNotas.

### Cenário 2: Faturamento Consolidado de Múltiplos Serviços
**Dado** que a Empresa "Acme Corp" tem 3 serviços finalizados com R$ 1.000,00 em aberto cada
**Quando** o usuário seleciona todos os 3 no Step 2
**E** não altera os valores sugeridos no Step 3 (faturamento total)
**Então** o sistema gerará um único registro de `Faturamento` no valor de R$ 3.000,00
**E** a nota fiscal emitida listará a consolidação dos 3 serviços.

### Cenário 3: Resiliência em Falha de Emissão (PlugNotas Indisponível)
**Dado** que a API da PlugNotas retorna um erro (ex: Timeout ou 500)
**Quando** o lote de faturamento é confirmado
**Então** as transações internas no banco de dados da Celic (criação de Faturamento, alteração de saldos) DEVEM ser concluídas com sucesso
**E** o lote deve ficar marcado no painel com o status "Erro na Emissão" ou "Pendente"
**E** o usuário deve conseguir clicar em "Reemitir" na lista de faturamentos posteriormente para tentar reenviar o payload à PlugNotas.
