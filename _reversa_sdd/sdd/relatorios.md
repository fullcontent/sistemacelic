# Módulo: Relatórios

## Visão Geral
Fornece visões agregadas e extração de dados simplificada focada no status das pendências.

## Responsabilidades
- Listar pendências de serviços agrupadas por responsável ou status.
- Consolidar as datas limites (SLA).
- Permitir filtros rápidos que não necessitam do processamento massivo do Dashboard.

## Interface
- **Modelos Principais:** `Pendencia`, `Servico`
- **Controladores:** `RelatoriosController`

## Regras de Negócio
- Relatórios consideram a hierarquia de status de `Pendencia` ("pendente" vs "concluido"). 🟢
- Dependendo do tipo de relatório, a visão pode ser limitada aos registros pertencentes ao usuário autenticado ou globais se ele for admin de alto nível. 🟡

## Fluxos Alternativos
- Sem alternativas complexas. Consiste puramente de Queries e renderização de Views Blade ou download em formato estruturado.

## Dependências
- `Pendencia` e `Servico`.

## Requisitos Não Funcionais
N/A

## Critérios de Aceitação

```gherkin
Dado que o usuário acessa o relatório de pendências
Quando ele aplica o filtro por "Status = Pendente"
Então o sistema deve exibir apenas registros não concluídos, em ordem de prioridade de SLA (data de vencimento)
```

## Prioridade
| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Relatórios Básicos | Should | Ajuda o operacional no dia a dia, mas a exportação do Dashboard é mais completa. |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `app/Http/Controllers/RelatoriosController.php` | `RelatoriosController` | 🟢 |
