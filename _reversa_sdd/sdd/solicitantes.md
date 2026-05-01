# Módulo: Solicitantes

## Visão Geral
Gerencia a tabela auxiliar de `Solicitante`, que armazena os dados das pessoas que requisitam os serviços dentro da empresa do cliente.

## Responsabilidades
- Cadastrar e gerenciar nomes, contatos e departamentos de quem originou a demanda do Serviço.
- Fornecer endpoints/views para listagem em selects/dropdowns no cadastro de Serviços e Propostas.

## Interface
- **Modelos Principais:** `Solicitante`
- **Controladores:** `SolicitantesController`

## Regras de Negócio
- Historicamente, o campo `solicitante` na tabela `servicos` era um `varchar` armazenando o nome direto. O sistema possui lógica implícita de retrocompatibilidade (ex: no `AdminController`): se `is_numeric()`, busca na tabela `Solicitante`, se for string, apenas exibe a string. 🟢

## Fluxos Alternativos
N/A

## Dependências
- Consumido amplamente pelas interfaces de `Servico` e listagens do `AdminController`.

## Requisitos Não Funcionais
N/A

## Cenários de Borda
1. **Retrocompatibilidade de Tipo de Dado:** Lidar com registros onde o campo na tabela pai é `String` legada ou `Inteiro` (FK) de forma transparente nas views de edição de serviço. 🟢

## Critérios de Aceitação

```gherkin
Dado que o usuário está criando um serviço
Quando ele clica no dropdown de "Solicitante"
Então a lista deve carregar os Solicitantes cadastrados

Dado que o sistema processa um serviço com o solicitante "Maria (Legado)"
Quando a view for renderizada
Então o sistema não deve tentar fazer `join` ou falhar, e sim exibir a string diretamente
```

## Prioridade
| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Cadastro Auxiliar | Must | Normalização de dados e melhoria na qualificação do cliente |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `app/Http/Controllers/SolicitantesController.php` | `SolicitantesController` | 🟢 |
| `app/Models/Solicitante.php` | `Solicitante` | 🟢 |
