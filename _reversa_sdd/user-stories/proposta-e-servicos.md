# User Story: Aprovação de Proposta Comercial

**Como** vendedor (Admin)
**Quero** aprovar uma proposta e converter seu escopo automaticamente
**Para** eliminar o trabalho manual de criação de serviços e pendências, e refletir a venda nas metas da empresa.

## Regras de Negócio e Contexto
- A meta comercial mensal exibida no Dashboard é de R$ 175.000,00.
- A aprovação cria Serviços (`Servico`), Financeiro (`ServicoFinanceiro`), Histórico (`Historico`) e Pendências Iniciais (`Pendencia`).
- O código da OS é gerado baseando-se nas iniciais do nome da Unidade.

## Cenários de Aceitação

### Cenário 1: Conversão Bem Sucedida de Múltiplos Serviços
**Dado** que uma proposta em status "Em análise" possui 2 serviços no escopo (Ex: "Licença A" R$ 1000 e "Consultoria B" R$ 2000)
**Quando** o usuário clica em "Aprovar Proposta"
**Então** o status da proposta deve mudar para "Aprovada"
**E** o sistema deve criar o "Serviço A" vinculado à unidade com status "andamento"
**E** o sistema deve criar o "Serviço B" vinculado à unidade com status "andamento"
**E** o "Serviço A" deve ter um `ServicoFinanceiro` com `valorTotal = 1000` e `valorAberto = 1000`
**E** uma pendência "Criar pendências!" deve ser atribuída a cada novo serviço
**E** o painel de vendas deve contabilizar + R$ 3000 na meta mensal.

### Cenário 2: Proposta já Aprovada (Prevenção de Duplicidade)
**Dado** que uma proposta já está com o status "Aprovada"
**Quando** o usuário tentar forçar a submissão do formulário de aprovação (via refresh ou API)
**Então** o sistema deve bloquear a clonagem dos serviços
**E** retornar uma mensagem de erro ou redirecionar informando que a proposta já foi convertida.

### Cenário 3: Geração do Código Sequencial (OS)
**Dado** que a unidade possui a Razão Social "McDonalds Centro"
**Quando** o primeiro serviço originado por proposta for criado para esta unidade
**Então** o código gerado via `getLastOs` deve ser "MC0001"
**E** ao criar o segundo serviço, o código deve ser "MC0002".
