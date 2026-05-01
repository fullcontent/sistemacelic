# Módulo: Proposta Comercial

## Visão Geral
Gerencia a criação de propostas (orçamentos) para clientes e orquestra a automação mais crítica do sistema: a conversão de propostas aprovadas na infraestrutura operacional completa (Serviços e Financeiro).

## Responsabilidades
- Registrar escopo comercial por Unidade/Empresa.
- Acompanhar status da negociação ("Revisando", "Em Análise", "Aprovada", "Recusada").
- Gerar os registros subsequentes na operação após o fechamento da venda.
- Fornecer métricas de funil de vendas em relação à meta (R$ 175.000).

## Interface
- **Modelos Principais:** `Proposta`, `PropostaServico`
- **Controladores:** `PropostasController`

## Regras de Negócio
- Uma proposta só pode ser convertida (Aprovada) uma vez. 🟢
- A aprovação cria em cascata instâncias de: `Servico`, `ServicoFinanceiro`, `Pendencia` ("Criar pendências!"), e logs em `Historico`. 🟢
- O número sequencial da OS (código formatado como `[iniciais da unidade]000X`) é gerado no ato da aprovação chamando `getLastOs()`. 🟢

## Fluxo Principal (Aprovação de Proposta)
1. Vendedor muda o status da Proposta para "Aprovada".
2. O controlador marca `approved_at` = agora.
3. Para cada `PropostaServico` vinculado, o sistema clona os dados e cria um novo `Servico`.
4. Uma OS string é formatada.
5. Um `ServicoFinanceiro` é criado associado ao novo serviço, contendo o valor negociado no escopo e status "aberto".
6. Uma `Pendencia` inicial é criada para notificar a equipe de operação que o projeto começou.

## Fluxos Alternativos
- **Proposta Recusada:** Altera o status e retira do funil de conversão ativa, armazenando o motivo em histórico.

## Dependências
- `Servico`, `ServicoFinanceiro`, `Pendencia`, `Historico` — Componentes instanciados automaticamente.
- `Unidade` — Fornece as iniciais usadas no código gerado.

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Desempenho | Criação em lote (N+1 queries possíveis na conversão) | `PropostasController@aprovar` | 🟡 |
| Integridade | A aprovação DEVE ser transacional (`DB::transaction`) | Decisão de Arquitetura | 🟢 |

## Cenários de Borda
1. **Falha no meio da conversão:**
   - Como a conversão foi reclassificada para uso obrigatório de `DB::transaction`, se o banco cair na metade da clonagem de serviços, ocorrerá o `rollback` garantindo que a proposta permaneça íntegra e não-aprovada. 🟢
2. **Duplicidade de Aprovação:**
   - O controlador verifica se o status já é "Aprovada" para bloquear a re-execução do fluxo de clonagem. 🟢

## Critérios de Aceitação

```gherkin
Dado que uma proposta em "Em análise" possui 3 serviços de escopo
Quando o usuário aciona a função de aprovação
Então a proposta muda de status
E o sistema deve criar exatamente 3 registros na tabela `servicos` vinculados a ela
E deve criar 3 registros em `servico_financeiros` com os respectivos valores em aberto

Dado que duas unidades têm o mesmo nome
Quando for aprovada uma proposta para cada uma
Então o gerador de `getLastOs` deve incrementar corretamente o identificador sem colisão
```

## Prioridade

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Automação de Aprovação | Must | É a ponte entre a venda e a operação. Sem ela, muito trabalho manual. |
| Workflow de Negociação | Must | Controle do Pipeline |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `app/Http/Controllers/PropostasController.php` | `PropostasController@aprovar` | 🟢 |
| `app/Http/Controllers/PropostasController.php` | `PropostasController@getLastOs` | 🟢 |
