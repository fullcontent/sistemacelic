# Regras de Negócio no Banco de Dados — SistemaCelic2

Como o SistemaCelic2 foi construído usando o framework Laravel com Eloquent ORM, o padrão arquitetural predominante é o **"Active Record"** com regras de negócio concentradas na camada de aplicação (Controllers e Models), e não diretamente no banco de dados.

## Triggers
Não foram identificadas Triggers complexas no banco de dados. As ações em cascata (como criar um `ServicoFinanceiro` ao aprovar uma `Proposta`) são gerenciadas inteiramente via PHP (`PropostasController@aprovar`).

## Stored Procedures e Funções
Não há uso de Stored Procedures (PL/pgSQL ou MySQL Routines) para regras de negócio. Algoritmos complexos, como o cálculo da mediana de reputação de prestadores (`calculateMedian`), são implementados em código PHP (`OrdemServicoController`).

## Views e Materialized Views
O sistema confia em agregações feitas em tempo real pelas queries do Eloquent ou manipulação de Collections em PHP (ex: geração de CSV via `cursor()` no `AdminController`), não possuindo Views SQL consolidadas para os relatórios principais.

## Constraints de Chave Estrangeira (FKs)
O banco utiliza extensivamente Foreign Keys para garantir a integridade referencial:
- Deleção em Cascata (`ON DELETE CASCADE`): Comum em tabelas dependentes como `UserAccess` em relação a `User`.
- Restrição de Deleção (`ON DELETE RESTRICT`): Esperado para entidades financeiras (não se pode deletar um `Servico` se ele possuir `Faturamento`).

## Check Constraints
A validação de limites (como "Valor Vinculado não pode ser maior que o Valor da OS") ocorre via validação de requests no Laravel (Regras de validação do formulário e checagens explícitas nos controllers), e não via `CHECK` constraints no nível do banco de dados.

---
**Conclusão do Data Master:** O banco de dados age estritamente como uma camada de persistência burra ("Dumb Database"). A inteligência, restrições e cálculos residem na API/Backend.
