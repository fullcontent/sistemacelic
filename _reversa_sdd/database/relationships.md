# Relacionamentos do Banco de Dados — SistemaCelic2

Detalhamento dos relacionamentos principais extraídos dos Modelos Eloquent.

## Relações N:M (Muitos-para-Muitos)

### User <-> Empresa / Unidade
- **Tabela de Junção:** `user_accesses`
- **Mapeamento:** Um usuário (`User`) pode acessar várias `Empresas` e `Unidades`. Uma `Empresa`/`Unidade` pode ser visualizada por vários usuários.
- **Implementação Eloquent:** `$this->belongsToMany('App\UserAccess','user_accesses','user_id','empresa_id')`

## Relações 1:N (Um-para-Muitos)

### Empresa -> Servico
- **Mapeamento:** Uma `Empresa` pode possuir dezenas de `Serviços` executados pela Celic.
- **Implementação Eloquent:** `Servico::empresa() -> belongsTo(Empresa)`

### Servico -> Historico
- **Mapeamento:** Um `Serviço` gera múltiplas entradas de `Histórico` (auditoria, comentários, menções).

### Servico -> Taxa
- **Mapeamento:** A execução de um `Serviço` pode incorrer em várias `Taxas` governamentais pagas.

### Proposta -> PropostaServico
- **Mapeamento:** Uma `Proposta` comercial contém N itens (`PropostaServico`) detalhando o escopo financeiro.

### OrdemServico -> OrdemServicoPagamento
- **Mapeamento:** Uma `OS` atribuída a um prestador pode ser paga em N parcelas (`OrdemServicoPagamento`).

### Prestador -> PrestadorComentario
- **Mapeamento:** Um `Prestador` acumula N avaliações/comentários (`PrestadorComentario`) baseados nos serviços prestados.

## Relações 1:1 (Um-para-Um)

### Servico <-> ServicoFinanceiro
- **Mapeamento:** Cada `Serviço` operacional tem exatamente um `ServicoFinanceiro` correspondente, que controla seu saldo, valor total e status de faturamento.

## Relações Hierárquicas (Self-Referencing)

### Servico -> subServicos
- **Mapeamento:** Um `Serviço` pode atuar como um "Projeto Pai" (`servicoPrincipal`), contendo múltiplos `subServicos`. A foreign key `servicoPrincipal` aponta para o próprio `id` da tabela `servicos`.

## Relacionamentos HasManyThrough (Através de)

### User -> Empresas (via UserAccess)
- **Implementação Eloquent:** O modelo `User` acessa todas as suas empresas permitidas atravessando a tabela pivô `UserAccess`.

### Servico -> Faturamento (via FaturamentoServico)
- **Implementação Eloquent:** O `Servico` descobre em qual lote de `Faturamento` está incluído atravessando a tabela pivô `FaturamentoServico`.
