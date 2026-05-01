# Dicionário de Dados — SistemaCelic2

## Entidade: User
Armazena os dados dos usuários do sistema e define seu nível de acesso principal.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint | Sim | - | Chave primária | 🟢 |
| `name` | string | Sim | - | Nome completo do usuário | 🟢 |
| `email` | string | Sim | - | E-mail (login) | 🟢 |
| `password` | string | Sim | - | Hash da senha | 🟢 |
| `privileges` | string | Sim | - | Nível de acesso: `admin` ou `cliente` | 🟢 |

## Entidade: UserAccess
Controla o acesso granular de usuários a empresas e unidades específicas.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint | Sim | - | Chave primária | 🟢 |
| `user_id` | bigint | Sim | - | FK para `users.id` | 🟢 |
| `empresa_id` | bigint | Não | NULL | FK para `empresas.id` | 🟢 |
| `unidade_id` | bigint | Não | NULL | FK para `unidades.id` | 🟢 |

## Entidade: Historico
Registra interações e alterações em serviços para auditoria e timeline.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint | Sim | - | Chave primária | 🟢 |
| `servico_id` | bigint | Sim | - | FK para `servicos.id` | 🟢 |
| `user_id` | bigint | Não | NULL | FK para `users.id` | 🟢 |
| `observacoes` | text | Não | NULL | Descrição da alteração ou comentário | 🟢 |

## Entidade: Faturamento
Agrupamento de serviços faturados para uma empresa sob uma mesma nota ou lote.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint | Sim | - | Chave primária | 🟢 |
| `empresa_id` | bigint | Sim | - | FK para `empresas.id` | 🟢 |
| `valorTotal` | decimal | Sim | 0 | Valor total do lote faturado | 🟢 |
| `nf` | string | Não | NULL | Número(s) da Nota Fiscal | 🟢 |

## Entidade: ServicoFinanceiro
Estado financeiro consolidado de um serviço individual.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint | Sim | - | Chave primária | 🟢 |
| `servico_id` | bigint | Sim | - | FK para `servicos.id` | 🟢 |
| `valorTotal` | decimal | Sim | 0 | Valor contratado do serviço | 🟢 |
| `valorFaturado` | decimal | Sim | 0 | Soma de todos os faturamentos já realizados | 🟢 |
| `valorAberto` | decimal | Sim | 0 | Valor restante a faturar | 🟢 |
| `status` | string | Sim | `aberto` | Status: `aberto`, `parcial`, `faturado` | 🟢 |

## Entidade: OrdemServico
Cabeçalho da ordem de serviço atribuída a um prestador.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | integer | Sim | - | Chave primária | 🟢 |
| `prestador_id` | integer | Não | NULL | FK para `prestadores.id` | 🟢 |
| `servico_id` | integer | Não | NULL | FK para `servicos.id` (Serviço Principal) | 🟢 |
| `valorServico` | decimal | Não | NULL | Valor total bruto da OS | 🟢 |
| `situacao` | string | Não | NULL | Status geral da OS | 🟢 |

## Entidade: OrdemServicoPagamento
Controle de parcelas e liquidação financeira de uma OS.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint | Sim | - | Chave primária | 🟢 |
| `ordemServico_id` | bigint | Sim | - | FK para `ordem_servicos.id` | 🟢 |
| `valor` | decimal | Não | NULL | Valor da parcela específica | 🟢 |
| `dataPagamento` | date | Não | NULL | Data em que o pagamento foi realizado | 🟢 |
| `dataVencimento` | date | Não | NULL | Data prevista para o pagamento | 🟢 |
| `comprovante` | string | Não | NULL | Caminho do arquivo de comprovante | 🟢 |
| `situacao` | string | Não | NULL | Status da parcela: `pago` ou `aberto` | 🟢 |

## Entidade: Prestador
Cadastro de prestadores de serviço.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | integer | Sim | - | Chave primária | 🟢 |
| `nome` | string | Sim | - | Nome/Razão Social | 🟢 |
| `cnpj` | string | Não | NULL | CNPJ ou CPF | 🟢 |
| `cidadeAtuacao` | json | Não | NULL | Cidades em formato JSON | 🟢 |
| `chavePix` | string | Não | NULL | Chave PIX para pagamento | 🟢 |

## Entidade: PrestadorComentario
Avaliações e comentários sobre o prestador.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | integer | Sim | - | Chave primária | 🟢 |
| `prestador_id` | integer | Sim | - | FK para `prestadors.id` | 🟢 |
| `ordemServico_id` | bigint | Não | NULL | FK para `ordem_servicos.id` | 🟢 |
| `user_id` | integer | Sim | - | FK para `users.id` (Avaliador) | 🟢 |
| `rating` | integer | Sim | - | Nota de 1 a 5 | 🟢 |
| `comentario` | text | Não | NULL | Observações textuais | 🟢 |

## Entidade: Proposta
Propostas comerciais geradas para clientes.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint | Sim | - | Chave primária | 🟢 |
| `empresa_id` | bigint | Sim | - | FK para `empresas.id` | 🟢 |
| `unidade_id` | bigint | Sim | - | FK para `unidades.id` | 🟢 |
| `status` | string | Sim | `Revisando` | `Revisando`, `Em análise`, `Aprovada`, `Recusada`, `Arquivada` | 🟢 |
| `created_by` | integer | Sim | - | FK para `users.id` (Vendedor) | 🟢 |
| `approved_at` | datetime | Não | NULL | Data de aprovação | 🟢 |

## Entidade: PropostaServico
Itens de serviço vinculados a uma proposta.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint | Sim | - | Chave primária | 🟢 |
| `proposta_id` | bigint | Sim | - | FK para `propostas.id` | 🟢 |
| `servico` | string | Sim | - | Nome do serviço | 🟢 |
| `valor` | decimal | Sim | - | Valor do item | 🟢 |
| `escopo` | text | Não | NULL | Descrição detalhada | 🟢 |

## Entidade: Reembolso
Lote de reembolsos de taxas para uma empresa.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint | Sim | - | Chave primária (Exposta como ID+1000) | 🟢 |
| `empresa_id` | bigint | Sim | - | FK para `empresas.id` | 🟢 |
| `valorTotal` | decimal | Sim | - | Soma das taxas do lote | 🟢 |
| `nome` | string | Não | NULL | Descrição/Referência do reembolso | 🟢 |

## Entidade: Solicitante
Cadastro de pessoas que solicitam serviços.

| Campo | Tipo | Requerido | Padrão | Descrição | Confiança |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | integer | Sim | - | Chave primária | 🟢 |
| `nome` | string | Sim | - | Nome do solicitante | 🟢 |
| `email` | string | Não | NULL | E-mail de contato | 🟢 |
