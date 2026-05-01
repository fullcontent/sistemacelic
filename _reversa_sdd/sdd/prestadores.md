# Módulo: Prestadores

## Visão Geral
Gerencia a base de dados de prestadores de serviço terceirizados, incluindo seus dados cadastrais, bancários, áreas de atuação e histórico de avaliações de qualidade.

## Responsabilidades
- Manter o cadastro de prestadores e seus dados de contato.
- Armazenar de forma segura as informações bancárias para pagamento das OS.
- Registrar e consolidar os comentários e avaliações geradas ao fim das Ordens de Serviço.

## Interface
- **Modelos Principais:** `Prestador`, `PrestadorComentario`
- **Controladores:** `PrestadorController`

## Regras de Negócio
- Prestadores devem possuir dados bancários vinculados para receber pagamentos via Ordem de Serviço. 🟡
- A área de atuação geográfica é gravada em formato JSON para suportar múltiplas cidades. 🟢

## Fluxo Principal (Avaliação de Prestador)
1. Prestador conclui o serviço designado.
2. Analista da Celic vai à tela da OS ou do Prestador e insere um comentário e uma nota (rating) de 1 a 5.
3. O `PrestadorController@rate` registra a nota atrelando o `user_id` e salva.
4. O módulo de OS recalcula a reputação geral.

## Fluxos Alternativos
- **Exclusão de Prestador:** Remove os vínculos (caso aplicável via restrições de FK no banco).

## Dependências
- `OrdemServico` — Consome os prestadores para atribuição de tarefas.
- `User` — Vinculado a quem fez o comentário de avaliação.

## Requisitos Não Funcionais

| Tipo | Requisito inferido | Evidência no código | Confiança |
|------|--------------------|---------------------|-----------|
| Persistência | Cidades de atuação armazenadas como JSON | Campos de modelo implícitos na view | 🟡 |

## Cenários de Borda
1. **Cadastro incompleto:** Tentar adicionar prestador sem os campos mínimos de identificação deve retornar erro do Validador.

## Critérios de Aceitação

```gherkin
Dado que o usuário está no perfil do prestador
Quando o usuário tenta submeter um comentário sem nota
Então o sistema deve exigir que a nota seja inserida
```

## Prioridade

| Requisito | MoSCoW | Justificativa |
|-----------|--------|---------------|
| Cadastro Base | Must | Requisito prévio para qualquer terceirização |
| Dados Bancários | Must | Necessário para contas a pagar |
| Comentários de Avaliação | Should | Histórico qualitativo do fornecedor |

## Rastreabilidade de Código

| Arquivo | Função / Classe | Cobertura |
|---------|-----------------|-----------|
| `app/Http/Controllers/PrestadorController.php` | `PrestadorController` | 🟢 |
| `app/Models/Prestador.php` | `Prestador` | 🟢 |
