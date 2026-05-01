# Módulo: Ordem de Serviço (OS)

## Visão Geral
Gerencia a execução operacional através de prestadores terceirizados, conectando um `Servico` a uma Ordem de Serviço, acompanhando o cronograma e os pagamentos associados.

## Responsabilidades
- Atribuir serviços a prestadores externos via OS.
- Garantir que a soma financeira dos vínculos de uma OS não ultrapasse seu valor total.
- Controlar parcelamentos e baixas (pagamentos) de prestadores.
- Realizar a avaliação de prestadores baseada na mediana das notas.

## Interface
- **Modelos Principais:** `OrdemServico`, `OrdemServicoVinculo`, `OrdemServicoPagamento`
- **Controladores:** `OrdemServicoController`

## Regras de Negócio
- O sistema bloqueia a associação de um serviço a uma OS se a soma do valor dos vínculos exceder o `valorServico` da OS (com tolerância de R$ 0,01 devido a arredondamentos). 🟢
- Uma OS só transita para `pago` automaticamente se for feito o upload de um comprovante no pagamento da parcela. 🟢
- O rating de um prestador na conclusão da OS é calculado via **mediana** de todas as suas avaliações anteriores para evitar peso de "outliers". 🟢

## Fluxo Principal (Criação e Pagamento)
1. Analista cria OS definindo Prestador e Valor Total.
2. Analista vincula o `Servico` da Celic à OS.
3. Sistema valida se `Valor Vinculado <= Valor OS`.
4. Analista gera parcelas de pagamento.
5. Quando a parcela é paga, é feito o upload do comprovante, atualizando o status da parcela.

## Fluxos Alternativos
- **Excesso de Vínculo:** Se a validação falhar, o sistema exibe alerta e bloqueia a adição do vínculo de serviço à OS.

## Dependências
- `Servico` — Entidade de negócio primária.
- `Prestador` — Entidade responsável pela execução.

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Segurança | Controle de acesso: apenas usuários admin podem manipular OS | `middleware('admin')` | 🟢 |
| Integridade | Bloqueio de over-allocation financeiro | `OrdemServicoController@associarOsServico` | 🟢 |

## Cenários de Borda
1. **Diferença de centavos por arredondamento:** A validação permite um overflow de até R$ 0,01 para evitar travamentos devido a inconsistências de ponto flutuante no PHP. 🟢

## Critérios de Aceitação

```gherkin
Dado que uma OS tem valor de R$ 500,00
Quando o usuário tenta adicionar um serviço cujo valor imputado no vínculo seja R$ 500,02
Então o sistema deve bloquear a ação e exibir a mensagem "O valor associado aos serviços excede o valor da O.S."

Dado que uma OS está finalizada
Quando o usuário envia uma avaliação nota 2 para o prestador (que antes tinha notas 5, 5, 5)
Então o rating do prestador no banco deve permanecer 5 (Mediana de [2, 5, 5, 5])
```

## Prioridade

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Controle Financeiro de OS | Must | Protege o fluxo de caixa de pagamentos a terceiros |
| Upload de Comprovante | Must | Requisito de auditoria e compliance interno |
| Cálculo de Mediana | Should | Melhora a confiabilidade dos fornecedores |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `app/Http/Controllers/OrdemServicoController.php` | `OrdemServicoController` | 🟢 |
| `app/Models/OrdemServico.php` | `OrdemServico` | 🟢 |
